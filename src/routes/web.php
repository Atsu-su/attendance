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
    return view('auth.login');
})->name('login');

Route::get('/register', function(){
    return view('auth.register');
})->name('register');

Route::middleware('header')->group(function () {
    Route::get('/attendance', function(){
        return view('attendance_register');
    });
    
    Route::get('/list', function() {
        return view('attendance_list');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
    });
});