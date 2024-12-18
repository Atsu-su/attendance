<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', function(){
    return view('auth.login');
})->name('login');

Route::get('/register', function(){
    return view('auth.register');
})->name('register');

Route::get('/admin/login', function(){
    return view('auth.login');
});

Route::get('/verify', function(){
    return view('auth.verify_email');
});

// ヘッダーのミドルウェア
Route::middleware('header')->group(function () {

    Route::get('/list', function() {
        return view('attendance_list');
    });

    Route::get('/detail', function() {
        return view('attendance_detail');
    });

    Route::get('/admin/detail', function(){
        return view('attendance_detail');
    });

    Route::get('/admin/staff', function(){
        return view('staff_list');
    });

    // --------------------------------------------------------

    // indexの設定
    Route::get('/', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'register'])
            ->name('attendance.register');
        Route::post('/attendance/startwork', [AttendanceController::class, 'startWorkApi'])
            ->name('attendance.start-work');
        Route::post('/attendance/endwork', [AttendanceController::class, 'endWorkApi'])
            ->name('attendance.end-work');
        Route::post('/attendance/startbreak', [AttendanceController::class, 'startBreakApi'])
            ->name('attendance.start-break');
        Route::post('/attendance/end-break', [AttendanceController::class, 'endBreakApi'])
            ->name('attendance.end-break');
        Route::get('/attendance/list/{year}/{month}', [AttendanceController::class, 'showList'])
            ->whereNumber('year')       // {year}は数字のみ許可
            ->whereNumber('month')      // {month}は数字のみ許可
            ->name('attendance.show-list');
        Route::get('/attendance/{id}', [AttendanceController::class, 'show'])
            ->whereNumber('id')         // {id}は数字のみ許可
            ->name('attendance.show');
        Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
            ->name('stamp_correction_request.list');
    });
});