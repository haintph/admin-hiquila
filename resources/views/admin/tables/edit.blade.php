@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold text-uppercase">{{ isset($table) ? 'Chỉnh Sửa' : 'Thêm' }} Bàn</h4>

    <form action="{{ isset($table) ? route('tables.update', $table->table_id) : route('tables.store') }}" method="POST">
        @csrf
        @if(isset($table)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Số bàn</label>
            <input type="text" class="form-control" name="table_number" value="{{ $table->table_number ?? '' }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Sức chứa</label>
            <input type="number" class="form-control" name="capacity" value="{{ $table->capacity ?? '' }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select class="form-control" name="status">
                @foreach(['Trống', 'Đã đặt', 'Đang phục vụ'] as $status)
                    <option value="{{ $status }}" {{ (isset($table) && $table->status == $status) ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Khu vực</label>
            <select class="form-control" name="area_id">
                <option value="">Không có</option>
                @foreach($areas as $area)
                    <option value="{{ $area->area_id }}" {{ (isset($table) && $table->area_id == $area->area_id) ? 'selected' : '' }}>
                        {{ $area->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($table) ? 'Cập nhật' : 'Thêm' }} Bàn</button>
    </form>
</div>
@endsection
