@extends('admin.layouts.master')

@section('styles')
<style>
    .table-status-badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }
    
    .area-info {
        font-size: 0.9rem;
    }
    
    .table-number-badge {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 1rem;
    }

    .filter-card {
        border-left: 4px solid #0d6efd;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="navbar-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">
                        <iconify-icon icon="solar:table-2-broken" class="me-2"></iconify-icon>
                        Quản lý bàn
                    </h4>
                </div>
            </div>
            <div>
                <a href="{{ route('tables.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:add-circle-broken" class="me-1"></iconify-icon>
                    Thêm bàn mới
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:table-2-broken" class="fs-48 mb-2 opacity-75"></iconify-icon>
                    <h3 class="mb-1">{{ $tables->total() ?? $tables->count() }}</h3>
                    <p class="mb-0">Tổng số bàn</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:check-circle-bold" class="fs-48 mb-2 opacity-75"></iconify-icon>
                    <h3 class="mb-1">{{ $tables->where('status', 'Trống')->count() }}</h3>
                    <p class="mb-0">Bàn trống</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:users-group-rounded-bold" class="fs-48 mb-2 opacity-75"></iconify-icon>
                    <h3 class="mb-1">{{ $tables->where('status', 'Đang phục vụ')->count() }}</h3>
                    <p class="mb-0">Đang phục vụ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <iconify-icon icon="solar:calendar-broken" class="fs-48 mb-2 opacity-75"></iconify-icon>
                    <h3 class="mb-1">{{ $tables->where('status', 'Đã đặt')->count() }}</h3>
                    <p class="mb-0">Đã đặt trước</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4 filter-card">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <iconify-icon icon="solar:filter-broken" class="me-2"></iconify-icon>
                Bộ lọc và tìm kiếm
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="area_id" class="form-label">Khu vực:</label>
                    <select name="area_id" id="area_id" class="form-select">
                        <option value="">Tất cả khu vực</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->area_id }}" {{ $area_id == $area->area_id ? 'selected' : '' }}>
                                {{ $area->code }} - {{ $area->name }}
                                @if($area->floor) (Tầng {{ $area->floor }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ $status == $statusOption ? 'selected' : '' }}>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="table_type" class="form-label">Loại bàn:</label>
                    <select name="table_type" id="table_type" class="form-select">
                        <option value="">Tất cả loại</option>
                        @foreach ($tableTypes as $type)
                            <option value="{{ $type }}" {{ $table_type == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <iconify-icon icon="solar:filter-broken" class="me-1"></iconify-icon>
                        Lọc
                    </button>
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:refresh-broken" class="me-1"></iconify-icon>
                        Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Hiển thị thông báo -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <div class="me-2">
                    <iconify-icon icon="solar:check-circle-bold" style="font-size: 24px;"></iconify-icon>
                </div>
                <div>
                    <strong>Thành công!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <div class="me-2">
                    <iconify-icon icon="solar:danger-triangle-bold" style="font-size: 24px;"></iconify-icon>
                </div>
                <div>
                    <strong>Lỗi!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Danh sách bàn -->
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <iconify-icon icon="solar:list-bold" class="me-2"></iconify-icon>
                    Danh sách bàn
                    @if($area_id || $status || $table_type)
                        <small class="text-muted">(Đã lọc)</small>
                    @endif
                </h6>
                <span class="badge bg-info">{{ $tables->total() ?? $tables->count() }} bàn</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="px-3">Số bàn</th>
                            <th>Khu vực / Tầng</th>
                            <th>Sức chứa</th>
                            <th>Loại bàn</th>
                            <th>Trạng thái</th>
                            <th>Chi tiêu tối thiểu</th>
                            <th>Đặt trước</th>
                            <th class="text-end pe-3">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $table)
                            <tr>
                                <td class="px-3">
                                    <span class="badge bg-primary-subtle text-primary table-number-badge">
                                        {{ $table->table_number }}
                                    </span>
                                </td>
                                <td>
                                    @if($table->area)
                                        <div class="area-info">
                                            <strong>{{ $table->area->code }} - {{ $table->area->name }}</strong>
                                            @if($table->area->floor)
                                                <br><small class="text-muted">Tầng {{ $table->area->floor }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Không thuộc khu vực</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $table->capacity }}</span> người
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $table->table_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge table-status-badge
                                        @if ($table->status == 'Trống') bg-success-subtle text-success
                                        @elseif($table->status == 'Đã đặt') bg-warning-subtle text-warning
                                        @elseif($table->status == 'Đang phục vụ') bg-primary-subtle text-primary
                                        @elseif($table->status == 'Đang dọn') bg-info-subtle text-info
                                        @elseif($table->status == 'Bảo trì') bg-danger-subtle text-danger
                                        @elseif($table->status == 'Không hoạt động') bg-secondary-subtle text-secondary @endif">
                                        @if ($table->status == 'Trống')
                                            <iconify-icon icon="solar:check-circle-broken" class="me-1"></iconify-icon>
                                        @elseif($table->status == 'Đã đặt')
                                            <iconify-icon icon="solar:calendar-broken" class="me-1"></iconify-icon>
                                        @elseif($table->status == 'Đang phục vụ')
                                            <iconify-icon icon="solar:users-group-rounded-broken" class="me-1"></iconify-icon>
                                        @elseif($table->status == 'Đang dọn')
                                            <iconify-icon icon="solar:broom-broken" class="me-1"></iconify-icon>
                                        @elseif($table->status == 'Bảo trì')
                                            <iconify-icon icon="solar:tools-broken" class="me-1"></iconify-icon>
                                        @else
                                            <iconify-icon icon="solar:close-circle-broken" class="me-1"></iconify-icon>
                                        @endif
                                        {{ $table->status }}
                                    </span>
                                    @if($table->occupied_at && $table->status == 'Đang phục vụ')
                                        <br><small class="text-muted">
                                            Từ {{ $table->occupied_at->format('H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($table->min_spend && $table->min_spend > 0)
                                        <span class="badge bg-warning-subtle text-warning">
                                            {{ number_format($table->min_spend) }}đ
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($table->is_reservable)
                                        <span class="badge bg-success-subtle text-success">
                                            <iconify-icon icon="solar:check-circle-broken" class="me-1"></iconify-icon>
                                            Có
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <iconify-icon icon="solar:close-circle-broken" class="me-1"></iconify-icon>
                                            Không
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if(Route::has('tables.show'))
                                            <a href="{{ route('tables.show', $table->table_id) }}" 
                                               class="btn btn-soft-info btn-sm" title="Xem chi tiết">
                                                <iconify-icon icon="solar:eye-broken" class="align-middle fs-16"></iconify-icon>
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('tables.edit', $table->table_id) }}" 
                                           class="btn btn-soft-primary btn-sm" title="Chỉnh sửa">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-16"></iconify-icon>
                                        </a>

                                        <form action="{{ route('tables.destroy', $table->table_id) }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bàn {{ $table->table_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" title="Xóa">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-16"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <iconify-icon icon="solar:table-2-broken" class="fs-48 text-muted mb-3"></iconify-icon>
                                        <h5 class="text-muted">Chưa có bàn nào</h5>
                                        <p class="text-muted mb-3">
                                            @if($area_id || $status || $table_type)
                                                Không tìm thấy bàn nào với bộ lọc hiện tại
                                            @else
                                                Bắt đầu bằng cách thêm bàn đầu tiên
                                            @endif
                                        </p>
                                        <a href="{{ route('tables.create') }}" class="btn btn-primary">
                                            <iconify-icon icon="solar:add-circle-broken" class="me-1"></iconify-icon>
                                            Thêm bàn mới
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($tables->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Hiển thị {{ $tables->firstItem() ?? 0 }} đến {{ $tables->lastItem() ?? 0 }} 
                trong tổng số {{ $tables->total() ?? 0 }} bàn
            </div>
            <div>
                {{ $tables->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Có thể thêm các script khác nếu cần
    console.log('Tables index loaded successfully');
});
</script>
@endsection