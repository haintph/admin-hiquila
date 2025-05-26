@extends('admin.layouts.master')

@section('title', 'Dashboard Thống Kê')

@section('content')
<div class="container-fluid">
    <!-- Alert thông báo -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-truncate mb-3" role="alert">
                📊 Dashboard Thống Kê Nhà Hàng - Dữ liệu cập nhật: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.dashboard.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Từ ngày</label>
                            <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Đến ngày</label>
                            <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Khu vực</label>
                            <select class="form-select" name="area_id">
                                <option value="">Tất cả khu vực</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->area_id }}" {{ request('area_id') == $area->area_id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-search me-1"></i>Lọc
                            </button>
                            <a href="{{ route('admin.dashboard.export', request()->all()) }}" class="btn btn-success">
                                <i class="bx bx-download me-1"></i>Xuất
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <!-- Tổng Doanh Thu -->
        <div class="col-md-6 col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-soft-success rounded">
                                <i class="bx bx-dollar-circle avatar-title fs-24 text-success"></i>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Tổng Doanh Thu</p>
                            <h3 class="text-dark mt-1 mb-0">{{ number_format($stats['total_revenue']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-success"><i class="bx bxs-up-arrow fs-12"></i> VNĐ</span>
                            <span class="text-muted ms-1 fs-12">Tổng cộng</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">Chi tiết</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng Đơn Hàng -->
        <div class="col-md-6 col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-soft-primary rounded">
                                <iconify-icon icon="solar:cart-5-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Tổng Đơn Hàng</p>
                            <h3 class="text-dark mt-1 mb-0">{{ $stats['total_orders'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-primary"><i class="bx bxs-up-arrow fs-12"></i> Đơn</span>
                            <span class="text-muted ms-1 fs-12">Hoàn thành</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">Xem thêm</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn Đã Hủy -->
        <div class="col-md-6 col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-soft-warning rounded">
                                <i class="bx bx-x-circle avatar-title fs-24 text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Đơn Đã Hủy</p>
                            <h3 class="text-dark mt-1 mb-0">{{ $stats['cancelled_orders'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-warning"><i class="bx bxs-down-arrow fs-12"></i> 
                                {{ $stats['total_orders'] > 0 ? round(($stats['cancelled_orders'] / ($stats['total_orders'] + $stats['cancelled_orders'])) * 100, 1) : 0 }}%
                            </span>
                            <span class="text-muted ms-1 fs-12">Tỷ lệ hủy</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">Chi tiết</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn Hàng Trung Bình -->
        <div class="col-md-6 col-lg-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-soft-info rounded">
                                <i class="bx bx-trending-up avatar-title fs-24 text-info"></i>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Đơn Hàng TB</p>
                            <h3 class="text-dark mt-1 mb-0">{{ number_format($stats['avg_order_value']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-info"><i class="bx bxs-up-arrow fs-12"></i> VNĐ</span>
                            <span class="text-muted ms-1 fs-12">Per đơn</span>
                        </div>
                        <a href="#!" class="text-reset fw-semibold fs-12">Phân tích</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu theo ngày (dạng Progress Bar) -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">📈 Doanh Thu 7 Ngày Gần Nhất</h4>
                    <div class="mt-4">
                        @php
                            $maxRevenue = collect($dailyRevenueData)->max() ?: 1;
                        @endphp
                        
                        @foreach($dailyLabels as $index => $label)
                            @php
                                $revenue = $dailyRevenueData[$index] ?? 0;
                                $percentage = $maxRevenue > 0 ? ($revenue / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">{{ $label }}</span>
                                    <span class="text-success fw-bold">{{ number_format($revenue) }} VNĐ</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if(empty($dailyLabels))
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-info-circle fs-24 mb-2"></i>
                                <p>Chưa có dữ liệu doanh thu</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu theo khu vực (dạng Card) -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🏢 Doanh Thu Theo Khu Vực</h5>
                    <div class="mt-3">
                        @php
                            $totalAreaRevenue = $areaRevenue->sum('total_revenue') ?: 1;
                        @endphp
                        
                        @foreach($areaRevenue as $index => $area)
                            @php
                                $revenue = $area->total_revenue ?? 0;
                                $percentage = ($revenue / $totalAreaRevenue) * 100;
                                $colors = ['primary', 'success', 'warning', 'info', 'secondary'];
                                $color = $colors[$index % count($colors)];
                                $floorText = $area->floor ? "Tầng {$area->floor}" : "N/A";
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-soft-{{ $color }} rounded me-3">
                                        <span class="avatar-title text-{{ $color }} fs-16">{{ $area->code ?? substr($area->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $area->name }}</h6>
                                        <small class="text-muted">
                                            🏗️ {{ $floorText }} | 💰 {{ number_format($revenue) }} VNĐ
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-{{ $color }}">{{ round($percentage, 1) }}%</span>
                            </div>
                        @endforeach
                        
                        @if($areaRevenue->isEmpty())
                            <div class="text-center text-muted py-3">
                                <i class="bx bx-building fs-24 mb-2"></i>
                                <p class="mb-0">Chưa có dữ liệu khu vực</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top món bán chạy và danh mục -->
    <div class="row">
        <!-- Top món bán chạy -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between gap-2">
                    <h4 class="card-title flex-grow-1">🌟 Top 10 Món Bán Chạy</h4>
                    <span class="badge bg-primary">{{ count($topDishes) }} món</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap table-centered m-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="text-muted ps-3">Hạng</th>
                                <th class="text-muted">Tên Món</th>
                                <th class="text-muted">Số Lượng</th>
                                <th class="text-muted">Doanh Thu</th>
                                <th class="text-muted">Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuantity = $topDishes->sum('total_quantity') ?: 1;
                            @endphp
                            
                            @forelse($topDishes as $index => $dish)
                            @php
                                $percentage = ($dish->total_quantity / $totalQuantity) * 100;
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    @if($index < 3)
                                        <span class="badge bg-warning">🏆 #{{ $index + 1 }}</span>
                                    @else
                                        <span class="badge bg-secondary">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td><a href="#" class="text-muted fw-bold">{{ $dish->name }}</a></td>
                                <td><span class="badge badge-soft-primary">{{ $dish->total_quantity }}</span></td>
                                <td><span class="text-success fw-bold">{{ number_format($dish->total_revenue) }} VNĐ</span></td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 60px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round($percentage, 1) }}%</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bx bx-info-circle me-2"></i>Chưa có dữ liệu món ăn
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Danh mục bán chạy -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📊 Danh Mục Bán Chạy</h5>
                    <div class="mt-3">
                        @php
                            $totalCategoryQuantity = collect($categoryQuantityData)->sum() ?: 1;
                        @endphp
                        
                        @foreach($categoryLabels as $index => $categoryName)
                            @php
                                $quantity = $categoryQuantityData[$index] ?? 0;
                                $percentage = ($quantity / $totalCategoryQuantity) * 100;
                                $colors = ['primary', 'success', 'warning', 'info', 'secondary'];
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">{{ $categoryName }}</span>
                                    <span class="badge bg-{{ $color }}">{{ $quantity }} món</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ round($percentage, 1) }}% tổng số lượng</small>
                            </div>
                        @endforeach
                        
                        @if(empty($categoryLabels))
                            <div class="text-center text-muted py-3">
                                <i class="bx bx-category fs-24 mb-2"></i>
                                <p class="mb-0">Chưa có dữ liệu danh mục</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng tần suất sử dụng bàn -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title">🪑 Tần Suất Sử Dụng Bàn</h4>
                        <span class="badge bg-info">{{ count($tableUsage) }} bàn</span>
                    </div>
                </div>
                <div class="table-responsive table-centered">
                    <table class="table mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-3">Số Bàn</th>
                                <th>Khu Vực</th>
                                <th>Tầng</th>
                                <th>Sức Chứa</th>
                                <th>Lần Sử Dụng</th>
                                <th>Doanh Thu</th>
                                <th>TB/Lần</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableUsage as $table)
                            <tr>
                                <td class="ps-3">
                                    <a href="#!" class="fw-bold">{{ $table->table_number }}</a>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">{{ $table->area_code ?? $table->area_name }}</span>
                                    <small class="d-block text-muted">{{ $table->area_name }}</small>
                                </td>
                                <td>
                                    @if($table->floor)
                                        <span class="badge bg-info">Tầng {{ $table->floor }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-secondary">{{ $table->capacity ?? 'N/A' }}</span>
                                    @if($table->table_type)
                                        <small class="d-block text-muted">{{ $table->table_type }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $table->usage_count }}</span>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">{{ number_format($table->total_revenue) }} VNĐ</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ number_format($table->usage_count > 0 ? $table->total_revenue / $table->usage_count : 0) }} VNĐ</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bx bx-info-circle me-2"></i>Chưa có dữ liệu sử dụng bàn
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(count($tableUsage) > 0)
                <div class="card-footer border-top">
                    <div class="row g-3">
                        <div class="col-sm">
                            <div class="text-muted">
                                Hiển thị <span class="fw-semibold">{{ count($tableUsage) }}</span> bàn
                                | Tổng lượt sử dụng: <span class="fw-semibold">{{ $tableUsage->sum('usage_count') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
    }
    
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endsection