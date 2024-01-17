<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Kiểm tra nếu là tuyến đường api/login thì bỏ qua kiểm tra và tiếp tục xử lý
        if ($request->is('api/login')) {
            return $next($request);
        }

        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json([
            'code' => 401,
            'message' => ['Phiên làm việc đã hết hạn'],
            'response' => null,
        ], 401);
    }
}

