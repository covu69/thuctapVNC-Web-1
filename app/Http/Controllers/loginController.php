<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class loginController extends Controller
{
    public function index()
    {
        return view('project/login');
    }

    public function check(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Kiểm tra và chuyển hướng dựa vào trạng thái tài khoản
            if (is_null($user->deleted_at)) {
                // Người dùng đã đăng nhập và tài khoản không bị vô hiệu hóa
                return redirect()->route('dashboard');
            } else {
                // Nếu tài khoản bị vô hiệu hóa, chuyển hướng hoặc xử lý tùy thuộc vào logic của bạn
                return redirect('/')->with('error', 'Your account has been deactivated.');
            }
        } else {
            // Đăng nhập không thành công
            return back()->with('error', 'Invalid email or password');
        }
    }


    public function logout(Request $request)
    {
        Auth::logout(); // Đăng xuất người dùng

        $request->session()->invalidate(); // Hủy bỏ phiên làm việc hiện tại

        $request->session()->regenerateToken(); // Tạo lại token mới cho phiên tiếp theo

        return redirect('/'); // Chuyển hướng sau khi đăng xuất, có thể điều chỉnh theo yêu cầu của bạn
    }
}
