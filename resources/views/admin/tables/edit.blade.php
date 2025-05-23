@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="fw-bold text-uppercase mb-0">
                <i class="fas fa-{{ isset($table) ? 'edit' : 'plus' }} me-2"></i>
                {{ isset($table) ? 'Chỉnh Sửa' : 'Thêm' }} Bàn
            </h4>
        </div>
        <div class="card-body">
            <!-- Flash Message for Error -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Hướng dẫn quy tắc tạo bàn -->
            @if(!isset($table))
            <div class="alert alert-info mb-4">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Quy tắc tạo bàn</h6>
                <ul class="mb-0">
                    <li><strong>Thứ tự theo khu vực:</strong> Phải tạo A1 trước A2, B1 trước B2, C1 trước C2...</li>
                    <li><strong>Số bàn tự động:</strong> Để trống để hệ thống tự tạo số tiếp theo</li>
                    <li><strong>Sức chứa:</strong> Tối đa 20 người/bàn, tổng không vượt sức chứa khu vực</li>
                    <li><strong>Format:</strong> A1, A2, A3... (khu A) | B1, B2, B3... (khu B)</li>
                </ul>
            </div>
            @endif

            <form action="{{ isset($table) ? route('tables.update', $table->table_id) : route('tables.store') }}" method="POST">
                @csrf
                @if(isset($table)) @method('PUT') @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="area_id" class="form-label">Khu vực {{ !isset($table) ? 'required' : '' }}</label>
                        <select class="form-select @error('area_id') is-invalid @enderror" 
                                id="area_id" name="area_id" {{ !isset($table) ? 'required' : '' }}>
                            <option value="">{{ isset($table) ? 'Không thuộc khu vực' : 'Chọn khu vực' }}</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->area_id }}" 
                                    {{ (old('area_id', $selectedAreaId ?? $table->area_id ?? '') == $area->area_id) ? 'selected' : '' }}
                                    data-code="{{ $area->code }}"
                                    data-capacity="{{ $area->capacity }}"
                                    data-floor="{{ $area->floor }}">
                                    {{ $area->code }} - {{ $area->name }} 
                                    @if($area->floor)(Tầng {{ $area->floor }})@endif
                                    @if($area->capacity) - Sức chứa: {{ $area->capacity }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('area_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn khu vực để số bàn tự động theo format A1, A2, B1, B2...</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="table_number" class="form-label {{ isset($table) ? 'required' : '' }}">Số bàn</label>
                        <input type="text" class="form-control @error('table_number') is-invalid @enderror" 
                               id="table_number" name="table_number" 
                               value="{{ old('table_number', $table->table_number ?? '') }}" 
                               maxlength="10" 
                               {{ isset($table) ? 'required' : '' }}
                               placeholder="{{ isset($table) ? 'Nhập số bàn' : 'Để trống để tự động tạo' }}">
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="table-number-suggestion" class="form-text"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="capacity" class="form-label required">Sức chứa</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" 
                               value="{{ old('capacity', $table->capacity ?? '') }}" 
                               min="1" max="20" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Số người có thể ngồi tại bàn (tối đa 20)</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="table_type" class="form-label required">Loại bàn</label>
                        <select class="form-select @error('table_type') is-invalid @enderror" 
                                id="table_type" name="table_type" required>
                            @foreach($tableTypes as $type)
                                <option value="{{ $type }}" {{ (old('table_type', $table->table_type ?? '') == $type) ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('table_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label required">Trạng thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="Trống" {{ (old('status', $table->status ?? '') == 'Trống') ? 'selected' : '' }}>Trống</option>
                            <option value="Đã đặt" {{ (old('status', $table->status ?? '') == 'Đã đặt') ? 'selected' : '' }}>Đã đặt</option>
                            <option value="Đang phục vụ" {{ (old('status', $table->status ?? '') == 'Đang phục vụ') ? 'selected' : '' }}>Đang phục vụ</option>
                            <option value="Đang dọn" {{ (old('status', $table->status ?? '') == 'Đang dọn') ? 'selected' : '' }}>Đang dọn</option>
                            <option value="Bảo trì" {{ (old('status', $table->status ?? '') == 'Bảo trì') ? 'selected' : '' }}>Bảo trì</option>
                            <option value="Không hoạt động" {{ (old('status', $table->status ?? '') == 'Không hoạt động') ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="min_spend" class="form-label">Chi tiêu tối thiểu</label>
                        <input type="number" class="form-control @error('min_spend') is-invalid @enderror" 
                               id="min_spend" name="min_spend" 
                               value="{{ old('min_spend', $table->min_spend ?? '') }}" min="0">
                        @error('min_spend')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu không có yêu cầu chi tiêu tối thiểu</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Ghi chú</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3" maxlength="500">{{ old('notes', $table->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Tối đa 500 ký tự</div>
                </div>
                
                <div class="mb-3">
                    <div id="area-capacity-info" class="mt-2"></div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_reservable" name="is_reservable" value="1"
                            {{ old('is_reservable', $table->is_reservable ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_reservable">
                            Cho phép đặt trước
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-{{ isset($table) ? 'save' : 'plus-circle' }} me-1"></i>
                        {{ isset($table) ? 'Cập nhật' : 'Thêm' }} Bàn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let isEdit = {{ isset($table) ? 'true' : 'false' }};
    let currentTableId = {{ isset($table) ? $table->table_id : 'null' }};
    
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('area_id');
        const tableNumberInput = document.getElementById('table_number');
        const capacityInput = document.getElementById('capacity');
        const areaCapacityInfo = document.getElementById('area-capacity-info');
        const tableNumberSuggestion = document.getElementById('table-number-suggestion');
        const submitBtn = document.getElementById('submitBtn');
        
        // Khởi tạo
        updateTableNumberSuggestion();
        updateAreaCapacityInfo();
        
        // Cập nhật khi thay đổi khu vực
        areaSelect.addEventListener('change', function() {
            updateTableNumberSuggestion();
            updateAreaCapacityInfo();
            validateTableNumber();
        });
        
        // Cập nhật khi thay đổi sức chứa bàn
        capacityInput.addEventListener('change', updateAreaCapacityInfo);
        
        // Validate số bàn khi nhập
        tableNumberInput.addEventListener('input', validateTableNumber);
        
        function updateTableNumberSuggestion() {
            const areaId = areaSelect.value;
            const selectedOption = areaSelect.options[areaSelect.selectedIndex];
            
            if (!areaId || isEdit) {
                tableNumberSuggestion.innerHTML = isEdit ? 'Số bàn hiện tại có thể chỉnh sửa' : 'Chọn khu vực để xem gợi ý số bàn';
                return;
            }
            
            const areaCode = selectedOption.dataset.code;
            
            // Gọi API để lấy số bàn tiếp theo có thể tạo
            fetch(`/admin/areas/${areaId}/next-available-table`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        tableNumberSuggestion.innerHTML = `<strong>Số bàn tiếp theo có thể tạo:</strong> <span class="text-primary">${data.next_table}</span>`;
                        
                        // Tự động điền nếu input trống
                        if (!tableNumberInput.value) {
                            tableNumberInput.value = data.next_table;
                        }
                    } else {
                        tableNumberSuggestion.innerHTML = `<span class="text-warning">${data.message}</span>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableNumberSuggestion.innerHTML = '<span class="text-danger">Không thể tải thông tin</span>';
                });
        }
        
        function validateTableNumber() {
            const areaId = areaSelect.value;
            const tableNumber = tableNumberInput.value.trim();
            
            if (!areaId || !tableNumber || isEdit) {
                submitBtn.disabled = false;
                return;
            }
            
            // Gọi API để kiểm tra số bàn có hợp lệ không
            fetch(`/admin/areas/${areaId}/validate-table-number`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    table_number: tableNumber,
                    table_id: currentTableId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    tableNumberSuggestion.innerHTML = `<span class="text-danger">${data.message}</span>`;
                    tableNumberInput.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else {
                    tableNumberSuggestion.innerHTML = `<span class="text-success">✓ Số bàn hợp lệ</span>`;
                    tableNumberInput.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        function updateAreaCapacityInfo() {
            const areaId = areaSelect.value;
            const selectedOption = areaSelect.options[areaSelect.selectedIndex];
            const tableCapacity = parseInt(capacityInput.value) || 0;
            
            if (!areaId) {
                areaCapacityInfo.innerHTML = '';
                return;
            }
            
            const areaCapacity = parseInt(selectedOption.dataset.capacity);
            
            if (!areaCapacity) {
                areaCapacityInfo.innerHTML = '<div class="alert alert-info">Khu vực chưa có giới hạn sức chứa</div>';
                return;
            }
            
            // Gọi API để lấy sức chứa hiện tại
            const url = `/admin/areas/${areaId}/current-capacity${currentTableId ? '?exclude_table=' + currentTableId : ''}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const currentCapacity = data.current_capacity;
                    const remainingCapacity = areaCapacity - currentCapacity;
                    const newTotal = currentCapacity + tableCapacity;
                    
                    let alertClass = 'alert-info';
                    let icon = 'fas fa-info-circle';
                    let message = '';
                    
                    if (newTotal > areaCapacity) {
                        alertClass = 'alert-danger';
                        icon = 'fas fa-exclamation-triangle';
                        message = `<strong>Cảnh báo:</strong> Vượt quá sức chứa khu vực!<br>
                                  Hiện tại: ${currentCapacity}/${areaCapacity} người. Còn lại: ${remainingCapacity} người. Cần: ${tableCapacity} người.`;
                    } else {
                        alertClass = 'alert-success';
                        icon = 'fas fa-check-circle';
                        message = `<strong>OK:</strong> Sức chứa phù hợp.<br>
                                  Hiện tại: ${currentCapacity}/${areaCapacity} người. Sau khi thêm: ${newTotal}/${areaCapacity} người.`;
                    }
                    
                    areaCapacityInfo.innerHTML = `
                        <div class="alert ${alertClass} mb-0">
                            <i class="${icon} me-2"></i>${message}
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    areaCapacityInfo.innerHTML = '<div class="alert alert-warning">Không thể tải thông tin sức chứa</div>';
                });
        }
    });
</script>
@endsection

<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>