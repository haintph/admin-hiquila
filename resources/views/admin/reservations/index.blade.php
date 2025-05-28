@extends('admin.layouts.master')

@section('title', 'Quản lý đặt bàn')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Quản lý đặt bàn</h1>
            <div>
                <button onclick="location.reload()" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- Thống kê nhanh -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng đặt bàn
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['total_reservations'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Hôm nay
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['today_reservations'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Đến muộn
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['late_arrivals'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Sắp tới
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['upcoming_reservations'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tìm kiếm và lọc -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm & Lọc</h6>
            </div>
            <div class="card-body">
                @php
                    // Sử dụng URL trực tiếp thay vì route names
                    $currentUrl = '/admin/reservations';
                @endphp
                <form method="GET" action="{{ $currentUrl }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search_name" class="form-label">Tên khách hàng</label>
                            <input type="text" class="form-control" id="search_name" name="search_name"
                                value="{{ request('search_name') }}" placeholder="Nhập tên khách hàng...">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="search_phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="search_phone" name="search_phone"
                                value="{{ request('search_phone') }}" placeholder="Nhập số điện thoại...">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="Đã đặt" {{ request('status') == 'Đã đặt' ? 'selected' : '' }}>Đã đặt</option>
                                <option value="Đến muộn" {{ request('status') == 'Đến muộn' ? 'selected' : '' }}>Đến muộn
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="date" class="form-label">Ngày</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ request('date') }}">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="area_id" class="form-label">Khu vực</label>
                            <select class="form-control" id="area_id" name="area_id">
                                <option value="">Tất cả</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->area_id }}"
                                        {{ request('area_id') == $area->area_id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ $currentUrl }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Xóa lọc
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tìm kiếm nhanh -->
        <div class="card shadow mb-4">
            <div class="card-body">
                @php
                    $quickSearchUrl = '/admin/reservations/search/quick';
                @endphp
                <form method="GET" action="{{ $quickSearchUrl }}" class="d-flex">
                    <input type="text" class="form-control me-2" name="q"
                        placeholder="Tìm nhanh theo tên, SĐT, hoặc số bàn..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Danh sách đặt bàn -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Danh sách đặt bàn
                    @if (isset($stats['search_term']))
                        <small class="text-muted">(Kết quả tìm kiếm: "{{ $stats['search_term'] }}")</small>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @if ($reservations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bàn</th>
                                    <th>Khách hàng</th>
                                    <th>Liên hệ</th>
                                    <th>Thời gian đặt</th>
                                    <th>Số người</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservations as $table)
                                    <tr class="{{ $table->status == 'Đến muộn' ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $table->table_number }}</strong><br>
                                            <small class="text-muted">{{ $table->area->name ?? 'N/A' }}</small><br>
                                            <small class="text-muted">{{ $table->table_type }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $table->reserved_by }}</strong><br>
                                            <small class="text-muted">
                                                Đặt lúc:
                                                {{ $table->reserved_at ? $table->reserved_at->format('d/m H:i') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone"></i> {{ $table->reserved_phone }}<br>
                                            @if ($table->reservation_notes)
                                                <small class="text-info">
                                                    <i class="fas fa-sticky-note"></i>
                                                    {{ Str::limit($table->reservation_notes, 30) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                {{ $table->reserved_time ? $table->reserved_time->format('d/m/Y') : 'N/A' }}
                                            </strong><br>
                                            <strong class="text-success">
                                                {{ $table->reserved_time ? $table->reserved_time->format('H:i') : 'N/A' }}
                                            </strong><br>
                                            @if ($table->reserved_time)
                                                <small class="text-muted">
                                                    {{ $table->reserved_time->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $table->reserved_party_size ?? 0 }}
                                                người</span><br>
                                            <small class="text-muted">Sức chứa: {{ $table->capacity }}</small>
                                        </td>
                                        <td>
                                            @if ($table->status == 'Đã đặt')
                                                <span class="badge bg-success">{{ $table->status }}</span>
                                            @elseif($table->status == 'Đến muộn')
                                                <span class="badge bg-warning text-dark">{{ $table->status }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $table->status }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $showUrl = "/admin/reservations/{$table->table_id}";
                                                $checkinUrl = "/admin/reservations/{$table->table_id}/checkin";
                                                $cancelUrl = "/admin/reservations/{$table->table_id}/cancel";
                                            @endphp
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <a href="{{ $showUrl }}"
                                                    class="btn btn-info btn-sm mb-1">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>

                                                @if (in_array($table->status, ['Đã đặt', 'Đến muộn']))
                                                    <form method="POST"
                                                        action="{{ $checkinUrl }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm mb-1 w-100"
                                                            onclick="return confirm('Xác nhận check-in khách hàng?')">
                                                            <i class="fas fa-check"></i> Check-in
                                                        </button>
                                                    </form>

                                                    <form method="POST"
                                                        action="{{ $cancelUrl }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm w-100"
                                                            onclick="return confirm('Xác nhận hủy đặt bàn?')">
                                                            <i class="fas fa-times"></i> Hủy
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $reservations->firstItem() ?? 0 }} - {{ $reservations->lastItem() ?? 0 }}
                                trong tổng số {{ $reservations->total() }} đặt bàn
                            </small>
                        </div>
                        <div>
                            {{ $reservations->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy đặt bàn nào</h5>
                        <p class="text-muted">Thử thay đổi điều kiện tìm kiếm hoặc xóa bộ lọc</p>
                        <a href="{{ $currentUrl }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Xem tất cả đặt bàn
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto refresh every 2 minutes
        setInterval(function() {
            if (!document.hidden) {
                location.reload();
            }
        }, 120000);

        // Highlight search terms
        $(document).ready(function() {
            var searchName = '{{ request('search_name') }}';
            var searchPhone = '{{ request('search_phone') }}';

            if (searchName) {
                $('td').highlight(searchName);
            }
            if (searchPhone) {
                $('td').highlight(searchPhone);
            }
        });
    </script>
@endpush