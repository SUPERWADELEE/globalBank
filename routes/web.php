<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Front\Member\Wallet;
use App\Livewire\Front\Auth\Login;
use App\Livewire\Front\Member\Deposit;


Route::get('/login', Login::class)->name('login');

// 會員區域路由 - 明確指定使用 web guard(要讓前端切版後續再加)
Route::prefix('member')->name('member.')->group(function () {
    Route::get('/wallet', Wallet::class)->name('wallet');
    Route::get('/deposit', Deposit::class)->name('deposit');
}); 
Route::get('/', function () {
    return redirect()->route('login');
});
