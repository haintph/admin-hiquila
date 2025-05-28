<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập.');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
