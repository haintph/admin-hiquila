@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold text-uppercase">Quản lý bàn</h4>

    <form method="GET" class="mb-3">
        <label for="area_id">Lọc theo khu vực:</label>
        <select name="area_id" class="form-control" onchange="this.form.submit()">
            <option value="">Tất cả</option>
            @foreach($areas as $area)
                <option value="{{ $area->area_id }}" {{ $area_id == $area->area_id ? 'selected' : '' }}>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
    </form>

    <a href="{{ route('tables.create') }}" class="btn btn-primary mb-3">Thêm Bàn</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Số bàn</th>
                <th>Sức chứa</th>
                <th>Trạng thái</th>
                <th>Khu vực</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tables as $table)
                <tr>
                    <td>{{ $table->table_number }}</td>
                    <td>{{ $table->capacity }}</td>
                    <td>{{ $table->status }}</td>
                    <td>{{ $table->area?->name ?? 'Không có' }}</td>
                    <td>
                        <a href="{{ route('tables.edit', $table->table_id) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('tables.destroy', $table->table_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $tables->links() }}
</div>
@endsection
