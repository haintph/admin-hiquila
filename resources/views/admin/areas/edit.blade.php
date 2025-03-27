@extends('admin.layouts.master')

@section('content')
    <h2>Chỉnh sửa khu vực</h2>
    <form action="{{ route('areas.update', $area) }}" method="POST">
        @csrf @method('PUT')
        <label>Tên:</label>
        <input type="text" name="name" value="{{ $area->name }}" required>
        <label>Trạng thái:</label>
        <select name="status">
            <option value="Hoạt động" {{ $area->status == 'Hoạt động' ? 'selected' : '' }}>Hoạt động</option>
            <option value="Bảo trì" {{ $area->status == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
            <option value="Đóng cửa" {{ $area->status == 'Đóng cửa' ? 'selected' : '' }}>Đóng cửa</option>
        </select>
        <button type="submit">Cập nhật</button>
    </form>
@endsection
