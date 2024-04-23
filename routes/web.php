<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SecondController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Front\UserController;

//========= admin routes =========================

require_once 'admin.php';



Auth::routes(['verify'=> true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');
Route::get('/', function () {
    return 'Home';
});
