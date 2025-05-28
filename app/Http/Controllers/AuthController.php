<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        // Nếu đã đăng nhập, redirect về dashboard phù hợp
        if (Auth::check()) {
            return redirect($this->getRedirectUrl(Auth::user()));
        }
        
        return view('admin.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới
     */
    public function register(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'terms' => 'required|accepted',
        ], [
            // Custom error messages
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'terms.required' => 'Bạn phải đồng ý với điều khoản sử dụng.',
            'terms.accepted' => 'Bạn phải đồng ý với điều khoản sử dụng.',
        ]);

        try {
            // Xử lý upload avatar
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $this->handleAvatarUpload($request->file('avatar'));
            }

            // Tạo user mới
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $validatedData['phone'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'role' => 'customer', // Mặc định role là customer
                'status' => 'active', // Mặc định status là active
            ]);

            // Tự động đăng nhập sau khi đăng ký thành công
            Auth::login($user);
            
            return redirect()->route('customer.dashboard')->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với hệ thống.');
            
        } catch (\Exception $e) {
            // Xóa avatar nếu có lỗi xảy ra
            if (isset($avatarPath) && $avatarPath) {
                Storage::delete('public/' . $avatarPath);
            }
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors(['error' => 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.']);
        }
    }

    /**
     * Xử lý upload avatar
     */
    private function handleAvatarUpload($file)
    {
        try {
            // Tạo tên file unique
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Lưu file vào storage/app/public/avatars
            $path = $file->storeAs('avatars', $fileName, 'public');
            
            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Không thể upload avatar: ' . $e->getMessage());
        }
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
                'owner' => route('admin.dashboard.index'),          // /owner
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