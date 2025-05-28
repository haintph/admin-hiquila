@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-xl-8 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Sửa biến thể: {{ $variant->name }}</h4>
                            <a href="{{ route('variant_list') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>Quay lại danh sách
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Hiển thị thông báo lỗi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('variants.update', $variant->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Tên biến thể <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               id="name"
                                               name="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $variant->name) }}"
                                               placeholder="Ví dụ: Size L, Tôm hùm loại A, Phần nhỏ..."
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="unit" class="form-label">
                                            Đơn vị bán <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               id="unit"
                                               name="unit" 
                                               class="form-control @error('unit') is-invalid @enderror" 
                                               value="{{ old('unit', $variant->unit) }}"
                                               placeholder="Ví dụ: phần, kg, gram, con, chai, ly..."
                                               required>
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Đơn vị tính khi bán cho khách</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">
                                            Giá bán (VNĐ) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   id="price"
                                                   name="price" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   value="{{ old('price', $variant->price) }}"
                                                   min="0"
                                                   step="1000"
                                                   placeholder="0"
                                                   required>
                                            <span class="input-group-text">VNĐ</span>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Giá bán cho khách hàng</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">
                                            Số lượng tồn kho <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               id="stock"
                                               name="stock" 
                                               class="form-control @error('stock') is-invalid @enderror" 
                                               value="{{ old('stock', $variant->stock) }}"
                                               min="0"
                                               placeholder="0"
                                               required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Số lượng hiện có trong kho (để 0 nếu không quản lý tồn kho)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="is_available" class="form-label">
                                            Trạng thái bán <span class="text-danger">*</span>
                                        </label>
                                        <select id="is_available" 
                                                name="is_available" 
                                                class="form-select @error('is_available') is-invalid @enderror" 
                                                required>
                                            <option value="1" {{ old('is_available', $variant->is_available) == '1' ? 'selected' : '' }}>
                                                ✅ Đang bán
                                            </option>
                                            <option value="0" {{ old('is_available', $variant->is_available) == '0' ? 'selected' : '' }}>
                                                ❌ Ngừng bán
                                            </option>
                                        </select>
                                        @error('is_available')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Trạng thái hiển thị với khách hàng</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Thông tin thời gian</label>
                                        <div class="bg-light p-2 rounded">
                                            <small class="d-block text-muted">
                                                <i class="mdi mdi-calendar-plus me-1"></i>
                                                Tạo: {{ $variant->created_at ? $variant->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </small>
                                            <small class="d-block text-muted">
                                                <i class="mdi mdi-calendar-edit me-1"></i>
                                                Sửa: {{ $variant->updated_at ? $variant->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('variant_list') }}" class="btn btn-light">
                                    <i class="mdi mdi-close me-1"></i>Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save me-1"></i>Cập nhật biến thể
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Thông tin món ăn --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-information-outline me-2"></i>Thông tin món ăn: {{ $dish->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                @if($dish->image)
                                    <img src="{{ asset('storage/' . $dish->image) }}" 
                                         alt="{{ $dish->name }}" 
                                         class="img-fluid rounded border"
                                         style="max-height: 100px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center border" 
                                         style="height: 100px; width: 100px;">
                                        <i class="mdi mdi-image-off text-muted fs-24"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h6 class="mb-1">{{ $dish->name }}</h6>
                                @if($dish->description)
                                    <p class="text-muted mb-2">{{ Str::limit($dish->description, 100) }}</p>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="mdi mdi-calendar me-1"></i>
                                            Tạo: {{ $dish->created_at ? $dish->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="mdi mdi-update me-1"></i>
                                            Sửa: {{ $dish->updated_at ? $dish->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                                @if($dish->variants && $dish->variants->count() > 0)
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="mdi mdi-format-list-bulleted me-1"></i>
                                            Tổng {{ $dish->variants->count() }} biến thể
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Format số tiền khi người dùng nhập (bước 1000 VNĐ)
    document.getElementById('price').addEventListener('input', function(e) {
        let value = e.target.value;
        if (value) {
            // Chỉ cho phép nhập số
            value = value.replace(/[^\d]/g, '');
            e.target.value = value;
        }
    });

    // Cảnh báo admin khi stock = 0 nhưng vẫn chọn "Đang bán"
    document.getElementById('is_available').addEventListener('change', function() {
        const stock = document.getElementById('stock').value;
        if (this.value == '1' && stock == '0') {
            if (confirm('⚠️ Cảnh báo: Bạn đang đặt trạng thái "Đang bán" nhưng tồn kho = 0.\n\nKhách hàng có thể đặt hàng nhưng không có sản phẩm để giao.\n\nBạn có chắc chắn muốn tiếp tục?')) {
                // Giữ nguyên lựa chọn
            } else {
                this.value = '0'; // Chuyển về "Ngừng bán"
            }
        }
    });

    // Tự động format giá khi blur (rời khỏi ô input)
    document.getElementById('price').addEventListener('blur', function(e) {
        if (e.target.value) {
            const value = parseInt(e.target.value);
            // Làm tròn thành bội số của 1000
            const rounded = Math.round(value / 1000) * 1000;
            e.target.value = rounded;
        }
    });

    // Gợi ý đơn vị phổ biến
    const unitInput = document.getElementById('unit');
    const commonUnits = ['phần', 'kg', 'gram', 'con', 'chai', 'ly', 'tô', 'dĩa', 'hộp', 'gói'];
    
    // Tạo datalist cho auto-complete
    const datalist = document.createElement('datalist');
    datalist.id = 'unit-suggestions';
    commonUnits.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit;
        datalist.appendChild(option);
    });
    document.body.appendChild(datalist);
    unitInput.setAttribute('list', 'unit-suggestions');

    // Highlight các thay đổi so với giá trị gốc
    const originalValues = {
        name: '{{ $variant->name }}',
        unit: '{{ $variant->unit }}',
        price: '{{ $variant->price }}',
        stock: '{{ $variant->stock }}',
        is_available: '{{ $variant->is_available }}'
    };

    function checkChanges() {
        ['name', 'unit', 'price', 'stock', 'is_available'].forEach(field => {
            const input = document.getElementById(field);
            if (input.value != originalValues[field]) {
                input.classList.add('border-warning');
            } else {
                input.classList.remove('border-warning');
            }
        });
    }

    // Kiểm tra thay đổi khi người dùng nhập
    ['name', 'unit', 'price', 'stock', 'is_available'].forEach(field => {
        document.getElementById(field).addEventListener('input', checkChanges);
        document.getElementById(field).addEventListener('change', checkChanges);
    });
</script>
@endpush