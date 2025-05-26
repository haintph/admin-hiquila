<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Routing\Route;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Nếu đã đăng nhập, redirect về dashboard phù hợp
        if (Auth::check()) {
            return redirect($this->getRedirectUrl(Auth::user()));
        }
        
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
            $request->session()->regenerate();
            return redirect()->intended($this->getRedirectUrl($user))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất!');
    }

    /**
     * Xác định trang cần chuyển hướng sau khi đăng nhập dựa trên role.
     */
    private function getRedirectUrl($user)
    {
        try {
            return match ($user->role) {
                'owner' => route('owner.dashboard'),          // /owner
                'manager' => route('manager.attendance.list'), // /manager/attendance
                'staff' => route('staff.index'),               // /staff
                'chef' => route('chef.dashboard'),             // /chef
                'customer' => route('customer.dashboard'),     // /dashboard
                default => route('login'), // Redirect về login nếu role không xác định
            };
        } catch (\Exception $e) {
            // Fallback nếu có lỗi
            return route('login');
        }
    }

    /**
     * Kiểm tra route có tồn tại không, nếu không thì dùng URL dự phòng
     */
    private function safeRoute($routeName, $fallbackUrl)
    {
        try {
            if (Route::has($routeName)) {
                return route($routeName);
            }
        } catch (\Exception $e) {
            // Route không tồn tại
        }
        
        return $fallbackUrl;
    }
}