<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AdminIpWhiteList;

class CheckAdminIpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = AdminIpWhiteList::pluck('ip_address')->toArray();


        if (!in_array($request->ip(), $allowedIps)) {
            abort(403, '您的 IP 不在允許的白名單內');
        }

        return $next($request);
    }
}