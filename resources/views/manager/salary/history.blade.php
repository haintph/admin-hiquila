@extends('manager.layouts.master')
@section('content')
    <!-- Start Container Fluid -->
    <div class="container-xxl">

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">Lịch Sử Bảng Lương</h4>

                        <form method="GET" action="{{ route('salary.history') }}" class="d-flex gap-2 me-2">
                            <select name="month" class="form-select form-select-sm">
                                <option value="">Tất cả tháng</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        Tháng {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="year" class="form-select form-select-sm">
                                <option value="">Tất cả năm</option>
                                @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                Lọc
                            </button>
                        </form>

                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Lịch sử lương
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('salary.calculate') }}" class="dropdown-item">Tính lương</a>
                                <a href="{{ route('salary.settings') }}" class="dropdown-item">Cài đặt lương</a>
                                <a href="#!" class="dropdown-item">Export All</a>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success mx-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($records->count() > 0)
                        <div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover table-centered">
                                    <thead class="bg-light-subtle">
                                        <tr>
                                            <th style="width: 20px;">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="customCheck1">
                                                    <label class="form-check-label" for="customCheck1"></label>
                                                </div>
                                            </th>
                                            <th>Nhân Viên</th>
                                            <th>Vai Trò</th>
                                            <th>Tháng/Năm</th>
                                            <th>Giờ Làm Việc</th>
                                            <th>Chi Tiết Lương</th>
                                            <th>Tổng Lương</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $record)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="customCheck{{ $record->id }}">
                                                        <label class="form-check-label" for="customCheck{{ $record->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                            <img src="{{ asset('storage/' . $record->user->avatar) }}" alt="" class="avatar-md">
                                                        </div>
                                                        <p class="text-dark fw-medium fs-15 mb-0">{{ $record->user->name }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($record->user->role)
                                                        @case('manager')
                                                            <span class="badge bg-primary">Quản lý</span>
                                                            @break
                                                        @case('staff')
                                                            <span class="badge bg-info">Nhân viên</span>
                                                            @break
                                                        @case('chef')
                                                            <span class="badge bg-warning">Đầu bếp</span>
                                                            @break
                                                        @case('cashier')
                                                            <span class="badge bg-success">Thu ngân</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $record->month }}/{{ $record->year }}</span>
                                                </td>
                                                <td onclick="toggleWorkDetails({{ $record->id }})" style="cursor: pointer;">
                                                    <span class="fw-bold">{{ $record->total_hours_worked }}h</span>
                                                    @if($record->overtime_hours > 0)
                                                        <small class="text-warning d-block">+{{ $record->overtime_hours }}h tăng ca</small>
                                                    @endif
                                                    <small class="text-muted">{{ $record->days_worked }} ngày</small>
                                                </td>
                                                <td onclick="toggleSalaryDetails({{ $record->id }})" style="cursor: pointer;">
                                                    <div class="small">
                                                        <div>Cơ bản: <span class="fw-bold">{{ number_format($record->base_salary, 0, ',', '.') }}₫</span></div>
                                                        <div>Theo giờ: <span class="fw-bold">{{ number_format($record->hourly_salary, 0, ',', '.') }}₫</span></div>
                                                        @if($record->overtime_salary > 0)
                                                            <div>Tăng ca: <span class="fw-bold text-warning">{{ number_format($record->overtime_salary, 0, ',', '.') }}₫</span></div>
                                                        @endif
                                                        @if($record->bonus > 0)
                                                            <div>Thưởng: <span class="fw-bold text-success">+{{ number_format($record->bonus, 0, ',', '.') }}₫</span></div>
                                                        @endif
                                                        @if($record->deduction > 0)
                                                            <div>Trừ: <span class="fw-bold text-danger">-{{ number_format($record->deduction, 0, ',', '.') }}₫</span></div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <div class="fw-bold text-success" style="font-size: 1.1rem;">
                                                            {{ number_format($record->total_salary, 0, ',', '.') }}₫
                                                        </div>
                                                        <small class="text-muted">Đã thanh toán</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-light btn-sm" onclick="viewDetails({{ $record->id }})">
                                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                                        </button>
                                                        <button type="button" class="btn btn-soft-primary btn-sm" onclick="exportSalary({{ $record->month }}, {{ $record->year }}, {{ $record->user_id }})">
                                                            <iconify-icon icon="solar:download-broken" class="align-middle fs-18"></iconify-icon>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Chi tiết giờ làm việc (ẩn) -->
                                            <tr id="work-details-{{ $record->id }}" style="display: none;">
                                                <td colspan="8">
                                                    <div class="p-3" style="background-color: #f8f9fa;">
                                                        <h6 class="mb-2">Chi tiết giờ làm việc - {{ $record->user->name }} ({{ $record->month }}/{{ $record->year }})</h6>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <small class="text-muted">Tổng giờ làm:</small>
                                                                <div class="fw-bold text-primary">{{ $record->total_hours_worked }} giờ</div>
                                                            </div>
                                                            @if($record->overtime_hours > 0)
                                                                <div class="col-md-3">
                                                                    <small class="text-muted">Giờ tăng ca:</small>
                                                                    <div class="fw-bold text-warning">{{ $record->overtime_hours }} giờ</div>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-3">
                                                                <small class="text-muted">Tổng ngày làm:</small>
                                                                <div class="fw-bold">{{ $record->days_worked }} ngày</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <small class="text-muted">Vai trò:</small>
                                                                <div class="fw-bold">{{ ucfirst($record->user->role) }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Chi tiết lương (ẩn) -->
                                            <tr id="salary-details-{{ $record->id }}" style="display: none;">
                                                <td colspan="8">
                                                    <div class="p-3" style="background-color: #f0f9ff;">
                                                        <h6 class="mb-2">Chi tiết lương - {{ $record->user->name }} ({{ $record->month }}/{{ $record->year }})</h6>
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <small class="text-muted">Lương cơ bản:</small>
                                                                <div class="fw-bold">{{ number_format($record->base_salary, 0, ',', '.') }}₫</div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <small class="text-muted">Lương theo giờ:</small>
                                                                <div class="fw-bold">{{ number_format($record->hourly_salary, 0, ',', '.') }}₫</div>
                                                            </div>
                                                            @if($record->overtime_salary > 0)
                                                                <div class="col-md-2">
                                                                    <small class="text-muted">Lương tăng ca:</small>
                                                                    <div class="fw-bold text-warning">{{ number_format($record->overtime_salary, 0, ',', '.') }}₫</div>
                                                                </div>
                                                            @endif
                                                            @if($record->bonus > 0)
                                                                <div class="col-md-2">
                                                                    <small class="text-muted">Tiền thưởng:</small>
                                                                    <div class="fw-bold text-success">{{ number_format($record->bonus, 0, ',', '.') }}₫</div>
                                                                </div>
                                                            @endif
                                                            @if($record->deduction > 0)
                                                                <div class="col-md-2">
                                                                    <small class="text-muted">Tiền trừ:</small>
                                                                    <div class="fw-bold text-danger">{{ number_format($record->deduction, 0, ',', '.') }}₫</div>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-2">
                                                                <small class="text-muted">Tổng lương:</small>
                                                                <div class="fw-bold text-success fs-5">{{ number_format($record->total_salary, 0, ',', '.') }}₫</div>
                                                            </div>
                                                        </div>
                                                        @if($record->note)
                                                            <hr>
                                                            <div>
                                                                <small class="text-muted">Ghi chú:</small>
                                                                <div class="mt-1 p-2 bg-light rounded">{{ $record->note }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Tổng chi phí hiển thị: </strong>
                                    <span class="text-success fw-bold fs-5">
                                        {{ number_format($records->sum('total_salary'), 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <nav aria-label="Page navigation">
                                    {{ $records->links() }}
                                </nav>
                            </div>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <div class="text-muted">
                                <iconify-icon icon="solar:file-invoice-dollar-broken" class="fs-1 mb-3 opacity-50"></iconify-icon>
                                <h4>Chưa có lịch sử lương</h4>
                                <p>Chưa có bảng lương nào được lưu trong hệ thống</p>
                                <a href="{{ route('salary.calculate') }}" class="btn btn-primary">
                                    Tính lương ngay
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <!-- End Container Fluid -->

    <!-- ========== Footer Start ========== -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <script>
                        document.write(new Date().getFullYear())
                    </script> &copy; Larkon. Crafted by
                    <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon>
                    <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- ========== Footer End ========== -->

<script>
// Toggle functions for showing/hiding details
function toggleWorkDetails(recordId) {
    const detailRow = document.getElementById('work-details-' + recordId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function toggleSalaryDetails(recordId) {
    const detailRow = document.getElementById('salary-details-' + recordId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function viewDetails(recordId) {
    // Toggle both work and salary details
    toggleWorkDetails(recordId);
    toggleSalaryDetails(recordId);
}

function exportSalary(month, year, userId = null) {
    let url = `/salary/export/${month}/${year}`;
    if (userId) {
        url += `?user_id=${userId}`;
    }
    
    // Tạo link download
    const link = document.createElement('a');
    link.href = url;
    link.download = `bang_luong_${month}_${year}${userId ? '_' + userId : ''}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Select all checkbox functionality
document.getElementById('customCheck1').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection