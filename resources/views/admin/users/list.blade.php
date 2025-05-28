@extends('admin.layouts.master')

@section('content')

    <div class="container-xxl">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Danh Sách Nhân Viên</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('user_create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Thêm Nhân Viên
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Tháng Này
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#!">Download</a></li>
                            <li><a class="dropdown-item" href="#!">Export</a></li>
                            <li><a class="dropdown-item" href="#!">Import</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc tìm kiếm -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form action="{{ route('user_list') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="keyword" class="form-label">Tìm kiếm</label>
                        <input type="text" name="keyword" class="form-control" placeholder="Nhập tên hoặc email" value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label">Chức vụ</label>
                        <select name="role" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="owner" {{ request('role')=='owner' ? 'selected' : '' }}>Chủ</option>
                            <option value="manager" {{ request('role')=='manager' ? 'selected' : '' }}>Quản lý</option>
                            <option value="staff" {{ request('role')=='staff' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="chef" {{ request('role')=='chef' ? 'selected' : '' }}>Đầu bếp</option>
                            <option value="cashier" {{ request('role')=='cashier' ? 'selected' : '' }}>Thu ngân</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            <option value="terminated" {{ request('status')=='terminated' ? 'selected' : '' }}>Đã nghỉ việc</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
            
        </div>
        <!-- End Bộ lọc tìm kiếm -->

        <!-- Hiển thị thông báo -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php                
            // Mapping chức vụ sang tiếng Việt
            $roleMap = [
                'owner'   => 'Chủ',
                'manager' => 'Quản lý',
                'staff'   => 'Nhân viên',
                'chef'    => 'Đầu bếp',
                'cashier' => 'Thu ngân',
            ];
        @endphp

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck1">
                                        <label class="form-check-label" for="customCheck1"></label>
                                    </div>
                                </th>
                                <th>Họ Tên</th>
                                {{-- <th>Email</th> --}}
                                <th>Số Điện Thoại</th>
                                <th>Chức Vụ</th>
                                <th>Lương</th>
                                <th>Ngày Vào Làm</th>
                                <th>Trạng Thái</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck{{ $user->id }}">
                                        <label class="form-check-label" for="customCheck{{ $user->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle" style="width:45px; height:45px; object-fit:cover;">
                                        <span class="fw-semibold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                {{-- <td>{{ $user->email }}</td> --}}
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>{{ $roleMap[$user->role] ?? ucfirst($user->role) }}</td>
                                <td>{{ number_format($user->salary, 0, ',', '.') }} VNĐ</td>
                                <td>{{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d-m-Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        @if ($user->status == 'active') bg-success 
                                        @elseif ($user->status == 'inactive') bg-warning 
                                        @else bg-danger 
                                        @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('user_detail', $user->id) }}" class="btn btn-light btn-sm">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a> <!-- Chi tiết -->
                                        <a href="{{ route('user_edit', $user->id) }}" class="btn btn-soft-primary btn-sm">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a> <!-- Sửa -->
                                        <form action="{{ route('user_destroy', $user->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')" type="submit"
                                                class="btn btn-soft-danger btn-sm">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </button>
                                        </form> <!-- Xóa -->
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                {{ $users->links() }}
            </div>
        </div>

    </div>
    <footer class="footer mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by 
                    <a href="https://1.envato.market/techzaa" class="fw-bold text-decoration-none" target="_blank">Techzaa</a>
                </div>
            </div>
        </div>
    </footer>
@endsection
