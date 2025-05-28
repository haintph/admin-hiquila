@extends('admin.layouts.master')

@section('title', 'Chi tiết đặt bàn')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Chi tiết đặt bàn - Bàn {{ $table->table_number }}</h1>
            <div>
                <a href="{{ route('staff.reservations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Thông tin bàn -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-utensils"></i> Thông tin bàn
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="font-weight-bold">Số bàn:</td>
                                <td><span class="badge badge-primary badge-lg">{{ $table->table_number }}</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Khu vực:</td>
                                <td>{{ $table->area->name ?? 'Không xác định' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Loại bàn:</td>
                                <td>{{ $table->table_type }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Sức chứa:</td>
                                <td>{{ $table->capacity }} người</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Trạng thái:</td>
                                <td>
                                    @if ($table->status == 'Đã đặt')
                                        <span class="badge badge-success">{{ $table->status }}</span>
                                    @elseif($table->status == 'Đến muộn')
                                        <span class="badge badge-warning">{{ $table->status }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $table->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($table->min_spend)
                                <tr>
                                    <td class="font-weight-bold">Chi tiêu tối thiểu:</td>
                                    <td>{{ number_format($table->min_spend) }} VNĐ</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-user"></i> Thông tin khách hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="font-weight-bold">Tên khách hàng:</td>
                                <td><strong class="text-primary">{{ $table->reserved_by }}</strong></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Số điện thoại:</td>
                                <td>
                                    <a href="tel:{{ $table->reserved_phone }}" class="text-success">
                                        <i class="fas fa-phone"></i> {{ $table->reserved_phone }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Số người:</td>
                                <td>
                                    <span class="badge badge-info">{{ $table->reserved_party_size ?? 0 }} người</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Thời gian đặt:</td>
                                <td>
                                    <strong class="text-danger">
                                        {{ $table->reserved_time ? $table->reserved_time->format('d/m/Y H:i') : 'Chưa xác định' }}
                                    </strong><br>
                                    @if ($table->reserved_time)
                                        <small class="text-muted">{{ $table->reserved_time->diffForHumans() }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Đặt lúc:</td>
                                <td>{{ $table->reserved_at ? $table->reserved_at->format('d/m/Y H:i') : 'Chưa xác định' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ghi chú -->
        @if ($table->reservation_notes)
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-sticky-note"></i> Ghi chú từ khách hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $table->reservation_notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hành động -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-cogs"></i> Hành động
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (in_array($table->status, ['Đã đặt', 'Đến muộn']))
                            <div class="row">
                                <!-- Nút Check-in - chỉ hiển thị khi trạng thái "Đã đặt" -->
                                @if ($table->status == 'Đã đặt')
                                    <div class="col-md-4 mb-3">
                                        <form method="POST"
                                            action="{{ route('staff.reservations.checkin', $table->table_id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-lg btn-block"
                                                onclick="return confirm('Xác nhận check-in khách hàng vào bàn {{ $table->table_number }}?')">
                                                <i class="fas fa-check"></i> Check-in khách hàng
                                            </button>
                                        </form>
                                        <small class="text-muted">Chuyển trạng thái bàn sang "Đang phục vụ"</small>
                                    </div>
                                @endif

                                <!-- Nút Hủy đặt bàn - hiển thị cho cả hai trạng thái -->
                                <div class="col-md-4 mb-3">
                                    <form method="POST"
                                        action="{{ route('staff.reservations.cancel', $table->table_id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-lg btn-block"
                                            onclick="return confirm('Xác nhận hủy đặt bàn {{ $table->table_number }}?\n\nBàn sẽ chuyển về trạng thái trống.')">
                                            <i class="fas fa-times"></i> Hủy đặt bàn
                                        </button>
                                    </form>
                                    <small class="text-muted">Hủy đặt bàn và chuyển về trạng thái trống</small>
                                </div>

                                <!-- Nút Gọi điện - luôn hiển thị -->
                                <div class="col-md-4 mb-3">
                                    <a href="tel:{{ $table->reserved_phone }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fas fa-phone"></i> Gọi điện cho khách
                                    </a>
                                    <small class="text-muted">Liên hệ trực tiếp với khách hàng</small>
                                </div>
                            </div>

                            <!-- Thông báo đặc biệt cho trạng thái "Đến muộn" -->
                            @if ($table->status == 'Đến muộn')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i>
                                    <strong>Khách hàng đã đến muộn!</strong><br>
                                    Thời gian đặt: {{ $table->reserved_time->format('d/m/Y H:i') }}<br>
                                    Muộn khoảng: {{ $table->reserved_time->diffForHumans() }}<br>
                                    <small class="text-muted">Bạn có thể gọi điện xác nhận hoặc hủy đặt bàn nếu cần.</small>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Bàn này hiện tại không có đặt trước hoặc đã được xử lý.
                            </div>
                        @endif

                        <!-- Quick actions -->
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('staff.reservations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-list"></i> Danh sách đặt bàn
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button onclick="window.print()" class="btn btn-outline-primary">
                                    <i class="fas fa-print"></i> In thông tin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline (Optional) -->
        @if ($table->reserved_time)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">
                                <i class="fas fa-history"></i> Thời gian biểu
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Thời điểm đặt bàn -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Đặt bàn</h6>
                                        <p class="timeline-text">
                                            {{ $table->reserved_at ? $table->reserved_at->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Thời gian đến dự kiến -->
                                <div class="timeline-item">
                                    <div
                                        class="timeline-marker {{ $table->reserved_time->isPast() ? ($table->status == 'Đến muộn' ? 'bg-danger' : 'bg-warning') : 'bg-info' }}">
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Thời gian đến dự kiến</h6>
                                        <p class="timeline-text">
                                            {{ $table->reserved_time->format('d/m/Y H:i') }}
                                            @if ($table->reserved_time->isPast())
                                                @if ($table->status == 'Đến muộn')
                                                    <span class="badge bg-danger text-white ml-2">
                                                        <i class="fas fa-clock"></i> Đến muộn
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark ml-2">Đã qua</span>
                                                @endif
                                            @else
                                                <span
                                                    class="badge bg-info text-white ml-2">{{ $table->reserved_time->diffForHumans() }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Thời điểm được đánh dấu đến muộn -->
                                @if ($table->status == 'Đến muộn')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-danger"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title text-danger">
                                                <i class="fas fa-clock"></i> Đánh dấu đến muộn
                                            </h6>
                                            <p class="timeline-text">
                                                <span class="text-danger">
                                                    Đã muộn {{ $table->reserved_time->diffForHumans() }}
                                                </span><br>
                                                <small class="text-muted">
                                                    Hệ thống tự động đánh dấu sau 15 phút
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Check-in (nếu đã check-in) -->
                                @if ($table->status == 'Đang phục vụ' && $table->occupied_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Check-in</h6>
                                            <p class="timeline-text">
                                                {{ $table->occupied_at->format('d/m/Y H:i') }}
                                                @if ($table->reserved_time)
                                                    <br><small class="text-muted">
                                                        @if ($table->occupied_at->greaterThan($table->reserved_time))
                                                            Muộn
                                                            {{ $table->reserved_time->diffInMinutes($table->occupied_at) }}
                                                            phút
                                                        @else
                                                            Đúng giờ
                                                        @endif
                                                    </small>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Trạng thái hiện tại -->
                                <div class="timeline-item">
                                    <div
                                        class="timeline-marker 
                @if ($table->status == 'Đã đặt') bg-success
                @elseif($table->status == 'Đến muộn') bg-danger
                @elseif($table->status == 'Đang phục vụ') bg-primary
                @else bg-secondary @endif">
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Trạng thái hiện tại</h6>
                                        <p class="timeline-text">
                                            <span
                                                class="badge 
                        @if ($table->status == 'Đã đặt') bg-success text-white
                        @elseif($table->status == 'Đến muộn') bg-danger text-white
                        @elseif($table->status == 'Đang phục vụ') bg-primary text-white
                        @else bg-secondary text-white @endif">
                                                {{ $table->status }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                Cập nhật: {{ now()->format('H:i d/m/Y') }}
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-marker {
            position: absolute;
            left: -2rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .timeline-text {
            color: #6c757d;
            margin-bottom: 0;
        }

        @media print {

            .btn,
            .timeline-item:before,
            .card-header {
                display: none !important;
            }
        }
    </style>
@endsection
