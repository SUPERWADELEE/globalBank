<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Front\Member\Wallet;
use App\Livewire\Front\Auth\Login;
use App\Livewire\Front\Member\Withdraw;
use App\Livewire\Front\Member\Deposit;
use App\Livewire\Front\Member\Exchange;
use App\Livewire\Front\Member\ExchangeRate;
use App\Livewire\Front\Member\Records\Index;
use App\Livewire\Front\Member\Records\Show;
use App\Livewire\Front\Member\Info;

Route::get('/login', Login::class)->name('login');

// 會員區域路由 - 明確指定使用 web guard(要讓前端切版後續再加)
Route::prefix('member')->name('member.')->group(function () {
    Route::get('/wallet', Wallet::class)->name('wallet');
    Route::get('/withdraw', Withdraw::class)->name('withdraw');
    Route::get('/deposit', Deposit::class)->name('deposit');
    Route::get('/exchange', Exchange::class)->name('exchange');
    Route::get('/exchange-rate', ExchangeRate::class)->name('exchange_rate');
    Route::prefix('records')->name('records.')->group(function () {
        Route::get('/', Index::class)->name('index');         
        Route::get('/{record}', Show::class)->name('show');   
    });
    Route::get('/info', Info::class)->name('info');
}); 
Route::get('/', function () {
    return redirect()->route('login');
});
