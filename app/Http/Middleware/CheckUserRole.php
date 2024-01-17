<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (auth()->check()) {
            // Kiểm tra vai trò của người dùng
            if (auth()->user()->role == 1) {
                // Nếu vai trò là 1, cho phép tiếp tục
                return $next($request);
            } else {
                // Nếu vai trò không phải 1, chuyển hướng về trang trước đó
                return redirect()->back();
            }
        }

        // Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
        return redirect('/login');
    }
}
