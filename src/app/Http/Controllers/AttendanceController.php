<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
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
        $attendance = Attendance::with('user')->find($id);

        $attendance->date = $this->dateFormatConvert($attendance->date);
        $attendance->start_time = $this->timeFormatConvert($attendance->start_time);
        $attendance->end_time = $this->timeFormatConvert($attendance->end_time);

        // break_timeを追加
        $attendance->break_time = $this->diffTime($attendance->break_start_time, $attendance->break_end_time);

        return view('attendance_detail', compact('attendance'));
    }

    public function store(StampCorrectionRequest $request)
    {
        // return redirect()->route('attendance.edit', ['id' => $attendance->id]);
    }
}
