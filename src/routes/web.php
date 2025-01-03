<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StampCorrectionRequestController;
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
    Route::get('admin/login', function() {
        return view('auth.admin_login');
        })->middleware('web', 'guest:admin');

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
                ->whereNumber('year')       // {year}は数字のみ許可
                ->whereNumber('month')      // {month}は数字のみ許可
                ->whereNumber('day')        // {day}は数字のみ許可
                ->name('admin-attendance.show-daily-list');
            Route::get('attendance/{id}', [AttendanceController::class, 'show'])
                ->whereNumber('id')         // {id}は数字のみ許可
                ->name('admin-attendance.show');
            Route::post('attendance/{id}', [AttendanceController::class, 'store'])
                ->whereNumber('id')         // {id}は数字のみ許可
                ->name('admin-attendance.store');
            Route::get('attendance/staff/{year}/{month}/{id}', [AttendanceController::class, 'showList'])
                ->whereNumber('year')        // {year}は数字のみ許可
                ->whereNumber('month')       // {month}は数字のみ許可
                ->whereNumber('id')          // {id}は数字のみ許可
                ->name('admin-attendance.show-list');
            Route::get('staff/list', [StaffController::class, 'showStaffList'])
                ->name('admin-staff.show-list');
            Route::post('csv/{year}/{month}/{id}', [AttendanceController::class, 'exportCsv'])
                ->whereNumber('year')        // {year}は数字のみ許可
                ->whereNumber('month')       // {month}は数字のみ許可
                ->whereNumber('id')          // {id}は数字のみ許可
                ->name('admin-attendance.export-csv');
            Route::get('stamp_correction_request/{id}', [StampCorrectionRequestController::class, 'show'])
                ->whereNumber('id')         // {id}は数字のみ許可
                ->name('admin-stamp-correction-request.show');
            Route::get('stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
                ->name('admin-stamp-correction-request.index');
            Route::get('stamp_correction_request/approve/{id}', [StampCorrectionRequestController::class, 'approve'])
                ->name('stamp-correction-request.approve');
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
            ->whereNumber('year')       // {year}は数字のみ許可
            ->whereNumber('month')      // {month}は数字のみ許可
            ->name('attendance.show-list');
        Route::get('attendance/{id}', [AttendanceController::class, 'show'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('attendance.show');
        Route::post('attendance/{id}', [AttendanceController::class, 'store'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('attendance.store');
        Route::get('attendance/{date}', [AttendanceController::class, 'create'])
            ->name('attendance.create');
        Route::get('stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
            ->name('stamp-correction-request.index');
        Route::get('stamp-correction-request/{id}', [StampCorrectionRequestController::class, 'show'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('stamp-correction-request.show');
    });
});
