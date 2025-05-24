@extends('admin.layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Đơn hàng đang chờ chế biến</h3>
                    <div>
                        <span class="badge bg-warning me-2">
                            {{ $pendingOrders->count() }} batch đang chờ
                        </span>
                        <button class="btn btn-primary" id="refresh-btn">
                            <i class="fas fa-sync-alt"></i> Làm mới
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    @if($pendingOrders->count() > 0)
                        <div class="row">
                            @foreach($pendingOrders as $order)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                Bàn: {{ $order->table->table_number ?? $order->table->name }} - Đơn #{{ $order->invoice_id }}
                                            </h5>
                                            <div class="text-end">
                                                <span class="badge bg-warning">
                                                    {{ \Carbon\Carbon::parse($order->sent_to_kitchen_at)->diffForHumans() }}
                                                </span>
                                                <small class="d-block text-white-50">
                                                    {{ $order->total_items }} món
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6>Các món cần chế biến:</h6>
                                            <ul class="list-group mb-3">
                                                @foreach($order->items as $item)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            @if($item->variant)
                                                                {{ $item->dish->name }} 
                                                                <small class="text-muted">({{ $item->variant->name }})</small>
                                                            @else
                                                                {{ $item->dish->name }}
                                                            @endif
                                                            <br>
                                                            <small class="text-muted">
                                                                #{{ $item->detail_id }} • {{ number_format($item->price, 0, ',', '.') }}đ
                                                            </small>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill">x{{ $item->quantity }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <form action="{{ route('chef.confirmOrder', $order->invoice_id) }}" method="POST">
                                                @csrf
                                                <!-- Gửi thời gian gửi bếp để xác định batch -->
                                                <input type="hidden" name="sent_time" value="{{ $order->sent_to_kitchen_at }}">
                                                <button type="submit" class="btn btn-success w-100"
                                                    onclick="return confirm('Xác nhận đã chế biến xong {{ $order->total_items }} món này?')">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    Xác nhận đã chế biến xong ({{ $order->total_items }} món)
                                                </button>
                                            </form>
                                        </div>
                                        <div class="card-footer text-muted d-flex justify-content-between">
                                            <span>
                                                Gửi đến bếp: {{ \Carbon\Carbon::parse($order->sent_to_kitchen_at)->format('H:i:s d/m/Y') }}
                                            </span>
                                            <span class="badge bg-info">
                                                Batch #{{ $order->batch_key }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Không có đơn hàng nào đang chờ chế biến.</strong>
                            <br>
                            <small class="text-muted">Trang sẽ tự động cập nhật khi có đơn mới.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động làm mới trang sau mỗi 30 giây
        setInterval(function() {
            location.reload();
        }, 30000);
        
        // Nút làm mới thủ công
        document.getElementById('refresh-btn').addEventListener('click', function() {
            location.reload();
        });
        
        // Hiệu ứng nhấp nháy cho các đơn mới (option)
        const cards = document.querySelectorAll('.card .card-header.bg-primary');
        cards.forEach(card => {
            const timeBadge = card.querySelector('.badge.bg-warning');
            if (timeBadge && timeBadge.textContent.includes('giây') || timeBadge.textContent.includes('phút')) {
                card.style.animation = 'pulse 2s infinite';
            }
        });
    });
    
    // CSS animation cho hiệu ứng nhấp nháy
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection