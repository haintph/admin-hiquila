<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            // Kiểm tra role của user
            $user = Auth::user();

            if ($user->role === 'Chủ') {
                return redirect('/')->with('success', 'Đăng nhập thành công!');
            }

            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng']);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login')->with('success', 'Bạn đã đăng xuất!');
    }
}
