@extends('admin.layouts.master')

@section('content')
    <h2>Thêm Khu Vực</h2>
    <form action="{{ route('areas.store') }}" method="POST">
        @csrf
        <label>Tên:</label>
        <input type="text" name="name" required>
        <label>Trạng thái:</label>
        <select name="status">
            <option value="Hoạt động">Hoạt động</option>
            <option value="Bảo trì">Bảo trì</option>
            <option value="Đóng cửa">Đóng cửa</option>
        </select>
        <button type="submit">Thêm</button>
    </form>
@endsection
