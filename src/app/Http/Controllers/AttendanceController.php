<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest as ModelsStampCorrectionRequest;
use App\Traits\DateTimeFormatTrait;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use DateTimeFormatTrait;

    // ユーザ編
    //
    // 出勤のAPI
    function startWork()
    {
        // 日付・時刻の取得
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i');

        // attendancesテーブルへの登録
        try {
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'attendance_start_time' => $time,
        ]);

        // レスポンスの返却（json）
        // return response()->json(['likeIt' => $likeIt]);
        } catch (Exception $e) {
            return response()->json(['error' => '出勤登録に失敗しました'], 500);
        }
    }

    // 休憩開始のAPI
    // 休憩終了のAPI
    // 退勤のAPI
    // 勤怠一覧の表示
    // 勤怠詳細の表示

    // 管理者編
    //
    // 勤怠一覧の表示
    // 勤怠詳細の表示
    // スタッフ別勤怠一覧の表示

    public function edit($id)
    {
        // 申請は複数回可能なため最後の申請を取得
        $request = ModelsStampCorrectionRequest::where('attendance_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        // 申請が存在しないか、申請が承認されている場合に編集可能
        if (!$request || $request->is_approved == 1) {
            $isApplicable = true;

            $attendance = Attendance::with('user')->find($id);
            $attendance->date = $this->dateFormatConvert($attendance->date);
            $attendance->start_time = $this->timeFormatConvert($attendance->start_time);
            $attendance->end_time = $this->timeFormatConvert($attendance->end_time);
            $attendance->break_start_time = $this->timeFormatConvert($attendance->break_start_time);
            $attendance->break_end_time = $this->timeFormatConvert($attendance->break_end_time);

            return view('attendance_detail', compact('attendance', 'isApplicable'));
        } else {
            $isApplicable = false;

            $request->request_date = $this->dateFormatConvert($request->request_date);
            $request->start_time = $this->timeFormatConvert($request->start_time);
            $request->end_time = $this->timeFormatConvert($request->end_time);
            $request->break_start_time = $this->timeFormatConvert($request->break_start_time);
            $request->break_end_time = $this->timeFormatConvert($request->break_end_time);

            return view('attendance_detail', compact('request', 'isApplicable'));
        }




    }

    public function store(StampCorrectionRequest $request)
    {
        // return redirect()->route('attendance.edit', ['id' => $attendance->id]);
    }
}
