@extends('admin.layouts.master')

@section('title', 'Quản lý giờ hoạt động')

@section('styles')
<style>
    .time-slot-item {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-active {
        background-color: #198754;
    }

    .status-inactive {
        background-color: #dc3545;
    }
    
    /* Thêm hiệu ứng hover cho các nút */
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: white;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }
    
    /* Đảm bảo các icon có cùng kích thước */
    .fs-12 {
        font-size: 12px;
    }
    
    .fs-18 {
        font-size: 18px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-xl-12">
            <!-- Hiển thị lỗi từ Laravel Validator -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Hiển thị thông báo từ session -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Thành công!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Quản lý giờ hoạt động - {{ $area->name }}</h4>
                    <a href="{{ route('areas.index') }}" class="btn btn-sm btn-outline-secondary">
                        <iconify-icon icon="solar:arrow-left-broken" class="me-1"></iconify-icon>
                        Quay lại
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('areas.updateHours', $area->area_id) }}" method="POST" id="operatingHoursForm">
                        @csrf
                        
                        <!-- Thêm hidden field để luôn gửi has_operating_hours=1 -->
                        <input type="hidden" name="has_operating_hours" value="1">
                        
                        <div class="card bg-light p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Khung giờ hoạt động</h6>
                                <a href="{{ route('areas.addTimeSlot', $area->area_id) }}" class="btn btn-sm btn-outline-primary">
                                    <iconify-icon icon="solar:add-circle-broken" class="me-1"></iconify-icon>
                                    Thêm khung giờ
                                </a>
                            </div>

                            <div id="time_slots_container">
                                @if($area->operatingHours && $area->operatingHours->count() > 0)
                                    @foreach($area->operatingHours as $index => $timeSlot)
                                        <div class="time-slot-item mb-2">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-success-subtle text-success">Từ</span>
                                                        <input type="time" class="form-control" 
                                                            name="operating_hours[{{ $index }}][start_time]" 
                                                            value="{{ $timeSlot->start_time }}">
                                                    </div>
                                                </div>
                                                <div class="col-5">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-danger-subtle text-danger">Đến</span>
                                                        <input type="time" class="form-control" 
                                                            name="operating_hours[{{ $index }}][end_time]" 
                                                            value="{{ $timeSlot->end_time }}">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <a href="{{ route('areas.removeTimeSlot', ['area' => $area->area_id, 'timeSlotId' => $timeSlot->id]) }}" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa khung giờ này?')">
                                                        <iconify-icon icon="solar:trash-bin-trash-broken" class="fs-12"></iconify-icon>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-center">
                                            <iconify-icon icon="solar:info-circle-bold" style="font-size: 24px; margin-right: 10px;"></iconify-icon>
                                            <div>
                                                Chưa có khung giờ hoạt động nào. 
                                                <a href="{{ route('areas.addTimeSlot', $area->area_id) }}" class="alert-link">Thêm khung giờ đầu tiên</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="non_operating_status" class="form-label">Trạng thái ngoài giờ hoạt động</label>
                            <select class="form-select" id="non_operating_status" name="non_operating_status">
                                <option value="Bảo trì" {{ $area->hourSetting && $area->hourSetting->non_operating_status == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
                                <option value="Đóng cửa" {{ !$area->hourSetting || $area->hourSetting->non_operating_status == 'Đóng cửa' ? 'selected' : '' }}>Đóng cửa</option>
                            </select>
                            <small class="form-text text-muted">
                                Trạng thái của khu vực khi ngoài giờ hoạt động
                            </small>
                        </div>
                        
                        <div class="mt-4 text-end">
                            <a href="{{ route('areas.index') }}" class="btn btn-outline-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection