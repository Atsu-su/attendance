<?php

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
    return view('auth.user.login');
})->name('login');

Route::get('/register', function(){
    return view('auth.user.register');
})->name('register');

Route::get('/admin/login', function(){
    return view('auth.admin.login');
});

// ヘッダーのミドルウェア
Route::middleware('header')->group(function () {
    Route::get('/attendance', function(){
        return view('user.attendance_register');
    });
    
    Route::get('/list', function() {
        return view('user.attendance_list');
    });

    Route::get('/application', function() {
        return view('user.application_list');
    });

    Route::get('/detail', function() {
        return view('user.attendance_detail');
    });
    
    Route::get('/admin/list', function(){
        return view('admin.attendance_list');
    });
    
    Route::get('/admin/detail', function(){
        return view('admin.attendance_detail');
    });

    Route::get('/admin/staff', function(){
        return view('admin.staff_list');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
    });
});