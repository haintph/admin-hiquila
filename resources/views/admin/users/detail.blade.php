@extends('admin.layouts.master')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-0">Chi Tiết Nhân Viên</h3>
            </div>
        </div>

        <!-- Thông tin nhân viên -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin nhân viên</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Ảnh đại diện -->
                    <div class="col-lg-4 text-center">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="img-fluid rounded mb-3" style="max-width: 250px;">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="img-fluid rounded mb-3" style="max-width: 250px;">
                        @endif
                    </div>
                    <!-- Thông tin -->
                    <div class="col-lg-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Họ và tên</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>{{ $user->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Chức vụ</th>
                                <td>{{ ucfirst($user->role) }}</td>
                            </tr>
                            <tr>
                                <th>Giới tính</th>
                                <td>
                                    @if($user->gender == 'male')
                                        Nam
                                    @elseif($user->gender == 'female')
                                        Nữ
                                    @else
                                        Khác
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày sinh</th>
                                <td>{{ $user->dob ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Lương</th>
                                <td>{{ number_format($user->salary, 0, ',', '.') }} VNĐ</td>
                            </tr>
                            <tr>
                                <th>Ngày vào làm</th>
                                <td>{{ $user->hire_date }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <span class="badge 
                                        @if($user->status == 'active') bg-success 
                                        @elseif($user->status == 'inactive') bg-warning 
                                        @else bg-danger 
                                        @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <a href="{{ route('user_edit', $user->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                        <a href="{{ route('user_list') }}" class="btn btn-secondary">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </div>

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
