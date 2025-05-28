<?php

use App\Livewire\Auth\Login;
use App\Livewire\Member\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

// 登入路由 - 使用標準的 login 名稱
Route::get('/login', Login::class)->name('login');

// 會員區域路由 - 明確指定使用 web guard
Route::prefix('member')->name('member.')->middleware('auth:web')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

// 重定向 member/login 到主要登入頁面
Route::get('/member/login', function () {
    return redirect()->route('login');
});

