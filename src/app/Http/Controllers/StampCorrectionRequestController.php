<?php

namespace App\Http\Controllers;

use App\Models\RequestBreakTime;
use App\Models\StampCorrectionRequest;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    /**
     * （管理者）申請一覧表示
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
     * （管理者）申請内容詳細表示
     * @param int $id
     */
    public function show($id)
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


        if (auth('admin')->check()) {
            return view('admin_stamp_correction_request_detail_approve', compact('request', 'requestBreakTimes'));
        }

        return view('user_stamp_correction_request_detail', compact('request', 'requestBreakTimes'));
    }
    /**
     * （管理者）申請承認画面表示
     */
    public function approve($id)
    {
        $request = StampCorrectionRequest::with('attendance', 'user', 'requestBreakTimes')->find($id);
        // ここから
    }
}
