@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <h2 class="mb-4">Chọn Khu Vực và Bàn</h2>

        <div class="mb-3">
            <label class="form-label">Chọn Khu Vực</label>
            <select id="areaSelect" class="form-control">
                <option value="">-- Chọn khu vực --</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->area_id }}">{{ $area->name }}</option>
                @endforeach
            </select>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Chọn Bàn</label>
                <select class="form-control" name="table_id" id="tableSelect" required>
                    <option value="">-- Chọn bàn --</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tạo Hóa Đơn</button>
        </form>
    </div>

    <script>
        document.getElementById('areaSelect').addEventListener('change', function() {
            let areaId = this.value;
            let tableSelect = document.getElementById('tableSelect');
            tableSelect.innerHTML = '<option value="">-- Chọn bàn --</option>'; // Xóa danh sách cũ

            if (areaId) {
                fetch(`/get-tables/${areaId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            tableSelect.innerHTML = '<option value="">Không có bàn trống</option>';
                        } else {
                            data.forEach(table => {
                                let option = document.createElement('option');
                                option.value = table.table_id;
                                option.text = `Bàn ${table.table_number} - Sức chứa: ${table.capacity}`;
                                tableSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Lỗi:', error));
            }
        });
    </script>
@endsection
