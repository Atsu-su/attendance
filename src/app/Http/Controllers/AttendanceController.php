<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest as ModelsStampCorrectionRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    // ----------------------------------
    // テスト用の日付
    const DAY = 13;
    private $now;

    public function __construct()
    {
        $this->now = now()->addDays(self::DAY);
    }
    // ----------------------------------

    public function index()
    {
        return redirect()->route('login');
    }

    // 勤怠登録トップ画面
    public function register()
    {
        $user = auth()->user();

        // ----------------------------------
        // テスト用の日付
        $now = $this->now;
        // $now = now();
        // ----------------------------------

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $now->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            try {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $now->format('Y-m-d'),
                    'status' => Attendance::BF_WORK
                ]);
            } catch (Exception $e) {

                // ----------------------------------
                // 修正予定
                //
                Log::error($e->getMessage());
                return '<p>勤怠管理システムが使用できません</p>';
                // ----------------------------------

            }
        }

        return view('attendance_register', compact('now', 'attendance'));
    }

    // 出勤のAPI
    public function startWorkApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        // ----------------------------------
        // テスト用の日付
        $now = $this->now;
        // $now = now();
        // ----------------------------------
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // attendancesテーブルへの登録
        try {
            Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->update([
                    'status' => Attendance::ON_DUTY,
                    'start_time' => $time,
                ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    // 退勤のAPI
    public function endWorkApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        // ----------------------------------
        // テスト用の日付
        $now = $this->now;
        // $now = now();
        // ----------------------------------
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // attendancesテーブルへの登録
        try {
            Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->update([
                    'status' => Attendance::OFF_DUTY,
                    'end_time' => $time,
                ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    // 休憩開始のAPI
    public function startBreakApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        // ----------------------------------
        // テスト用の日付
        $now = $this->now;
        // $now = now();
        // ----------------------------------
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->where('status', Attendance::ON_DUTY)
            ->first();

        if (!$attendance) {
            Log::error('更新対象の勤怠情報が存在しません');
            return response()->json(null, 400);
        }

        // break_timesテーブルへの登録とattendancesテーブルの更新
        try {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => $time,
            ]);

            $attendance->update([
                'status' => Attendance::BREAK
            ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    // 休憩終了のAPI
    public function endBreakApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        // ----------------------------------
        // テスト用の日付
        $now = $this->now;
        // $now = now();
        // ----------------------------------
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->where('status', Attendance::BREAK)
            ->first();

        if (!$attendance) {
            Log::error('更新対象の勤怠情報が存在しません');
            return response()->json(null, 400);
        }

        // break_timesテーブルとattendancesテーブルの更新
        try {
            BreakTime::where('attendance_id', $attendance->id)
                ->orderBy('created_at', 'desc')
                ->first()
                ->update([
                    'end_time' => $time,
                ]);

            $attendance->update([
                'status' => Attendance::ON_DUTY
            ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    // 勤怠一覧の表示
    public function showList($year, $month)
    {
        $user = auth()->user();

        $date = Carbon::create($year, $month);
        $startOfMonth = $date->startOfMonth();
        $endOfMonth = $date->endOfMonth();
        $days = $startOfMonth->diff($endOfMonth)->days + 1;

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')])
            ->get()
            ->map(function ($attendance) {
                $attendance->date = $attendance->dateFormatConvert($attendance->date);
                $attendance->start_time = $attendance->timeFormatConvert($attendance->start_time);
                $attendance->end_time = $attendance->timeFormatConvert($attendance->end_time);

                $totalBreakTime = 0;
                $breakTimes = $attendance->breakTimes;

                // この値が空
                dd($breakTimes);

                foreach ($breakTimes as $breakTime) {
                    $startTime = Carbon::createFromFormat('H:i', $breakTime->timeFormatConvert($breakTime->start_time));
                    $endTime = Carbon::createFromFormat('H:i', $breakTime->timeFormatConvert($breakTime->end_time));
                    $totalBreakTime += $startTime->diffInMinutes($endTime);
                }

                $attendance->total_break_time = $totalBreakTime;

                return $attendance;
            });

    }

    // 勤怠詳細の表示

    // 管理者編
    //
    // 勤怠一覧の表示
    // 勤怠詳細の表示
    // スタッフ別勤怠一覧の表示

    // 申請用の画面表示
    public function show($id)
    {
        // 申請は複数回可能なため最後の申請を取得
        $request = ModelsStampCorrectionRequest::with('requestBreakTimes')
            ->where('attendance_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        // 申請が存在しないか、申請が承認されている場合に編集可能
        if (!$request || $request->is_approved == 1) {
            $isApplicable = true;

            // 勤怠情報の取得
            $attendance = Attendance::with('user')->find($id);
            $attendance->date = $attendance->dateFormatConvert($attendance->date);
            $attendance->start_time = $attendance->timeFormatConvert($attendance->start_time);
            $attendance->end_time = $attendance->timeFormatConvert($attendance->end_time);
            $attendance->break_start_time = $attendance->timeFormatConvert($attendance->break_start_time);
            $attendance->break_end_time = $attendance->timeFormatConvert($attendance->break_end_time);

            // 休憩時間の取得
            $breakTimes = BreakTime::where('attendance_id', $attendance->id)
                ->get()
                ->map(function ($breakTime) {
                    $breakTime->start_time = $breakTime->timeFormatConvert($breakTime->start_time);
                    $breakTime->end_time = $breakTime->timeFormatConvert($breakTime->end_time);
                    return $breakTime;
                });

            return view('attendance_detail', compact('attendance', 'breakTimes', 'isApplicable'));
        } else {
            $isApplicable = false;

            $request->date = $request->dateFormatConvert($request->attendance->date);
            $request->start_time = $request->timeFormatConvert($request->start_time);
            $request->end_time = $request->timeFormatConvert($request->end_time);

            $requestBreakTimes = $request
                ->requestBreakTimes
                ->map(function ($requestBreakTime) {
                    $requestBreakTime->start_time = $requestBreakTime->timeFormatConvert($requestBreakTime->start_time);
                    $requestBreakTime->end_time = $requestBreakTime->timeFormatConvert($requestBreakTime->end_time);
                    return $requestBreakTime;
                });

            return view('attendance_detail', compact('request', 'requestBreakTimes', 'isApplicable'));
        }




    }

    public function store(StampCorrectionRequest $request)
    {
        // return redirect()->route('attendance.edit', ['id' => $attendance->id]);
    }
}
