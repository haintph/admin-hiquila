@php
// Kiểm tra xem đây là form tạo mới hay chỉnh sửa
$isEdit = isset($area);
@endphp

@extends('admin.layouts.master')

@section('title', $isEdit ? 'Chỉnh sửa khu vực: ' . $area->name : 'Thêm khu vực mới')

@section('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    
    .input-group-text.icon-container {
        width: 45px;
        justify-content: center;
    }
    
    .form-text {
        margin-top: 0.25rem;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .card {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }
    
    .table-card {
        border: 1px solid rgba(0,0,0,.05);
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    
    .table-card:hover {
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.1);
    }

    .area-rules {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .rule-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .rule-item:last-child {
        margin-bottom: 0;
    }

    .rule-icon {
        margin-right: 0.5rem;
        opacity: 0.8;
    }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="navbar-header mb-3">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">
                        {{ $isEdit ? 'Chỉnh Sửa Khu Vực' : 'Thêm Khu Vực' }}
                    </h4>
                </div>
                @if($isEdit && ($area->tables_count ?? $area->tables()->count() > 0))
                <div class="ms-3">
                    <span class="badge bg-info-subtle text-info px-3 py-2 fs-12">
                        <iconify-icon icon="solar:table-2-broken" class="me-1 fs-14"></iconify-icon>
                        {{ $area->tables_count ?? $area->tables()->count() }} bàn
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Hiển thị lỗi từ Laravel Validator -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex">
                <div class="me-2">
                    <iconify-icon icon="solar:danger-triangle-bold" style="font-size: 24px;"></iconify-icon>
                </div>
                <div>
                    <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Hiển thị thông báo từ session -->
        @if(session('error'))
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

        <!-- Quy tắc tạo khu vực -->
        @if(!$isEdit)
        <div class="area-rules">
            <h6 class="mb-3">
                <iconify-icon icon="solar:info-circle-bold" class="me-2"></iconify-icon>
                Quy tắc tạo khu vực
            </h6>
            <div class="rule-item">
                <iconify-icon icon="solar:buildings-2-broken" class="rule-icon"></iconify-icon>
                <span>Tối đa 3 tầng (Tầng 1, 2, 3)</span>
            </div>
            <div class="rule-item">
                <iconify-icon icon="solar:sort-horizontal-broken" class="rule-icon"></iconify-icon>
                <span>Mỗi tầng phải tạo khu A trước, rồi mới đến B, C...</span>
            </div>
            <div class="rule-item">
                <iconify-icon icon="solar:hashtag-bold" class="rule-icon"></iconify-icon>
                <span>Mã khu vực: A, B, C... (chỉ một chữ cái)</span>
            </div>
            <div class="rule-item">
                <iconify-icon icon="solar:table-2-broken" class="rule-icon"></iconify-icon>
                <span>Bàn trong khu: A1, A2, A3... (khu A) | B1, B2, B3... (khu B)</span>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary-subtle">
                        <h5 class="card-title mb-0 text-primary">
                            <iconify-icon icon="solar:info-circle-broken" class="fs-22 me-2"></iconify-icon>
                            Thông Tin Khu Vực{{ $isEdit ? ' #' . $area->area_id : '' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ $isEdit ? route('areas.update', $area->area_id) : route('areas.store') }}" 
                              method="POST" enctype="multipart/form-data" id="areaForm">
                            @csrf
                            @if($isEdit) @method('PUT') @endif
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tầng <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-primary-subtle text-primary">
                                            <iconify-icon icon="solar:square-top-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <select class="form-select @error('floor') is-invalid @enderror" 
                                                name="floor" id="floorSelect" required {{ $isEdit ? '' : 'onchange="updateAvailableAreas()"' }}>
                                            <option value="">Chọn tầng</option>
                                            <option value="1" {{ old('floor', $area->floor ?? '') == '1' ? 'selected' : '' }}>Tầng 1</option>
                                            <option value="2" {{ old('floor', $area->floor ?? '') == '2' ? 'selected' : '' }}>Tầng 2</option>
                                            <option value="3" {{ old('floor', $area->floor ?? '') == '3' ? 'selected' : '' }}>Tầng 3</option>
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">Tối đa 3 tầng</small>
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã khu vực <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-primary-subtle text-primary">
                                            <iconify-icon icon="solar:hashtag-bold" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               name="code" id="codeInput" value="{{ old('code', $area->code ?? '') }}" required
                                               placeholder="A, B, C..."
                                               maxlength="1"
                                               pattern="[A-Z]"
                                               style="text-transform: uppercase;"
                                               {{ $isEdit ? '' : 'oninput="validateAreaCode()"' }}>
                                    </div>
                                    <div id="codeHelp" class="form-text text-muted">
                                        @if(!$isEdit)
                                            Chọn tầng trước để xem khu vực có thể tạo
                                        @else
                                            Mã khu vực (một chữ cái: A, B, C...) - Bàn sẽ có số: A1, A2, A3...
                                        @endif
                                    </div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên khu vực <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-primary-subtle text-primary">
                                            <iconify-icon icon="solar:pen-2-linear" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name', $area->name ?? '') }}" 
                                               placeholder="Nhập tên khu vực" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="Hoạt động" {{ old('status', $area->status ?? '') == 'Hoạt động' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="Bảo trì" {{ old('status', $area->status ?? '') == 'Bảo trì' ? 'selected' : '' }}>
                                            Bảo trì
                                        </option>
                                        <option value="Đóng cửa" {{ old('status', $area->status ?? '') == 'Đóng cửa' ? 'selected' : '' }}>
                                            Đóng cửa
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              name="description" rows="2" 
                                              placeholder="Mô tả ngắn về khu vực">{{ old('description', $area->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sức chứa (người)</label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-primary-subtle text-primary">
                                            <iconify-icon icon="solar:users-group-rounded-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                               name="capacity" value="{{ old('capacity', $area->capacity ?? '') }}" 
                                               placeholder="Số người tối đa có thể chứa" min="0" max="20">
                                    </div>
                                    <small class="form-text text-muted">Tối đa 20 người mỗi khu vực</small>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phụ thu (đơn vị tiền)</label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-primary-subtle text-primary">
                                            <iconify-icon icon="solar:dollar-minimalistic-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="number" step="0.01" class="form-control @error('surcharge') is-invalid @enderror" 
                                               name="surcharge" value="{{ old('surcharge', $area->surcharge ?? 0) }}" 
                                               placeholder="Phụ thu cho khu vực này" min="0">
                                    </div>
                                    <small class="form-text text-muted">Phụ thu thêm khi khách hàng sử dụng khu vực này</small>
                                    @error('surcharge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cho phép hút thuốc? <span class="text-danger">*</span></label>
                                    <select class="form-select @error('is_smoking') is-invalid @enderror" name="is_smoking" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="0" {{ old('is_smoking', $area->is_smoking ?? '') === '0' || old('is_smoking', $area->is_smoking ?? '') === 0 ? 'selected' : '' }}>
                                            Không
                                        </option>
                                        <option value="1" {{ old('is_smoking', $area->is_smoking ?? '') === '1' || old('is_smoking', $area->is_smoking ?? '') === 1 ? 'selected' : '' }}>
                                            Có
                                        </option>
                                    </select>
                                    @error('is_smoking')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Khu VIP? <span class="text-danger">*</span></label>
                                    <select class="form-select @error('is_vip') is-invalid @enderror" name="is_vip" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="0" {{ old('is_vip', $area->is_vip ?? '') === '0' || old('is_vip', $area->is_vip ?? '') === 0 ? 'selected' : '' }}>
                                            Không
                                        </option>
                                        <option value="1" {{ old('is_vip', $area->is_vip ?? '') === '1' || old('is_vip', $area->is_vip ?? '') === 1 ? 'selected' : '' }}>
                                            Có
                                        </option>
                                    </select>
                                    @error('is_vip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        {{ $isEdit ? 'Hình ảnh hiện tại' : 'Hình ảnh' }}
                                    </label>
                                    
                                    @if($isEdit && $area->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $area->image) }}" alt="{{ $area->name }}" 
                                             class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    @endif
                                    
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           name="image" accept="image/*">
                                    <small class="form-text text-muted">
                                        {{ $isEdit ? 'Tải lên hình mới để thay thế (nếu muốn).' : '' }} 
                                        Hỗ trợ định dạng: JPG, PNG, GIF (< 2MB)
                                    </small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($isEdit && $area->hourSetting && $area->hourSetting->has_operating_hours)
                                <div class="col-12">
                                    <hr class="text-muted">
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <iconify-icon icon="solar:clock-circle-bold" style="font-size: 24px;"></iconify-icon>
                                            </div>
                                            <div>
                                                <h5 class="alert-heading">Giờ hoạt động</h5>
                                                <p>Khu vực này có {{ $area->operatingHours->count() }} khung giờ hoạt động.</p>
                                                <hr>
                                                <a href="{{ route('areas.manageHours', $area->area_id) }}" class="btn btn-sm btn-info">
                                                    <iconify-icon icon="solar:clock-circle-broken" class="me-1"></iconify-icon>
                                                    Quản lý giờ hoạt động
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('areas.index') }}" class="btn btn-secondary">
                                    <iconify-icon icon="solar:arrow-left-broken" class="me-1"></iconify-icon> 
                                    Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <iconify-icon icon="{{ $isEdit ? 'solar:disk-broken' : 'solar:add-circle-broken' }}" class="me-1"></iconify-icon> 
                                    {{ $isEdit ? 'Lưu Thay Đổi' : 'Thêm Khu Vực' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                @if($isEdit && $area->tables()->count() > 0)
                <div class="card mt-4">
                    <div class="card-header bg-info-subtle">
                        <h5 class="card-title mb-0 text-info">
                            <iconify-icon icon="solar:table-2-broken" class="fs-22 me-2"></iconify-icon>
                            Bàn trong khu vực này ({{ $area->tables()->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($area->tables as $table)
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card h-100 table-card {{ $table->status == 'Trống' ? 'border-success' : ($table->status == 'Đang phục vụ' ? 'border-warning' : 'border-secondary') }}">
                                    <div class="card-body p-3">
                                        <h5 class="card-title mb-1">Bàn {{ $table->table_number }}</h5>
                                        <p class="card-text mb-1 small">
                                            <span class="badge {{ $table->status == 'Trống' ? 'bg-success-subtle text-success' : 
                                             ($table->status == 'Đang phục vụ' ? 'bg-warning-subtle text-warning' : 
                                             'bg-secondary-subtle text-secondary') }}">
                                                {{ $table->status }}
                                            </span>
                                        </p>
                                        <p class="card-text mb-0 small">
                                            <iconify-icon icon="solar:users-group-rounded-broken" class="me-1"></iconify-icon> {{ $table->capacity }} người<br>
                                            <iconify-icon icon="solar:square-academic-cap-broken" class="me-1"></iconify-icon> {{ $table->table_type }}
                                        </p>
                                    </div>
                                    <div class="card-footer p-2 bg-transparent">
                                        <a href="{{ route('tables.edit', $table->table_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                            <iconify-icon icon="solar:pen-2-broken" class="me-1"></iconify-icon> Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-3">
                            @if(Route::has('areas.manageLayout'))
                            <a href="{{ route('areas.manageLayout', $area->area_id) }}" class="btn btn-info">
                                <iconify-icon icon="solar:table-broken" class="me-1"></iconify-icon> Quản lý bố trí bàn
                            </a>
                            @endif
                            <a href="{{ route('tables.create') }}?area_id={{ $area->area_id }}" class="btn btn-outline-primary ms-2">
                                <iconify-icon icon="solar:add-circle-broken" class="me-1"></iconify-icon> Thêm bàn mới
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@if(!$isEdit)
<script>
let floorAreaData = {};

// Cập nhật khu vực có thể tạo khi chọn tầng
function updateAvailableAreas() {
    const floor = document.getElementById('floorSelect').value;
    const codeInput = document.getElementById('codeInput');
    const codeHelp = document.getElementById('codeHelp');
    
    if (!floor) {
        codeHelp.innerHTML = 'Chọn tầng trước để xem khu vực có thể tạo';
        codeHelp.className = 'form-text text-muted';
        codeInput.value = '';
        return;
    }

    // Gọi API để lấy thông tin khu vực có thể tạo
    fetch(`{{ url('/admin/areas/floor') }}/${floor}/available`)
        .then(response => response.json())
        .then(data => {
            floorAreaData[floor] = data;
            updateCodeHelp(floor, data);
        })
        .catch(error => {
            console.error('Error:', error);
            codeHelp.innerHTML = 'Có lỗi xảy ra khi tải thông tin';
            codeHelp.className = 'form-text text-danger';
        });
}

function updateCodeHelp(floor, data) {
    const codeHelp = document.getElementById('codeHelp');
    
    if (!data.can_create) {
        codeHelp.innerHTML = `<strong>Tầng ${floor} đã đầy!</strong> Không thể tạo thêm khu vực.`;
        codeHelp.className = 'form-text text-warning';
        return;
    }

    let helpText = `<strong>Tầng ${floor}:</strong> `;
    
    if (data.existing_areas.length === 0) {
        helpText += 'Chưa có khu vực nào. Bắt đầu với khu <strong>A</strong>';
        helpText += '<br><small class="text-muted">Bàn sẽ là: A1, A2, A3...</small>';
    } else {
        helpText += `Đã có: ${data.existing_areas.join(', ')}. Tiếp theo có thể tạo: <strong>${data.next_available}</strong>`;
        if (data.table_format_example) {
            helpText += `<br><small class="text-muted">Bàn trong khu ${data.next_available}: ${data.table_format_example}</small>`;
        }
    }
    
    codeHelp.innerHTML = helpText;
    codeHelp.className = 'form-text text-info';
}

function validateAreaCode() {
    const floor = document.getElementById('floorSelect').value;
    const code = document.getElementById('codeInput').value.trim().toUpperCase();
    const codeInput = document.getElementById('codeInput');
    const codeHelp = document.getElementById('codeHelp');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!floor || !code) {
        submitBtn.disabled = false;
        return;
    }

    // Cập nhật giá trị input
    codeInput.value = code;

    const data = floorAreaData[floor];
    if (!data) {
        return;
    }

    // Kiểm tra format code - chỉ cho phép một chữ cái
    const codePattern = /^[A-Z]$/;
    if (!codePattern.test(code)) {
        codeHelp.innerHTML = 'Mã khu vực phải là một chữ cái: A, B, C, D...';
        codeHelp.className = 'form-text text-danger';
        codeInput.classList.add('is-invalid');
        submitBtn.disabled = true;
        return;
    }

    const areaLetter = code;
    
    // Kiểm tra thứ tự
    if (data.next_available && areaLetter !== data.next_available) {
        if (data.existing_areas.includes(areaLetter)) {
            codeHelp.innerHTML = `Khu vực ${areaLetter} đã tồn tại trên tầng ${floor}`;
            codeHelp.className = 'form-text text-danger';
        } else {
            codeHelp.innerHTML = `Phải tạo khu vực ${data.next_available} trước khi tạo khu vực ${areaLetter} trên tầng ${floor}`;
            codeHelp.className = 'form-text text-danger';
        }
        codeInput.classList.add('is-invalid');
        submitBtn.disabled = true;
        return;
    }

    // Hợp lệ
    codeInput.classList.remove('is-invalid');
    codeInput.classList.add('is-valid');
    updateCodeHelp(floor, data);
    submitBtn.disabled = false;
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const floor = document.getElementById('floorSelect').value;
    if (floor) {
        updateAvailableAreas();
    }
});
</script>
@endif
@endsection