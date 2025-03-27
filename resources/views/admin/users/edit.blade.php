@extends('admin.layouts.master')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-0">Chỉnh Sửa Thông Tin Nhân Viên</h3>
            </div>
        </div>

        <form action="{{ route('user_update', $user->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin nhân viên</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cột bên trái: Avatar -->
                        <div class="col-lg-4 text-center">
                            <div class="mb-3">
                                <label>Ảnh đại diện</label>
                                <input type="file" name="avatar" id="avatar" class="form-control" onchange="previewImage(event)">
                                @if($user->avatar)
                                    <img id="imagePreview" src="{{ asset('storage/' . $user->avatar) }}" alt="Image Preview" class="mt-2" style="max-width: 250px;">
                                @else
                                    <img id="imagePreview" src="" alt="Image Preview" class="mt-2" style="max-width: 250px; display: none;">
                                @endif
                            </div>
                        </div>
                        <!-- Cột bên phải: Các trường thông tin -->
                        <div class="col-lg-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 30%;">Họ và tên</th>
                                    <td><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ</th>
                                    <td><textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea></td>
                                </tr>
                                <tr>
                                    <th>Chức vụ</th>
                                    <td>
                                        <select name="role" class="form-control" required>
                                            <option value="">Chọn chức vụ</option>
                                            <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Chủ</option>
                                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Quản lý</option>
                                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                            <option value="chef" {{ old('role', $user->role) == 'chef' ? 'selected' : '' }}>Đầu bếp</option>
                                            <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Thu ngân</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Giới tính</th>
                                    <td>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Chọn giới tính</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh</th>
                                    <td><input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob) }}"></td>
                                </tr>
                                <tr>
                                    <th>Lương</th>
                                    <td><input type="number" name="salary" class="form-control" value="{{ old('salary', $user->salary) }}" required></td>
                                </tr>
                                <tr>
                                    <th>Ngày vào làm</th>
                                    <td><input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $user->hire_date) }}" required></td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                            <option value="terminated" {{ old('status', $user->status) == 'terminated' ? 'selected' : '' }}>Đã nghỉ việc</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mật khẩu mới</th>
                                    <td><input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới (nếu muốn đổi)"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Nút lưu & hủy -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">Lưu</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('user_list') }}" class="btn btn-primary w-100">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            function previewImage(event) {
                const file = event.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('imagePreview').src = e.target.result;
                        document.getElementById('imagePreview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }
        </script>
        <!-- Footer -->
        <footer class="footer mt-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <script>document.write(new Date().getFullYear())</script> &copy; Larkon.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
@endsection
