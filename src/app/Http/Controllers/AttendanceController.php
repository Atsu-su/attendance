<?php

namespace App\Http\Controllers;

use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\RequestBreakTime;
use App\Models\User;
use App\Models\StampCorrectionRequest as ModelsStampCorrectionRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
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
     * 勤怠情報の整形
     * @param Attendance $attendance
     * @return Attendance $attendance
     */
    public function attendanceFormatConvert($attendance)
    {
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

        // 休憩時間の登録がある場合のみ計算（ない場合は0分）
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
    }

    /**
     * abortするかを判定
     * @param $callback, $code
     * @return Illuminate\Http\Response
     * $id: attendancesテーブルのid
     */
    public function abort($callback, $code)
    {
        if ($callback()) {
            return abort($code);
        }
    }

    /**
     * 勤怠情報の所有者かどうかを確認
     * @param $id
     * @return Illuminate\Http\Response
     * @return bool
     * $id: attendancesテーブルのid
     */
    public function checkAttendanceOwner($id, $user)
    {
        $attendance = Attendance::where('id', $id)->first();
        if (!$attendance) {
            return abort(404);
        } else if ($attendance->user_id !== $user->id) {
            return abort(403);
        }
        return true;
    }

    /**
     * （共通）トップ画面表示
     */
    public function index()
    {
        if (auth('admin')->check()) {
           return redirect()->route('admin-attendance.show-daily-list', [
               'year' => date('Y'),
               'month' => date('m'),
               'day' => date('d')
           ]);
        }
        return redirect()->route('login');
    }

    /**
     * （ユーザ）勤怠登録トップ画面表示
     */
    public function register()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        $now = now();

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $now->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            try {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $now->format('Y-m-d'),
                    'status' => Attendance::BF_WORK[0]
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

        return view('user_attendance_register', compact('now', 'attendance'));
    }

    /**
     * （ユーザ）出勤登録のAPI
     */
    public function startWorkApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // attendancesテーブルへの登録
        try {
            Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->update([
                    'status' => Attendance::ON_DUTY[0],
                    'start_time' => $time,
                ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    /**
     * （ユーザ）退勤登録のAPI
     */
    public function endWorkApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // attendancesテーブルへの登録
        try {
            Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->update([
                    'status' => Attendance::OFF_DUTY[0],
                    'end_time' => $time,
                ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    /**
     * （ユーザ）休憩開始登録のAPI
     */
    public function startBreakApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->where('status', Attendance::ON_DUTY[0])
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
                'status' => Attendance::BREAK[0]
            ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    /**
     * （ユーザ）休憩終了登録のAPI
     */
    public function endBreakApi()
    {
        $user = auth()->user();

        // 日付・時刻の取得
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->where('status', Attendance::BREAK[0])
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
                'status' => Attendance::ON_DUTY[0]
            ]);

        // レスポンスの返却（json）
        return response()->noContent();

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(null, 500);
        }
    }

    /**
     * （共通）勤怠一覧の表示
     * @param int $year, $month
     * @param int $id
     * $id: usersテーブルのid（管理者からのアクセスのみ必要）
     */
    public function showList($year, $month, $id = null)
    {
        if (auth('admin')->check()) {
            $user = User::find($id);
        } else {
            $user = auth()->user();
        }

        // 与えられた月が未来の場合はabort403
        $this->abort(function () use ($year, $month) {
            if (Carbon::create($year, $month)->isFuture()) {
                return true;    // abort(403)
            }
            return false;
        }, 403);

        // ----------------------------------
        // 日付・時間の取得
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
                return $this->attendanceFormatConvert($attendance);
            });

        // ----------------------------------
        // データ整形
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
            'user' => $user,
            'attendances' => $attendances,
            'date' => $date,
            'days' => $days,
            'prevDate' => $date->copy()->subMonth(),
            'nextDate' => $date->copy()->addMonth(),
        ];

        return view('common_attendance_list', $data);
    }

    /**
     * （共通）勤怠申請の作成
     * 勤怠情報未登録日の勤怠情報を登録する
     * @param int $date
     */
    public function create($year, $month, $day, $id = null)
    {
        if (auth('admin')->check()) {
            $user = User::find($id);
        } else {
            $user = auth()->user();
        }

        $date = Carbon::create($year, $month, $day)->format('Y-m-d');
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        // 勤怠情報が存在していれば作成不可
        if ($attendance) {
            if (auth('admin')->check()) {
                return redirect()->route('admin-attendance.show', $attendance->id);
            }
            return redirect()->route('attendance.show', $attendance->id);
        }

        // 当日あるいは未来日付の場合は勤怠登録画面へリダイレクト
        if ($date >= now()->format('Y-m-d')) {
            if (auth('admin')->check()) {
                return abort(403);
            }
            return redirect()->route('attendance.register');
        }

        // 勤怠情報の登録
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'status' => Attendance::OFF_DUTY[0],
        ]);

        if (auth('admin')->check()) {
            return redirect()->route('admin-attendance.show', $attendance->id);
        }
        return redirect()->route('attendance.show', $attendance->id);
    }

    /**
     * （共通）勤怠情報の削除
     * @param int $date
     */
    public function delete($id)
    {
        Attendance::find($id)->delete();
        return redirect()->route('attendance.show-list', [
            'year' => date('Y'),
            'month' => date('m'),
        ]);
    }

    /**
     * （共通）勤怠一覧から勤怠情報詳細／申請内容表示
     * @param int $id
     * $id: attendancesテーブルのid
     */
    public function show($id)
    {
        $admin = auth('admin')->user();

        // adminガードの場合はアクセス可能
        if (!$admin) {
            // webガードの場合は勤怠情報の所有者か確認
            $user = auth('web')->user();
            $this->checkAttendanceOwner($id, $user);
        }

        // 申請は複数回可能なため最後の申請を取得
        $request = ModelsStampCorrectionRequest::with('requestBreakTimes')
            ->where('attendance_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        // （申請が存在しない OR 申請が承認されている）AND 対象の日付が前日以前
        // の場合に編集可能
        $attendance = Attendance::find($id);
        if ($attendance->date <= now()->subDay()->format('Y-m-d')) {

            $isApplicableForDate = true;

            if ((!$request || $request->is_approved == 1)) {

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

                return view('common_attendance_detail', compact('isApplicableForDate', 'isApplicable', 'attendance', 'breakTimes'));

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

                return view('common_attendance_detail', compact('isApplicableForDate', 'isApplicable', 'request', 'requestBreakTimes'));
            }
        } else {

            $isApplicableForDate = false;

            return view('common_attendance_detail', compact('isApplicableForDate'));
        }
    }

    /**
     * （共通）申請内容の登録
     * @param StampCorrectionRequest $request, $id
     * $id: attendancesテーブルのid
     */
    public function store(StampCorrectionRequest $request, $id)
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
        $stampCorrectionRequest = ModelsStampCorrectionRequest::where('attendance_id', $id)
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
                $stampCorrectionRequest =  ModelsStampCorrectionRequest::create([
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

    /**
     * （管理者）勤怠一覧表示
     * @param int $year, $month, $day
     */
    public function showDailyList($year, $month, $day)
    {
        $date = Carbon::create($year, $month, $day);
        $prevDate = $date->copy()->subDay();
        $nextDate = $date->copy()->addDay();

        $count = 0;
        $isNoData = false;

        $users = User::with(['attendances' => function ($query) use ($date) {
            $query->where('date', $date->format('Y-m-d'));
        }, 'attendances.breakTimes'])
            ->orderBy('id')
            ->get()
            ->map(function ($user) use (&$count) {
                if (empty($user->attendances[0])) {
                    // Undefined offset: 0への対応
                    $user->attendances[0] = new Attendance();
                    $count++;
                } else {
                    $this->attendanceFormatConvert($user->attendances[0]);
                }

                return $user;
            });

            if ($count === count($users)) {
                $isNoData = true;
            }

        return view('admin_daily_attendance_list', compact('date', 'prevDate', 'nextDate', 'users', 'isNoData'));
    }

    /**
     * （管理者）CSVファイルの出力
     */
    public function exportCsv($year, $month, $id)
    {
        $user = User::find($id);

        // ----------------------------------
        // 日付・時間の取得
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
                return $this->attendanceFormatConvert($attendance);
            });

        // ----------------------------------
        // データ整形
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

        // ----------------------------------
        // CSVファイルの出力
        // ----------------------------------
        // map内で配列をreturnするために先に日付を-1日する
        $csvKeyDate = $startOfMonth->copy()->subDay();
        $csvFileName = "attendance_{$user->family_name}{$user->given_name}_{$year}_{$month}.csv";
        $csvData = $attendances
            ->sortKeys()
            ->map(function ($attendance) use (&$csvKeyDate) {
                $csvKeyDate->addDay();
                return [
                    'date' =>  $attendance->date ?? $csvKeyDate->format('Y-m-d'),
                    'start_time' => $attendance->start_time ?? '',
                    'end_time' => $attendance->end_time ?? '',
                    'total_break_time' => $attendance->total_break_time ?? '',
                    'total_work_time' => $attendance->total_work_time ?? '',
                ];
            });

        // CSVヘッダー
        $headers = [
            'date',
            'start_time',
            'end_time',
            'total_break_time',
            'total_work_time'
        ];

        // CSVをダウンロード
        return response()
            ->streamDownload(function() use ($csvData, $headers) {
                $handle = fopen('php://output', 'w');

                // BOMを追加（Excelで開いたときの文字化け防止）
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                // ヘッダーを書き込み
                fputcsv($handle, $headers);

                // データを書き込み
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);

            }, $csvFileName, ['Content-Type' => 'text/csv',]);
    }

    /**
     * （管理者）申請承認
     * @param int $year, $month, $day
     */
    public function storeRequest($stamp_correction_request)
    {
        $request = ModelsStampCorrectionRequest::with('requestBreakTimes', 'attendance')->find($stamp_correction_request);

        try{
            DB::transaction(function () use ($request) {
                // 申請情報の登録
                $attendance =  Attendance::find($request->attendance_id);
                $attendance->update([
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);

                $breakTimes = $request->requestBreakTimes;  // 新たな休憩時間
                $oldBreakTimes = BreakTime::where('attendance_id', $request->attendance_id)
                    ->get();    // これから更新される休憩時間（更新対象）

                // 休憩時間の更新／登録
                if ($oldBreakTimes) {
                    foreach ($oldBreakTimes as $index => $oldBreakTime) {
                        $oldBreakTime->update([
                            'start_time' => $breakTimes[$index]->start_time,
                            'end_time' => $breakTimes[$index]->end_time,
                        ]);
                    }
                } else {
                    // 休憩時間の新規登録（休憩時間登録忘れへの対応）
                    BreakTime::create([
                        'attendance_id' => $request->attendance_id,
                        'start_time' => $breakTimes[0]->start_time,
                        'end_time' => $breakTimes[0]->end_time,
                    ]);
                }

                // 申請のステータス変更
                $request->update(['is_approved' => true]);
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin-stamp-correction-request.index');
        }

        return redirect()->route('admin-stamp-correction-request.index');
    }
}