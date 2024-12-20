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
    /**
     * 分のフォーマット変換
     * @param int $mins
     * @return string
     *
     * （注意）
     * 引数がnullの場合はnullを返す
     * 引数が0の場合は00:00を返す
     *
     * 例）126分 → 02:06
     */
    public function minutesFormatConvert($mins)
    {
        if ($mins === null) return null;

        $hours = floor($mins / 60);
        $minutes = $mins % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * 勤怠情報の所有者かどうかを確認
     * @param $id
     * @return Illuminate\Http\Response
     * @return bool
     * $id: attendancesテーブルのid
     */
    public function checkAttendanceOwner($id)
    {
        $attendance = Attendance::where('id', $id)->first();
        if (!$attendance) {
            return abort(404);
        } else if ($attendance->user_id !== auth()->id()) {
            return abort(403);
        }
        return true;
    }

    // ----------------------------------
    // テスト用の日付
    const DAY = 1;
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

    /**
     * 勤怠一覧の表示
     * @param int $year, $month
     */
    public function showList($year, $month)
    {
        $user = auth()->user();

        // ----------------------------------
        //
        // 日付・時間の取得
        //
        // ----------------------------------
        // 基準となる日付（これを直接メソッドチェーンで使用しないこと）
        $date = Carbon::create($year, $month, 1);
        $date->settings(['monthOverflow' => false]);

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $days = $startOfMonth->diff($endOfMonth)->days + 1;

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')])
            ->get()
            ->map(function ($attendance) {

                // ===================================================
                // ＜重要＞
                // 時間が登録されていない場合は計算せず、結果はnullとする
                // ===================================================

                // -------------------
                // 表記変更
                // -------------------
                $attendance->start_time = $attendance->start_time === null ? null : $attendance->timeFormatConvert($attendance->start_time);
                $attendance->end_time = $attendance->end_time === null ? null : $attendance->timeFormatConvert($attendance->end_time);

                // -------------------
                // 滞在時間の計算
                // -------------------
                $startTime = $attendance->start_time === null ? null : Carbon::createFromFormat('H:i', $attendance->start_time);
                $endTime = $attendance->end_time === null ? null : Carbon::createFromFormat('H:i', $attendance->end_time);
                $timeInOffice = ($startTime === null || $endTime === null) ? null : $startTime->diffInMinutes($endTime);

                // -------------------
                // 休憩時間の計算
                // -------------------
                $totalBreakTime = 0;
                $breakTimes = $attendance->breakTimes;

                // 休憩時間の登録がある場合のみ計算
                if (!$breakTimes->isEmpty()) {
                    foreach ($breakTimes as $breakTime) {
                        $startTime = Carbon::createFromFormat('H:i', $breakTime->timeFormatConvert($breakTime->start_time));
                        $endTime = Carbon::createFromFormat('H:i', $breakTime->timeFormatConvert($breakTime->end_time));
                        $totalBreakTime += $startTime->diffInMinutes($endTime);
                    }
                }

                // -------------------
                // 勤務時間の計算
                // -------------------
                // 出勤時間・退勤時間のいずれかが登録されていない場合はnull
                $totalWorkTime = $timeInOffice === null ? null : $timeInOffice - $totalBreakTime;

                // -------------------
                // 表記変更
                // formatメソッドが使えない
                // -------------------
                // minutesFormatConvertメソッドは引数がnullの場合はnullを返す
                $attendance->total_break_time = $this->minutesFormatConvert($totalBreakTime);
                $attendance->total_work_time = $this->minutesFormatConvert($totalWorkTime);

                return $attendance;
            });

        // ----------------------------------
        //
        // データ整形
        //
        // ----------------------------------
        // データがある場合のみデータを埋める
        if ($attendances->count() !== 0) {
            // 連想配列のキーをdateに変更
            $attendances = $attendances->keyBy('date');

            // $keyDateは対象月の1日（11月1日など）
            $keyDate = $startOfMonth->copy();
            for ($i = 0; $i < $days; $i++) {
                if (!isset($attendances[$keyDate->format('Y-m-d')])) {
                    $attendances->put($keyDate->format('Y-m-d'), null);
                }
                $keyDate->addDay();
            }
        }

        $data = [
            'attendances' => $attendances,
            'date' => $date,
            'days' => $days,
            'prevDate' => $date->copy()->subMonth(),
            'nextDate' => $date->copy()->addMonth(),
        ];

        return view('attendance_list', $data);
    }

    // 勤怠詳細の表示

    // 管理者編
    //
    // 勤怠一覧の表示
    // 勤怠詳細の表示
    // スタッフ別勤怠一覧の表示

    /**
     * 勤怠申請の作成
     * 勤怠情報未登録日の勤怠情報を登録する
     * @param int $date
     */
    public function create($date)
    {
        // return view('attendance_register');
        return '<p>OK</p>';
    }

    /**
     * 申請画面表示
     * @param int $id
     * $id: attendancesテーブルのid
     */
    public function show($id)
    {
        // 勤怠情報の所有者か確認
        $this->checkAttendanceOwner($id);

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
            $attendance->date = $attendance->toJapaneseDate($attendance->date);
            $attendance->start_time = $attendance->start_time === null ? null : $attendance->timeFormatConvert($attendance->start_time);
            $attendance->end_time = $attendance->end_time === null ? null : $attendance->timeFormatConvert($attendance->end_time);

            // 休憩時間の取得
            $breakTimes = BreakTime::where('attendance_id', $attendance->id)
                ->get()
                ->map(function ($breakTime) {
                    $breakTime->start_time = $breakTime->timeFormatConvert($breakTime->start_time);
                    $breakTime->end_time = $breakTime->end_time === null ? null : $breakTime->timeFormatConvert($breakTime->end_time);
                    return $breakTime;
                });

            return view('attendance_detail', compact('isApplicable', 'attendance', 'breakTimes'));

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

    public function store(StampCorrectionRequest $request, $id)
    {
        dd($request->all());

        $user = auth()->user();

        // 勤怠情報の所有者か確認
        $this->checkAttendanceOwner($id);

        // 申請情報の登録
        ModelsStampCorrectionRequest::create([
            'attendance_id' => $id,
            'user_id' => $user->id,
            'is_approved' => false,
            'request_date' => now()->format('Y-m-d'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'remarks' => $request->input('remarks'),
        ]);

        // 休憩時間の登録
        foreach ($request->input('break_start_time') as $key => $breakStartTime) {
            $breakEndTime = $request->input('break_end_time')[$key];
            BreakTime::create([
                'attendance_id' => $id,
                'start_time' => $breakStartTime,
                'end_time' => $breakEndTime,
            ]);
        }

        // return redirect()->route('attendance.edit', ['id' => $attendance->id]);
    }
}
