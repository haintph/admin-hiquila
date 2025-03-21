@extends('admin.layouts.master')

@section('content')
    <h2>Danh sách khu vực</h2>
    <a href="{{ route('areas.create') }}" class="btn btn-success">Thêm Khu Vực</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <tr>
            <th>ID</th><th>Tên</th><th>Trạng thái</th><th>Hành động</th>
        </tr>
        @foreach ($areas as $area)
            <tr>
                <td>{{ $area->area_id }}</td>
                <td>{{ $area->name }}</td>
                <td>{{ $area->status }}</td>
                <td>
                    <a href="{{ route('areas.show', $area) }}" class="btn btn-info">Xem</a>
                    <a href="{{ route('areas.edit', $area) }}" class="btn btn-warning">Sửa</a>
                    <form action="{{ route('areas.destroy', $area) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
