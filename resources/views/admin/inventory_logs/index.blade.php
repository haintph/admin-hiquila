@extends('admin.layouts.master')

@section('title', 'Lịch sử nhập - xuất kho')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Lịch sử nhập - xuất kho</h2>
            <a href="{{ route('inventory_logs.create') }}" class="btn btn-success">+ Thêm giao dịch</a>
        </div>

        <!-- Bộ lọc -->
        <form action="{{ route('inventory_logs.index') }}" method="GET" class="mb-3 d-flex">
            <select name="inventory_id" class="form-select me-2">
                <option value="">Tất cả nguyên liệu</option>
                @foreach ($inventories as $inventory)
                    <option value="{{ $inventory->id }}" {{ request('inventory_id') == $inventory->id ? 'selected' : '' }}>
                        {{ $inventory->name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="form-select me-2">
                <option value="">Tất cả giao dịch</option>
                <option value="import" {{ request('type') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                <option value="export" {{ request('type') == 'export' ? 'selected' : '' }}>Xuất kho</option>
            </select>

            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Bảng danh sách giao dịch -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nguyên liệu</th>
                    <th>Loại giao dịch</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Ghi chú</th>
                    <th>Người giao dịch</th>
                    <th>Ngày</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventoryLogs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $log->inventory->name }}</td>
                        <td>{{ $log->type == 'import' ? 'Nhập kho' : 'Xuất kho' }}</td>
                        <td>{{ $log->quantity }}, {{ $log->inventory->unit }}</td>
                        <td>{{ $log->cost ? number_format($log->cost, 2) : '-' }}</td>
                        <td>{{ $log->note ?? '-' }}</td>
                        <td>{{ $log->user ? $log->user->name : 'Không xác định' }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('inventory_logs.edit', $log->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('inventory_logs.destroy', $log->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        {{ $inventoryLogs->links() }}
    </div>
@endsection
