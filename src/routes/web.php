<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Requests\StampCorrectionRequest;
use App\Models\Attendance;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 修正
// 管理者ログイン時はユーザ用のサイトにアクセスできない（逆も同じ）ようにする

// 日付の情報はheaderと
Route::middleware('date')->group(function () {
    // -----------------------------------------------------
    // 管理者用のルーティング
    // -----------------------------------------------------
    // 管理者ログイン画面
    Route::get('admin/login', [AdminLoginController::class, 'index'])
        ->middleware('web', 'guest:admin');

    Route::post('admin/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(['guest:admin'])
        ->name('admin-login');

    Route::middleware(['auth:admin'])->group(function () {
        Route::prefix('admin')->group(function() {
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('admin-logout');
            Route::get('attendance/list', [AttendanceController::class, 'index'])
                ->name('admin-attendance.index');
            Route::get('attendance/list/{year}/{month}/{day}', [AttendanceController::class, 'showDailyList'])
                ->whereNumber(['year', 'month', 'day'])       // 数字のみ許可
                ->name('admin-attendance.show-daily-list');
            Route::get('attendance/staff/{year}/{month}/{id}', [AttendanceController::class, 'showList'])
                ->whereNumber(['year', 'month', 'id'])        // 数字のみ許可
                ->name('admin-attendance.show-list');
            Route::get('attendance/{id}', [AttendanceController::class, 'show'])
                ->whereNumber('id')         // {id}は数字のみ許可
                ->name('admin-attendance.show');
            Route::get('attendance/{year}/{month}/{day}/{id}', [AttendanceController::class, 'create'])
                ->whereNumber(['year', 'month', 'day', 'id']) // 数字のみ許可
                ->name('admin-attendance.create');
            Route::post('attendance/store', [AttendanceController::class, 'store'])
                ->name('admin-attendance.store');
            Route::get('csv/{year}/{month}/{id}', [AttendanceController::class, 'exportCsv'])
                ->whereNumber(['year', 'month', 'id'])        // 数字のみ許可
                ->name('admin-attendance.export-csv');
            Route::get('staff/list', [StaffController::class, 'showList'])
                ->name('admin-staff.show-list');

            Route::get('stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
                ->name('admin-stamp-correction-request.index');
            Route::post('stamp_correction_request/{id}', [StampCorrectionRequestController::class, 'store'])
                ->whereNumber('id')         // {id}は数字のみ許可
                ->name('admin-stamp-correction-request.store');

            // -------------------------------------------------------------------
            // 結局同じメソッドを呼び出すのでルートを分ける必要はないが、意味が異なるので分ける
            // -------------------------------------------------------------------
            // 承認済み申請の表示
            Route::get('stamp_correction_request/{stamp_correction_request}', [StampCorrectionRequestController::class, 'show'])
                ->whereNumber('stamp_correction_request')         // {stamp_correction_request}は数字のみ許可
                ->name('admin-stamp-correction-request.show');
            // 未承認申請の表示
            Route::get('stamp_correction_request/approve/{stamp_correction_request}', [StampCorrectionRequestController::class, 'show'])
                ->whereNumber('stamp_correction_request')         // {stamp_correction_request}は数字のみ許可
                ->name('admin-stamp-correction-request-approve.show');

            Route::post('stamp_correction_request/approve/{stamp_correction_request}', [AttendanceController::class, 'update'])
                ->whereNumber('stamp_correction_request')         // {stamp_correction_request}は数字のみ許可
                ->name('admin-attendance.update');
        });
    });

    // -----------------------------------------------------
    // ユーザ用のルーティング
    // -----------------------------------------------------
    // indexの設定
    Route::get('/', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('attendance', [AttendanceController::class, 'register'])
            ->name('attendance.register');
        Route::post('attendance/startwork', [AttendanceController::class, 'startWorkApi'])
            ->name('attendance.start-work');
        Route::post('attendance/endwork', [AttendanceController::class, 'endWorkApi'])
            ->name('attendance.end-work');
        Route::post('attendance/startbreak', [AttendanceController::class, 'startBreakApi'])
            ->name('attendance.start-break');
        Route::post('attendance/endbreak', [AttendanceController::class, 'endBreakApi'])
            ->name('attendance.end-break');
        Route::get('attendance/list/{year}/{month}', [AttendanceController::class, 'showList'])
            ->whereNumber(['year', 'month'])       // 数字のみ許可
            ->name('attendance.show-list');
        Route::get('attendance/{id}', [AttendanceController::class, 'show'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('attendance.show');

        Route::get('stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
            ->name('stamp-correction-request.index');
        Route::post('stamp_correction_request/{id}', [StampCorrectionRequestController::class, 'store'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('stamp-correction-request.store');
        Route::get('stamp_correction_request/{stamp_correction_request}', [StampCorrectionRequestController::class, 'show'])
            ->whereNumber('stamp_correction_request')         // {stamp_correction_request}は数字のみ許可
            ->name('stamp-correction-request.show');
    });
});
