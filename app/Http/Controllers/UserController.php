<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    // public function list()
    // {
    //     $users = User::query()->latest('id')->paginate(10);
    //     return view('admin.users.list', compact('users'));
    // }
    public function create() {
        return view('admin.users.create');
    }
    public function list(Request $request)
    {
        $query = User::query();

        // Tìm kiếm ghép theo tên và email nếu có keyword
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Lọc theo chức vụ (nếu có)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Lọc theo trạng thái (nếu có)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest('id')->paginate(10)->appends($request->all());

        return view('admin.users.list', compact('users'));
    }



    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        // Xử lý lưu ảnh nếu có
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Tạo user
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'dob' => $validatedData['dob'],
            'gender' => $validatedData['gender'],
            'avatar' => $avatarPath,
            'hire_date' => $validatedData['hire_date'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('user_list')->with('success', 'Thêm nhân viên thành công!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        // Xử lý ảnh đại diện nếu có ảnh mới
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }
            $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật thông tin nhân viên
        $user->update($validatedData);

        return redirect()->route('user_list')->with('success', 'Cập nhật nhân viên thành công!');
    }

    public function detail($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.detail', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Xóa ảnh nếu có
        if ($user->avatar) {
            Storage::delete('public/' . $user->avatar);
        }

        // Xóa nhân viên khỏi database
        $user->delete();

        return redirect()->route('user_list')->with('success', 'Xóa nhân viên thành công!');
    }
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $avatarName = $user->id . '.jpg';

        // Lưu ảnh vào storage/app/public/avatars/
        $request->file('avatar')->storeAs('public/avatars', $avatarName);

        // Cập nhật đường dẫn avatar cho user (nếu cần)
        $user->avatar = 'avatars/' . $avatarName;
        $user->save();

        return back()->with('success', 'Avatar đã được cập nhật!');
    }
}
