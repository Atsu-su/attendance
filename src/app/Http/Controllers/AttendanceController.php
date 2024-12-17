<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest as ModelsStampCorrectionRequest;
use Exception;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // ユーザ編
    // 時刻表示画面
    public function index()
    {
        // ----------------------------------
        // テスト用の日付
        $now = now()->addDays(-17);
        // $now = now();
        // ----------------------------------

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $now->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => auth()->id(),
                'date' => $now->format('Y-m-d'),
                'status' => Attendance::BF_WORK
            ]);
        }

        return view('attendance_register', compact('now', 'attendance'));
    }

    // viewのjs-hiddenのクラスを状態によって付け替えて表示を変える
    // 注意すべきはjs-hiddenを取り除かれて他のアクションが取られること
    // テーブルや勤怠のステータスに応じてAPI側で適切な処理かどうかの
    // 判定を行うこと
    // view側でも例えば、勤怠が開始した後は勤怠開始ボタンは表示しない
    // ようにする（二重は不要かも）

    // 出勤のAPI
    public function startWorkApi()
    {
        // 出勤処理がされていないか確認

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
