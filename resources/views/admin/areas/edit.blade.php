@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa khu vực: ' . $area->name)

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

    .current-info {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-icon {
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
                        Chỉnh Sửa Khu Vực
                    </h4>
                </div>
                @if($area->tables_count ?? $area->tables()->count() > 0)
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

        @if(session('success'))
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

        <!-- Thông tin hiện tại -->
        <div class="current-info">
            <h6 class="mb-3">
                <iconify-icon icon="solar:info-circle-bold" class="me-2"></iconify-icon>
                Thông tin hiện tại của khu vực
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <iconify-icon icon="solar:hashtag-bold" class="info-icon"></iconify-icon>
                        <span><strong>Mã:</strong> {{ $area->code }}</span>
                    </div>
                    <div class="info-item">
                        <iconify-icon icon="solar:buildings-2-broken" class="info-icon"></iconify-icon>
                        <span><strong>Tầng:</strong> {{ $area->floor ?? 'Chưa xác định' }}</span>
                    </div>
                    <div class="info-item">
                        <iconify-icon icon="solar:users-group-rounded-broken" class="info-icon"></iconify-icon>
                        <span><strong>Sức chứa:</strong> {{ $area->capacity ?? 'Chưa xác định' }} người</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <iconify-icon icon="solar:check-circle-broken" class="info-icon"></iconify-icon>
                        <span><strong>Trạng thái:</strong> {{ $area->status }}</span>
                    </div>
                    <div class="info-item">
                        <iconify-icon icon="solar:smoking-broken" class="info-icon"></iconify-icon>
                        <span><strong>Hút thuốc:</strong> {{ $area->is_smoking ? 'Cho phép' : 'Không cho phép' }}</span>
                    </div>
                    <div class="info-item">
                        <iconify-icon icon="solar:crown-broken" class="info-icon"></iconify-icon>
                        <span><strong>VIP:</strong> {{ $area->is_vip ? 'Có' : 'Không' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-warning-subtle">
                        <h5 class="card-title mb-0 text-warning">
                            <iconify-icon icon="solar:pen-2-broken" class="fs-22 me-2"></iconify-icon>
                            Chỉnh Sửa Thông Tin Khu Vực #{{ $area->area_id }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('areas.update', $area->area_id) }}" 
                              method="POST" enctype="multipart/form-data" id="areaEditForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tầng <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-warning-subtle text-warning">
                                            <iconify-icon icon="solar:square-top-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <select class="form-select @error('floor') is-invalid @enderror" 
                                                name="floor" id="floorSelect" required onchange="updateAvailableAreas()">
                                            <option value="">Chọn tầng</option>
                                            <option value="1" {{ old('floor', $area->floor) == '1' ? 'selected' : '' }}>Tầng 1</option>
                                            <option value="2" {{ old('floor', $area->floor) == '2' ? 'selected' : '' }}>Tầng 2</option>
                                            <option value="3" {{ old('floor', $area->floor) == '3' ? 'selected' : '' }}>Tầng 3</option>
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
                                        <span class="input-group-text icon-container bg-warning-subtle text-warning">
                                            <iconify-icon icon="solar:hashtag-bold" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               name="code" id="codeInput" value="{{ old('code', $area->code) }}" required
                                               placeholder="A, B, C..."
                                               maxlength="1"
                                               pattern="[A-Z]"
                                               style="text-transform: uppercase;"
                                               oninput="validateAreaCode()">
                                    </div>
                                    <div id="codeHelp" class="form-text text-muted">
                                        Mã khu vực (một chữ cái: A, B, C...) - Bàn sẽ có số: A1, A2, A3...
                                    </div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên khu vực <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text icon-container bg-warning-subtle text-warning">
                                            <iconify-icon icon="solar:pen-2-linear" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name', $area->name) }}" 
                                               placeholder="Nhập tên khu vực" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                                        <option value="Hoạt động" {{ old('status', $area->status) == 'Hoạt động' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="Bảo trì" {{ old('status', $area->status) == 'Bảo trì' ? 'selected' : '' }}>
                                            Bảo trì
                                        </option>
                                        <option value="Đóng cửa" {{ old('status', $area->status) == 'Đóng cửa' ? 'selected' : '' }}>
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
                                              placeholder="Mô tả ngắn về khu vực">{{ old('description', $area->description) }}</textarea>
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
                                        <span class="input-group-text icon-container bg-warning-subtle text-warning">
                                            <iconify-icon icon="solar:users-group-rounded-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                               name="capacity" value="{{ old('capacity', $area->capacity) }}" 
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
                                        <span class="input-group-text icon-container bg-warning-subtle text-warning">
                                            <iconify-icon icon="solar:dollar-minimalistic-broken" class="fs-18"></iconify-icon>
                                        </span>
                                        <input type="number" step="0.01" class="form-control @error('surcharge') is-invalid @enderror" 
                                               name="surcharge" value="{{ old('surcharge', $area->surcharge) }}" 
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
                                        <option value="0" {{ old('is_smoking', $area->is_smoking) === '0' || old('is_smoking', $area->is_smoking) === 0 ? 'selected' : '' }}>
                                            Không
                                        </option>
                                        <option value="1" {{ old('is_smoking', $area->is_smoking) === '1' || old('is_smoking', $area->is_smoking) === 1 ? 'selected' : '' }}>
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
                                        <option value="0" {{ old('is_vip', $area->is_vip) === '0' || old('is_vip', $area->is_vip) === 0 ? 'selected' : '' }}>
                                            Không
                                        </option>
                                        <option value="1" {{ old('is_vip', $area->is_vip) === '1' || old('is_vip', $area->is_vip) === 1 ? 'selected' : '' }}>
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
                                    <label class="form-label">Hình ảnh hiện tại</label>
                                    
                                    @if($area->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $area->image) }}" alt="{{ $area->name }}" 
                                             class="img-thumbnail" style="max-height: 150px;">
                                        <p class="small text-muted mt-1">Hình ảnh hiện tại</p>
                                    </div>
                                    @else
                                    <div class="mb-2">
                                        <div class="border rounded p-3 text-center bg-light">
                                            <iconify-icon icon="solar:image-broken" class="fs-48 text-muted"></iconify-icon>
                                            <p class="text-muted mb-0">Chưa có hình ảnh</p>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           name="image" accept="image/*">
                                    <small class="form-text text-muted">
                                        Tải lên hình mới để thay thế (nếu muốn). 
                                        Hỗ trợ định dạng: JPG, PNG, GIF (< 2MB)
                                    </small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($area->hourSetting && $area->hourSetting->has_operating_hours)
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
                                <div>
                                    <a href="{{ route('areas.show', $area->area_id) }}" class="btn btn-outline-info me-2">
                                        <iconify-icon icon="solar:eye-broken" class="me-1"></iconify-icon> 
                                        Xem chi tiết
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <iconify-icon icon="solar:disk-broken" class="me-1"></iconify-icon> 
                                        Lưu Thay Đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                @if($area->tables()->count() > 0)
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
<script>
let floorAreaData = {};

// Cập nhật khu vực có thể tạo khi chọn tầng
function updateAvailableAreas() {
    const floor = document.getElementById('floorSelect').value;
    const codeInput = document.getElementById('codeInput');
    const codeHelp = document.getElementById('codeHelp');
    
    if (!floor) {
        codeHelp.innerHTML = 'Chọn tầng trước để kiểm tra thứ tự khu vực';
        codeHelp.className = 'form-text text-muted';
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
    const currentCode = document.getElementById('codeInput').value.trim().toUpperCase();
    
    let helpText = `<strong>Tầng ${floor}:</strong> `;
    
    if (data.existing_areas.length === 0) {
        helpText += 'Chưa có khu vực nào. Có thể tạo khu <strong>A</strong>';
        helpText += '<br><small class="text-muted">Bàn sẽ là: A1, A2, A3...</small>';
    } else {
        helpText += `Đã có: ${data.existing_areas.join(', ')}.`;
        if (data.next_available) {
            helpText += ` Tiếp theo có thể tạo: <strong>${data.next_available}</strong>`;
            if (data.table_format_example) {
                helpText += `<br><small class="text-muted">Bàn trong khu ${data.next_available}: ${data.table_format_example}</small>`;
            }
        } else {
            helpText += ' <strong>Tầng này đã đầy!</strong>';
        }
    }
    
    // Kiểm tra nếu code hiện tại hợp lệ
    if (currentCode && data.existing_areas.includes(currentCode)) {
        helpText += `<br><span class="text-success"><strong>✓ Khu ${currentCode} đã tồn tại - OK để chỉnh sửa</strong></span>`;
        codeHelp.className = 'form-text text-success';
    } else {
        codeHelp.className = 'form-text text-info';
    }
    
    codeHelp.innerHTML = helpText;
}

function validateAreaCode() {
    const floor = document.getElementById('floorSelect').value;
    const code = document.getElementById('codeInput').value.trim().toUpperCase();
    const codeInput = document.getElementById('codeInput');
    const codeHelp = document.getElementById('codeHelp');
    const submitBtn = document.getElementById('submitBtn');
    const originalCode = '{{ $area->code }}'; // Code gốc của area
    
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

    // Nếu code giống code gốc, luôn cho phép (đang edit cùng record)
    if (code === originalCode) {
        codeInput.classList.remove('is-invalid');
        codeInput.classList.add('is-valid');
        updateCodeHelp(floor, data);
        submitBtn.disabled = false;
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
@endsection