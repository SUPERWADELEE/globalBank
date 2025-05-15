<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocaleFromAdminUser
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $locale = Auth::user()->locale ?? 'zh_TW'; // 假設有 locale 欄位
            App::setLocale($locale);
        }

        return $next($request);
    }
}