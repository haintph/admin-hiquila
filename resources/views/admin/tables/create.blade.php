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
                        <label for="table_number" class="form-label required">Số bàn</label>
                        <input type="text" class="form-control @error('table_number') is-invalid @enderror" 
                               id="table_number" name="table_number" 
                               value="{{ old('table_number', $table->table_number ?? '') }}" maxlength="10" required>
                        @error('table_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Số hoặc tên bàn, tối đa 10 ký tự</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="capacity" class="form-label required">Sức chứa</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" 
                               value="{{ old('capacity', $table->capacity ?? '') }}" min="1" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Số người có thể ngồi tại bàn</div>
                    </div>
                </div>
                
                <div class="row mb-3">
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
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label required">Trạng thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ (old('status', $table->status ?? '') == $status) ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="area_id" class="form-label">Khu vực</label>
                        <select class="form-select @error('area_id') is-invalid @enderror" 
                                id="area_id" name="area_id">
                            <option value="">Không có</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->area_id }}" {{ (old('area_id', $table->area_id ?? '') == $area->area_id) ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('area_id')
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
                              id="notes" name="notes" rows="3">{{ old('notes', $table->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_reservable" name="is_reservable" value="1"
                            {{ old('is_reservable', $table->is_reservable ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_reservable">
                            Cho phép đặt trước
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-{{ isset($table) ? 'save' : 'plus-circle' }} me-1"></i>
                        {{ isset($table) ? 'Cập nhật' : 'Thêm' }} Bàn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>