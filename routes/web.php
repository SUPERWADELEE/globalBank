<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Front\Auth\Login;


Route::get('/login', Login::class)->name('login');

Route::get('/', function () {
    return redirect()->route('login');
});
