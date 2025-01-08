<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\StampCorrectionRequest as RequestsStampCorrectionRequest;
use App\Models\RequestBreakTime;
use App\Traits\CheckTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StampCorrectionRequestController extends Controller
{
    use CheckTrait;

    public function getRequestData($id)
    {
        $request = StampCorrectionRequest::with('attendance', 'user', 'requestBreakTimes')->find($id);
        $request->request_date = $request->toJapaneseDate($request->request_date);
        $request->attendance->date = $request->attendance->toJapaneseDate($request->attendance->date);
        $request->start_time = $request->timeFormatConvert($request->start_time);
        $request->end_time = $request->timeFormatConvert($request->end_time);

        $requestBreakTimes = $request
            ->requestBreakTimes
            ->map(function ($requestBreakTime) {
                $requestBreakTime->start_time = $requestBreakTime->timeFormatConvert($requestBreakTime->start_time);
                $requestBreakTime->end_time = $requestBreakTime->timeFormatConvert($requestBreakTime->end_time);
                return $requestBreakTime;
            });

        return ['request' => $request, 'requestBreakTimes' => $requestBreakTimes];
    }

    /**
     * （共通）申請一覧表示
     */
    public function index()
    {
        $query = StampCorrectionRequest::with('user')
            // このjoinはorderByのために必要
            ->join('attendances', 'stamp_correction_requests.attendance_id', '=', 'attendances.id')
            ->orderBy('attendances.date', 'asc');

        if (auth('admin')->check()) {
            $query->orderBy('stamp_correction_requests.user_id', 'asc');
        } else {
            $user =  auth()->user();
            $query->where('stamp_correction_requests.user_id', $user->id);
        }

        $requests = $query
            // 明示的にselectしないとattendances.idが取得されてしまう
            ->select('stamp_correction_requests.*', 'attendances.date',)
            ->get();

        $formattedRequests = $requests
            ->map(function ($request) {
                $request->request_date = $request->dateFormatConvert($request->request_date);
                $request->date = $request->dateFormatConvert($request->date);
                return $request;
            });

        return view('common_stamp_correction_request_list', compact('formattedRequests'));
    }

    /**
     * （共通）申請内容詳細表示
     * @param int $stamp_correction_request
     */
    public function show($stamp_correction_request)
    {
        $requestData = $this->getRequestData($stamp_correction_request);

        return view('common_stamp_correction_request_detail', $requestData);
    }

    /**
     * （共通）申請内容の登録
     * @param StampCorrectionRequest $request, $id
     * $id: attendancesテーブルのid
     */
    public function store(RequestsStampCorrectionRequest $request, $id)
    {
        // 直接POSTされたときのための対策
        $admin = auth('admin')->user();
        // adminガードの場合はアクセス可能
        if (!$admin) {
            // webガードの場合は勤怠情報の所有者か確認
            $user = auth('web')->user();
            $this->checkAttendanceOwner($id, $user);
        }

        $attendance = Attendance::find($id);
        $stampCorrectionRequest = StampCorrectionRequest::where('attendance_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        // 申請が存在する AND 申請が承認されていない場合は申請不可
        if ($stampCorrectionRequest && $stampCorrectionRequest->is_approved == 0) {
            //申請できない
            if (auth('admin')->check()) {
                return redirect()->route('admin-attendance.show', $id);
            }
            return redirect()->route('attendance.show', $id);
        }

        try{
            DB::transaction(function () use ($request, $id, $attendance) {
                // 申請情報の登録
                $stampCorrectionRequest =  StampCorrectionRequest::create([
                    'attendance_id' => $id,
                    'user_id' => $attendance->user_id,
                    'is_approved' => false,
                    'request_date' => now()->format('Y-m-d'),
                    'start_time' => $request->input('start_time'),
                    'end_time' => $request->input('end_time'),
                    'remarks' => $request->input('remarks'),
                ]);

                // 休憩時間の登録
                $breakStartTime = $request->input('break_start_time');
                $breakEndTime = $request->input('break_end_time');

                foreach ($breakStartTime as $key => $startTime) {
                    $endTime = $breakEndTime[$key];
                    RequestBreakTime::create([
                        'stamp_correction_request_id' => $stampCorrectionRequest->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                }
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            if ($admin) {
                return redirect()->route('admin-attendance.show', $id);
            }
            return redirect()->route('attendance.show', $id);
        }

        if ($admin) {
            return redirect()->route('admin-stamp-correction-request.index');
        }
        return redirect()->route('stamp-correction-request.index');
    }
}
