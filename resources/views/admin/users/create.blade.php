@extends('admin.layouts.master')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-0">Thêm Nhân Viên</h3>
            </div>
        </div>
        
        <form action="{{ route('user_store') }}" method="post" enctype="multipart/form-data">
            @csrf
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
                                <img id="imagePreview" src="" alt="Image Preview" class="mt-2" style="max-width: 250px; display: none;">
                            </div>
                        </div>
                        <!-- Cột bên phải: Các trường thông tin -->
                        <div class="col-lg-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 30%;">Họ và tên</th>
                                    <td><input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" required></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><input type="email" name="email" class="form-control" placeholder="Nhập email" required></td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td><input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại"></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ</th>
                                    <td><textarea name="address" class="form-control" rows="2" placeholder="Nhập địa chỉ"></textarea></td>
                                </tr>
                                <tr>
                                    <th>Chức vụ</th>
                                    <td>
                                        <select name="role" class="form-control" required>
                                            <option value="">Chọn chức vụ</option>
                                            <option value="owner">Chủ</option>
                                            <option value="manager">Quản lý</option>
                                            <option value="staff">Nhân viên</option>
                                            <option value="chef">Đầu bếp</option>
                                            <option value="cashier">Thu ngân</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Giới tính</th>
                                    <td>
                                        <select name="gender" class="form-control" required>
                                            <option value="">Chọn giới tính</option>
                                            <option value="male">Nam</option>
                                            <option value="female">Nữ</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh</th>
                                    <td><input type="date" name="dob" class="form-control"></td>
                                </tr>
                                <tr>
                                    <th>Lương</th>
                                    <td><input type="number" name="salary" class="form-control" placeholder="Nhập lương" required></td>
                                </tr>
                                <tr>
                                    <th>Ngày vào làm</th>
                                    <td><input type="date" name="hire_date" class="form-control" required></td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        <select name="status" class="form-control">
                                            <option value="active">Hoạt động</option>
                                            <option value="inactive">Ngừng hoạt động</option>
                                            <option value="terminated">Đã nghỉ việc</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mật khẩu</th>
                                    <td><input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút tạo & hủy -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">Thêm</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('user_list') }}" class="btn btn-primary w-100">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Script xem trước ảnh -->
        <script>
            function previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const image = document.getElementById('imagePreview');
                        image.src = e.target.result;
                        image.style.display = 'block';
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
