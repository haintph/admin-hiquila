@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Tạo Hóa Đơn Mới</h5>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <form action="{{ route('staff.store') }}" method="POST" id="invoiceForm">
                @csrf
                <input type="hidden" name="table_id" id="selectedTableId">
                
                <!-- Hiển thị tầng dưới dạng tab -->
                <div class="mb-4">
                    <h5 class="mb-3">Chọn Tầng</h5>
                    <ul class="nav nav-tabs" id="floorTabs" role="tablist">
                        @foreach($floors as $floor)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $floor == $selectedFloor ? 'active' : '' }}" 
                                   href="{{ route('staff.create', ['floor' => $floor]) }}">
                                    Tầng {{ $floor }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Hiển thị khu vực dưới dạng thẻ -->
                <div class="mb-4">
                    <h5 class="mb-3">Chọn Khu Vực</h5>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach($areas as $area)
                            <div class="col">
                                <a href="{{ route('staff.create', ['floor' => $selectedFloor, 'area_id' => $area->area_id]) }}" 
                                   class="text-decoration-none">
                                    <div class="card h-100 {{ $area->area_id == $area_id ? 'border-primary bg-light' : '' }}">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ $area->name }}</h5>
                                            <p class="card-text">
                                                <span class="badge bg-primary">{{ $area->code }}</span>
                                                @if($area->is_smoking)
                                                    <span class="badge bg-warning text-dark">Hút thuốc</span>
                                                @endif
                                                @if($area->is_vip)
                                                    <span class="badge bg-danger">VIP</span>
                                                @endif
                                            </p>
                                            <p class="card-text">
                                                <span class="badge bg-success">
                                                    {{ $area->tables->where('status', 'Trống')->count() }} bàn trống
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Hiển thị bàn dưới dạng hình ảnh -->
                <div class="mb-4">
                    <h5 class="mb-3">Chọn Bàn</h5>
                    @if(count($tables) > 0)
                        <div class="row row-cols-1 row-cols-md-4 g-4">
                            @foreach($tables as $table)
                                <div class="col">
                                    <div class="card h-100 table-card position-relative" onclick="selectTable({{ $table->table_id }})">
                                        <div class="status-badge">Trống</div>
                                        <span class="capacity-badge">{{ $table->capacity }} người</span>
                                        <div class="card-body text-center">
                                            <div class="table-visual table-{{ $tableTypes[$table->table_type]['size'] ?? 'medium' }} mb-3"
                                                 style="background-color: {{ $tableTypes[$table->table_type]['color'] ?? '#f8f9fa' }}; 
                                                        border-radius: {{ $tableTypes[$table->table_type]['shape'] == 'circle' ? '50%' : 
                                                                          ($tableTypes[$table->table_type]['shape'] == 'rectangle' ? '8px/20px' : '8px') }};">
                                                {{ $table->table_number }}
                                            </div>
                                            <h5 class="card-title">Bàn {{ $table->table_number }}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">Sức chứa: {{ $table->capacity }} người</small><br>
                                                <small class="text-muted">Loại: {{ $table->table_type }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">Không có bàn trống trong khu vực này</div>
                    @endif
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Ghi chú loại bàn</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($tableTypes as $type => $style)
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 20px; height: 20px; background-color: {{ $style['color'] }}; 
                                                            border-radius: {{ $style['shape'] == 'circle' ? '50%' : 
                                                                              ($style['shape'] == 'rectangle' ? '3px/10px' : '3px') }};">
                                                </div>
                                                <span class="ms-2">{{ $type }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitButton" disabled>Tạo Hóa Đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .table-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
        border-radius: 8px;
    }
    
    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .table-card.selected {
        border: 2px solid #198754;
        background-color: #d1e7dd;
    }
    
    .table-visual {
        width: 70px;
        height: 70px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: #212529;
    }
    
    .table-small {
        width: 50px;
        height: 50px;
    }
    
    .table-medium {
        width: 60px;
        height: 60px;
    }
    
    .table-large {
        width: 70px;
        height: 70px;
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
    function selectTable(tableId) {
        // Remove selection from all table cards
        document.querySelectorAll('.table-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selection to clicked card
        event.currentTarget.classList.add('selected');
        
        // Update hidden input with selected table ID
        document.getElementById('selectedTableId').value = tableId;
        
        // Enable submit button
        document.getElementById('submitButton').disabled = false;
    }
</script>
@endsection