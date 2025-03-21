@extends('admin.layouts.master')

@section('content')
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <h3>Chi tiết biến thể</h3>
                </div>
                
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-4">
                                    <tbody>
                                        <tr>
                                            <th width="200">ID</th>
                                            <td>{{ $variant->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tên biến thể</th>
                                            <td>{{ $variant->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Thuộc món</th>
                                            <td>{{ $variant->dish->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Giá</th>
                                            <td>{{ number_format($variant->price) }} VNĐ</td>
                                        </tr>
                                        <tr>
                                            <th>Tồn kho</th>
                                            <td>{{ $variant->stock }}</td>
                                        </tr>
                                        <tr>
                                            <th>Trạng thái</th>
                                            <td>
                                                @if($variant->is_available == 1)
                                                    <span class="badge bg-success">Còn bán</span>
                                                @else
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ngày tạo</th>
                                            <td>{{ $variant->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cập nhật lần cuối</th>
                                            <td>{{ $variant->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('variants.edit', $variant->id) }}" class="btn btn-primary">Chỉnh sửa</a>
                                
                                <form action="{{ route('variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa biến thể này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                                
                                <a href="{{ route('dish_detail', $variant->dish_id) }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection