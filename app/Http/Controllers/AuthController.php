<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Lấy user theo email
        $user = User::where('email', $request->email)->first();

        // Kiểm tra user có tồn tại không
        if (!$user) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        // Chặn user bị "terminated" hoặc "inactive"
        if ($user->status === 'terminated') {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa vĩnh viễn.']);
        }

        if ($user->status === 'inactive') {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị vô hiệu hóa.']);
        }

        // Đăng nhập nếu thông tin hợp lệ
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->intended($this->getRedirectUrl($user))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Bạn đã đăng xuất!');
    }

    /**
     * Xác định trang cần chuyển hướng sau khi đăng nhập dựa trên role.
     */
    private function getRedirectUrl($user)
    {
        return match ($user->role) {
            'owner' => route('owner.dashboard'), // Trang admin chính
            'manager' => route('manager.dashboard'), // Trang quản lý
            'staff' => route('staff.dashboard'), // Trang nhân viên
            default => url('/'), // Redirect về trang chủ nếu không xác định
        };
    }

}
