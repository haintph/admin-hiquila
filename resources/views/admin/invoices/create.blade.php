@extends('admin.layouts.master')

@section('content')
<div class="container">
    <h2 class="mb-4">Chọn Khu Vực và Bàn</h2>
    
    <!-- Chọn khu vực với giao diện thẻ -->
    <div class="mb-4">
        <h4 class="mb-3">Chọn Khu Vực</h4>
        <div class="row row-cols-1 row-cols-md-3 g-4" id="areaContainer">
            @foreach ($areas as $area)
                <div class="col">
                    <div class="card h-100 area-card" data-area-id="{{ $area->area_id }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $area->name }}</h5>
                            <p class="card-text">
                                <span class="badge bg-success">
                                    {{ $area->tables->where('status', 'Trống')->count() }} bàn trống
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        <input type="hidden" name="table_id" id="selectedTableId">
        
        <!-- Hiển thị bàn -->
        <div class="mb-4">
            <h4 class="mb-3">Chọn Bàn Trống</h4>
            <div class="alert alert-info" id="tablePrompt">Vui lòng chọn khu vực trước</div>
            <div class="row row-cols-1 row-cols-md-4 g-4" id="tableContainer">
                <!-- Các bàn trống sẽ được hiển thị ở đây -->
            </div>
        </div>
        
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg" id="submitButton" disabled>Tạo Hóa Đơn</button>
        </div>
    </form>
</div>

<style>
    .area-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
    }
    
    .area-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .area-card.active {
        border: 2px solid #0d6efd;
        background-color: #e8f0fe;
    }
    
    .table-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        height: 100%;
    }
    
    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .table-card.active {
        border: 2px solid #0d6efd;
        background-color: #e8f0fe;
    }
    
    .table-visual {
        width: 70px;
        height: 70px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #f8f9fa;
        font-size: 1.5rem;
        font-weight: bold;
        color: #0d6efd;
        border: 2px solid #0d6efd;
    }
    
    .table-small {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }
    
    .table-medium {
        width: 60px;
        height: 60px;
        border-radius: 8px;
    }
    
    .table-large {
        width: 70px;
        height: 70px;
        border-radius: 6px;
    }
    
    .capacity-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #0d6efd;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    .status-badge {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        padding: 3px;
        background-color: #198754;
        color: white;
        text-align: center;
        font-size: 0.7rem;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaCards = document.querySelectorAll('.area-card');
        const tableContainer = document.getElementById('tableContainer');
        const tablePrompt = document.getElementById('tablePrompt');
        const submitButton = document.getElementById('submitButton');
        const selectedTableId = document.getElementById('selectedTableId');
        
        // Xử lý sự kiện khi chọn khu vực
        areaCards.forEach(card => {
            card.addEventListener('click', function() {
                // Xóa trạng thái active cũ
                areaCards.forEach(c => c.classList.remove('active'));
                
                // Thêm trạng thái active cho thẻ đã chọn
                this.classList.add('active');
                
                const areaId = this.dataset.areaId;
                
                // Hiển thị trạng thái đang tải
                tablePrompt.textContent = 'Đang tải danh sách bàn trống...';
                tablePrompt.style.display = 'block';
                tableContainer.innerHTML = '';
                
                // Tải danh sách bàn từ server
                fetch(`/get-tables/${areaId}`)
                    .then(response => response.json())
                    .then(data => {
                        tablePrompt.style.display = 'none';
                        
                        if (data.length === 0) {
                            tablePrompt.textContent = 'Không có bàn trống trong khu vực này';
                            tablePrompt.style.display = 'block';
                        } else {
                            data.forEach(table => {
                                let tableSize = 'medium';
                                if (table.capacity <= 2) {
                                    tableSize = 'small';
                                } else if (table.capacity > 6) {
                                    tableSize = 'large';
                                }
                                
                                const tableCard = document.createElement('div');
                                tableCard.className = 'col';
                                tableCard.innerHTML = `
                                    <div class="card table-card position-relative" data-table-id="${table.table_id}">
                                        <div class="status-badge">Trống</div>
                                        <span class="capacity-badge">${table.capacity} người</span>
                                        <div class="card-body text-center">
                                            <div class="table-visual table-${tableSize} mb-3">
                                                ${table.table_number}
                                            </div>
                                            <h5 class="card-title">Bàn ${table.table_number}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">Sức chứa: ${table.capacity} người</small>
                                            </p>
                                        </div>
                                    </div>
                                `;
                                tableContainer.appendChild(tableCard);
                                
                                // Thêm sự kiện click cho bàn
                                const newTableCard = tableCard.querySelector('.table-card');
                                newTableCard.addEventListener('click', function() {
                                    // Xóa trạng thái active cũ
                                    document.querySelectorAll('.table-card').forEach(c => c.classList.remove('active'));
                                    
                                    // Thêm trạng thái active cho bàn đã chọn
                                    this.classList.add('active');
                                    
                                    // Lưu ID bàn đã chọn
                                    selectedTableId.value = this.dataset.tableId;
                                    
                                    // Kích hoạt nút tạo hóa đơn
                                    submitButton.disabled = false;
                                });
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        tablePrompt.textContent = 'Đã xảy ra lỗi khi tải danh sách bàn';
                        tablePrompt.style.display = 'block';
                    });
            });
        });
    });
</script>
@endsection