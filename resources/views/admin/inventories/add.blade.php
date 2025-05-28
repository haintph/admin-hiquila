@extends('admin.layouts.master')

@section('title', 'Thêm nguyên liệu')

@section('content')
<h2>Thêm nguyên liệu mới</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('inventory.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Tên nguyên liệu</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Đơn vị</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tồn kho</label>
        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" step="0.01" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tồn tối thiểu</label>
        <input type="number" name="min_quantity" class="form-control" value="{{ old('min_quantity', 0) }}" step="0.01" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Giá / Đơn vị</label>
        <input type="number" name="cost_per_unit" class="form-control" value="{{ old('cost_per_unit') }}" step="0.01">
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Lưu</button>
</form>
@endsection
