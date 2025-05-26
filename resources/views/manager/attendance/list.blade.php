@extends('manager.layouts.master')
@section('content')
<style>
    .status-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
    }
    
    .status-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }
    
    .role-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
    }
    
    .time-info {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .employee-avatar {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }
    
    .shift-select, .status-select {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background: white;
        min-width: 100px;
    }
    
    .shift-select:focus, .status-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .action-btn {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-checkout {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .btn-checkout:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-1px);
    }
    
    .btn-reset {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .btn-reset:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
    }
    
    .work-schedule-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 0.75rem;
        padding: 0.75rem;
        margin: 0.25rem 0;
    }
    
    .schedule-time {
        font-weight: 600;
        color: #0369a1;
    }
    
    .schedule-status {
        font-size: 0.7rem;
        margin-top: 0.25rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        .employee-avatar {
            width: 35px;
            height: 35px;
        }
    }
</style>

<!-- Start Container Fluid -->
<div class="container-xxl">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Quản lý điểm danh nhân viên</h4>
                    
                    <div class="d-flex gap-2 me-2">
                        <form method="GET" action="{{ route('manager.attendance.list') }}" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                    Có mặt
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                    Vắng mặt
                                </option>
                            </select>
                            <select name="shift" class="form-select form-select-sm">
                                <option value="">Tất cả ca làm</option>
                                <option value="morning" {{ request('shift') == 'morning' ? 'selected' : '' }}>
                                    Ca sáng
                                </option>
                                <option value="afternoon" {{ request('shift') == 'afternoon' ? 'selected' : '' }}>
                                    Ca chiều
                                </option>
                                <option value="full_day" {{ request('shift') == 'full_day' ? 'selected' : '' }}>
                                    Cả ngày
                                </option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                Lọc
                            </button>
                        </form>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ date('d/m/Y') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            {{-- {{ route('attendance.report') }} --}}
                            {{-- {{ route('attendance.history') }} --}}
                            <a href="#" class="dropdown-item">Báo cáo điểm danh</a> 
                            <a href="#" class="dropdown-item">Lịch sử điểm danh</a>
                            <a href="#!" class="dropdown-item">Export Excel</a>
                        </div>
                    </div>
                </div>
                
                @if (session('success'))
                    <div class="alert alert-success mx-3">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger mx-3">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

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
                                    <th>Nhân viên</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Ca làm việc</th>
                                    <th>Lịch trình hôm nay</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staffs as $staff)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $staff->id }}">
                                                <label class="form-check-label" for="customCheck{{ $staff->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                    <img src="{{ asset('storage/' . $staff->avatar) }}" alt="" class="avatar-md rounded-circle">
                                                </div>
                                                <div>
                                                    <p class="text-dark fw-medium fs-15 mb-0">{{ $staff->name }}</p>
                                                    <small class="text-muted">{{ $staff->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @switch($staff->role)
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
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($staff->role) }}</span>
                                            @endswitch
                                        </td>
                                        
                                        <td onclick="toggleStatusDetails({{ $staff->id }})" style="cursor: pointer;">
                                            <form action="{{ route('manager.attendance.update', $staff->id) }}" method="POST">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()">
                                                    <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>
                                                        Có mặt
                                                    </option>
                                                    <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>
                                                        Vắng mặt
                                                    </option>
                                                </select>
                                            </form>
                                            <div class="mt-1">
                                                <span class="status-badge {{ $staff->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                                    {{ $staff->status == 'active' ? 'Đang làm việc' : 'Nghỉ' }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <form action="{{ route('manager.attendance.updateShift', $staff->id) }}" method="POST">
                                                @csrf
                                                <select name="shift" class="form-select form-select-sm shift-select" onchange="this.form.submit()">
                                                    <option value="morning" {{ $staff->shift == 'morning' ? 'selected' : '' }}>
                                                        Ca sáng
                                                    </option>
                                                    <option value="afternoon" {{ $staff->shift == 'afternoon' ? 'selected' : '' }}>
                                                        Ca chiều
                                                    </option>
                                                    <option value="full_day" {{ $staff->shift == 'full_day' ? 'selected' : '' }}>
                                                        Cả ngày
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        
                                        <td onclick="toggleScheduleDetails({{ $staff->id }})" style="cursor: pointer;">
                                            <div class="work-schedule-card">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold text-primary">Hôm nay</span>
                                                    @if ($staff->check_in_time && $staff->check_out_time)
                                                        @php
                                                            $workHours = \Carbon\Carbon::parse($staff->check_in_time)->diffInHours(\Carbon\Carbon::parse($staff->check_out_time));
                                                            $workMinutes = \Carbon\Carbon::parse($staff->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($staff->check_out_time)) % 60;
                                                        @endphp
                                                        <span class="badge bg-success">{{ $workHours }}h {{ $workMinutes }}m</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Vào làm</small>
                                                        <div class="schedule-time">
                                                            @if ($staff->check_in_time)
                                                                 {{ \Carbon\Carbon::parse($staff->check_in_time)->format('H:i') }}
                                                            @else
                                                                <span class="text-muted">--:--</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Tan làm</small>
                                                        <div class="schedule-time">
                                                            @if ($staff->check_out_time)
                                                                 {{ \Carbon\Carbon::parse($staff->check_out_time)->format('H:i') }}
                                                            @else
                                                                <span class="text-muted">--:--</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="schedule-status mt-2">
                                                    @if ($staff->status == 'active' && !$staff->check_out_time)
                                                        <span class="text-success">Đang trong ca làm việc</span>
                                                    @elseif ($staff->check_out_time)
                                                        <span class="text-info">Đã hoàn thành ca</span>
                                                    @else
                                                        <span class="text-muted">Chưa bắt đầu</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <small class="text-muted d-block">Click để xem chi tiết</small>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                @if ($staff->status == 'active' && !$staff->check_out_time)
                                                    <form action="{{ route('manager.attendance.checkOut', $staff->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-light btn-sm">
                                                            <iconify-icon icon="solar:logout-2-broken" class="align-middle fs-18"></iconify-icon>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <button type="button" class="btn btn-light btn-sm" onclick="viewAttendanceDetails({{ $staff->id }})">
                                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                                </button>
                                                
                                                <form action="{{ route('manager.attendance.reset', $staff->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-soft-danger btn-sm" 
                                                            onclick="return confirm('Bạn có chắc chắn muốn reset điểm danh?')">
                                                        <iconify-icon icon="solar:restart-broken" class="align-middle fs-18"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Chi tiết trạng thái (ẩn) -->
                                    <tr id="status-details-{{ $staff->id }}" style="display: none;">
                                        <td colspan="7">
                                            <div class="p-3" style="background-color: #f8f9fa;">
                                                <h6 class="mb-2">Chi tiết trạng thái - {{ $staff->name }}</h6>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <small class="text-muted">Trạng thái hiện tại:</small>
                                                        <div class="fw-bold">{{ $staff->status == 'active' ? 'Đang làm việc' : 'Nghỉ' }}</div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted">Ca làm việc:</small>
                                                        <div class="fw-bold">
                                                            @switch($staff->shift)
                                                                @case('morning') Ca sáng @break
                                                                @case('afternoon') Ca chiều @break
                                                                @case('full_day') Cả ngày @break
                                                            @endswitch
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <small class="text-muted">Cập nhật lần cuối:</small>
                                                        <div class="fw-bold">{{ $staff->updated_at ? $staff->updated_at->format('H:i d/m/Y') : 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Chi tiết lịch trình (ẩn) -->
                                    <tr id="schedule-details-{{ $staff->id }}" style="display: none;">
                                        <td colspan="7">
                                            <div class="p-3" style="background-color: #f0f9ff;">
                                                <h6 class="mb-2">Chi tiết lịch trình - {{ $staff->name }}</h6>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <small class="text-muted">Giờ vào:</small>
                                                        <div class="fw-bold">
                                                            @if ($staff->check_in_time)
                                                                {{ \Carbon\Carbon::parse($staff->check_in_time)->format('H:i:s') }}
                                                            @else
                                                                <span class="text-muted">Chưa check in</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted">Giờ ra:</small>
                                                        <div class="fw-bold">
                                                            @if ($staff->check_out_time)
                                                                {{ \Carbon\Carbon::parse($staff->check_out_time)->format('H:i:s') }}
                                                            @else
                                                                <span class="text-muted">Chưa check out</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if ($staff->check_in_time && $staff->check_out_time)
                                                        <div class="col-md-3">
                                                            <small class="text-muted">Tổng thời gian:</small>
                                                            @php
                                                                $totalMinutes = \Carbon\Carbon::parse($staff->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($staff->check_out_time));
                                                                $hours = floor($totalMinutes / 60);
                                                                $minutes = $totalMinutes % 60;
                                                            @endphp
                                                            <div class="fw-bold text-primary">{{ $hours }}h {{ $minutes }}m</div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-3">
                                                        <small class="text-muted">Ngày:</small>
                                                        <div class="fw-bold">{{ date('d/m/Y') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer border-top d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Tổng: {{ $staffs->total() }} nhân viên</strong>
                        <span class="text-muted ms-2">
                            ({{ $staffs->where('status', 'active')->count() }} đang làm việc, 
                             {{ $staffs->where('status', 'inactive')->count() }} nghỉ)
                        </span>
                    </div>
                    {{ $staffs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== Footer Start ========== -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by
                <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon>
                <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
            </div>
        </div>
    </div>
</footer>
<!-- ========== Footer End ========== -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox "select all" functionality
    document.getElementById('customCheck1').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody .form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Shift change confirmation
    document.querySelectorAll('.shift-select').forEach(select => {
        select.addEventListener('change', function(e) {
            const row = this.closest('tr');
            const statusBadge = row.querySelector('.status-active');
            const checkInTime = row.querySelector('.schedule-time').textContent;
            
            if (statusBadge && checkInTime !== '--:--') {
                if (!confirm('Nhân viên đã check-in. Bạn có chắc muốn đổi ca?')) {
                    e.preventDefault();
                    this.selectedIndex = this.dataset.originalIndex;
                    return false;
                }
            }
        });
        
        // Store original index for revert
        select.dataset.originalIndex = select.selectedIndex;
    });
});

// Toggle functions for showing/hiding details
function toggleStatusDetails(userId) {
    const detailRow = document.getElementById('status-details-' + userId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function toggleScheduleDetails(userId) {
    const detailRow = document.getElementById('schedule-details-' + userId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function viewAttendanceDetails(userId) {
    // Toggle both status and schedule details
    toggleStatusDetails(userId);
    toggleScheduleDetails(userId);
}
</script>
@endsection