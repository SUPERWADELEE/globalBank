<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AdminIpWhitelist;

class CheckAdminIpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = AdminIpWhitelist::pluck('ip_address')->toArray();
        // 當白名單為空時，系統尚未啟用限制機制，允許所有人進入
        if (empty($allowedIps)) {
            return $next($request);
        }
        if (!in_array($request->ip(), $allowedIps)) {
            abort(403, __('admin_ip_white_list.ip_not_allowed'));
        }

        return $next($request);
    }
}
