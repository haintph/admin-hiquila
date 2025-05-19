@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold text-uppercase">Quản lý bàn</h4>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Bộ lọc
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="area_id" class="form-label">Khu vực:</label>
                    <select name="area_id" id="area_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->area_id }}" {{ $area_id == $area->area_id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ $status == $statusOption ? 'selected' : '' }}>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="table_type" class="form-label">Loại bàn:</label>
                    <select name="table_type" id="table_type" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($tableTypes as $type)
                            <option value="{{ $type }}" {{ $table_type == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Lọc</button>
                    <a href="{{ route('tables.index') }}" class="btn btn-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('tables.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm bàn mới
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách bàn
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Số bàn</th>
                        <th>Sức chứa</th>
                        <th>Loại bàn</th>
                        <th>Trạng thái</th>
                        <th>Khu vực</th>
                        <th>Chi tiêu tối thiểu</th>
                        <th>Cho phép đặt trước</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tables as $table)
                        <tr>
                            <td>{{ $table->table_number }}</td>
                            <td>{{ $table->capacity }} người</td>
                            <td>{{ $table->table_type }}</td>
                            <td>
                                <span class="badge 
                                    @if($table->status == 'Trống') bg-success
                                    @elseif($table->status == 'Đã đặt') bg-warning
                                    @elseif($table->status == 'Đang phục vụ') bg-primary
                                    @elseif($table->status == 'Đang dọn') bg-info
                                    @elseif($table->status == 'Bảo trì') bg-danger
                                    @endif">
                                    {{ $table->status }}
                                </span>
                               
                            </td>
                            <td>{{ $table->area?->name ?? 'Không có' }}</td>
                            <td>{{ number_format($table->min_spend ?? 0) }} đ</td>
                            <td>
                                <span class="badge {{ $table->is_reservable ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $table->is_reservable ? 'Có' : 'Không' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tables.edit', $table->table_id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('tables.destroy', $table->table_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bàn {{ $table->table_number }}?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có bàn nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="d-flex justify-content-end">
                {{ $tables->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle quick status updates
        document.querySelectorAll('.update-status').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                
                const tableId = this.dataset.tableId;
                const status = this.dataset.status;
                
                if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái bàn thành "' + status + '"?')) {
                    fetch('{{ url("admin/tables") }}/' + tableId + '/update-status', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    });
                }
            });
        });
    });
</script>
@endsection