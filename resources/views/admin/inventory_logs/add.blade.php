@extends('admin.layouts.master')

@section('title', 'Thêm giao dịch kho')

@section('content')
<h2>Thêm giao dịch kho</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('inventory_logs.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Nguyên liệu</label>
        <select name="inventory_id" class="form-control" required>
            @foreach ($inventories as $inventory)
                <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Loại giao dịch</label>
        <select name="type" class="form-control" required>
            <option value="import">Nhập kho</option>
            <option value="export">Xuất kho</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Số lượng</label>
        <input type="number" name="quantity" class="form-control" step="0.01" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Chi phí (tùy chọn)</label>
        <input type="number" name="cost" class="form-control" step="0.01">
    </div>

    <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <input type="text" name="note" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Lưu</button>
</form>
@endsection
