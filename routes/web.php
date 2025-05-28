<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

// 登入路由 - 使用標準的 login 名稱
Route::get('/login', Login::class)->name('login');
// 重定向 member/login 到主要登入頁面
Route::get('/member/login', function () {
    return redirect()->route('login');
});
