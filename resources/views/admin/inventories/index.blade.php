@extends('admin.layouts.master')

@section('title', 'Quản lý nguyên liệu')

@section('content')
<h2>Danh sách nguyên liệu</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('inventory.create') }}" class="btn btn-primary mb-3">Thêm nguyên liệu</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Đơn vị</th>
            <th>Số lượng</th>
            <th>Tồn tối thiểu</th>
            <th>Giá / Đơn vị</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($inventories as $inventory)
        <tr>
            <td>{{ $inventory->id }}</td>
            <td>{{ $inventory->name }}</td>
            <td>{{ $inventory->unit }}</td>
            <td>{{ $inventory->quantity }}</td>
            <td>{{ $inventory->min_quantity }}</td>
            <td>{{ number_format($inventory->cost_per_unit, 2) }}</td>
            <td>
                <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa nguyên liệu này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $inventories->links() }}
@endsection
