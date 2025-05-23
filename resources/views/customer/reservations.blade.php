@extends('customer.layouts.app')

@section('title', 'Đặt bàn của tôi')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Đặt bàn của tôi</h1>

    <!-- Thống kê nhanh -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-lg font-semibold">Tổng cộng: {{ $reservations->count() }} đặt bàn</span>
            </div>
            <div>
                <a href="{{ route('customer.select-table') }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + Đặt bàn mới
                </a>
            </div>
        </div>
    </div>

    <!-- Danh sách đặt bàn -->
    @if($reservations->count() > 0)
        @foreach($reservations as $table)
        <div class="bg-white p-4 rounded shadow mb-4 
                    {{ $table->status == 'Đến muộn' ? 'border-l-4 border-red-500' : 'border-l-4 border-green-500' }}">
            
            <div class="flex justify-between items-start">
                <!-- Thông tin bàn -->
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <h3 class="text-xl font-semibold">Bàn {{ $table->table_number }}</h3>
                        <span class="ml-3 px-2 py-1 rounded text-sm 
                                    {{ $table->status == 'Đã đặt' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $table->status }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><strong>Khu vực:</strong> {{ $table->area->name ?? 'Không xác định' }}</p>
                            <p><strong>Loại bàn:</strong> {{ $table->table_type }}</p>
                            <p><strong>Sức chứa:</strong> {{ $table->capacity }} người</p>
                        </div>
                        <div>
                            <p><strong>Thời gian đặt:</strong> 
                                <span class="text-blue-600 font-semibold">
                                    {{ $table->reserved_time ? $table->reserved_time->format('d/m/Y H:i') : 'Chưa xác định' }}
                                </span>
                            </p>
                            <p><strong>Số người:</strong> {{ $table->reserved_party_size ?? 'Chưa xác định' }} người</p>
                            <p><strong>Đặt lúc:</strong> {{ $table->reserved_at ? $table->reserved_at->format('d/m/Y H:i') : 'Chưa xác định' }}</p>
                        </div>
                    </div>

                    @if($table->reservation_notes)
                    <div class="mt-3 p-2 bg-gray-50 rounded">
                        <p class="text-sm"><strong>Ghi chú:</strong> {{ $table->reservation_notes }}</p>
                    </div>
                    @endif

                    <!-- Thông báo đặc biệt -->
                    @if($table->status == 'Đến muộn')
                    <div class="mt-3 p-2 bg-red-50 rounded">
                        <p class="text-sm text-red-700">
                            ⚠️ <strong>Đến muộn:</strong> Vui lòng liên hệ nhà hàng để xác nhận bàn.
                        </p>
                    </div>
                    @endif

                    @if($table->reserved_time && $table->reserved_time->isFuture() && $table->status == 'Đã đặt')
                    <div class="mt-3 p-2 bg-blue-50 rounded">
                        <p class="text-sm text-blue-700">
                            ℹ️ <strong>Nhắc nhở:</strong> Vui lòng đến đúng giờ hoặc sớm hơn 10 phút.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Nút hành động -->
                <div class="ml-4">
                    @php
                        $canCancel = $table->reserved_time && $table->reserved_time->diffInMinutes(now(), false) >= 30;
                    @endphp
                    
                    @if(in_array($table->status, ['Đã đặt', 'Đến muộn']) && $canCancel)
                        <form method="POST" action="{{ route('customer.reservations.cancel', $table->table_id) }}" 
                              onsubmit="return confirm('Bạn có chắc muốn hủy đặt bàn này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                Hủy đặt
                            </button>
                        </form>
                    @elseif(in_array($table->status, ['Đã đặt', 'Đến muộn']))
                        <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded text-sm">
                            Không thể hủy
                        </span>
                        <p class="text-xs text-gray-500 mt-1 text-center">
                            (Chỉ hủy trước 30 phút)
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    @else
    <!-- Không có đặt bàn -->
    <div class="bg-white p-8 rounded shadow text-center">
        <div class="text-6xl mb-4">🍽️</div>
        <h3 class="text-xl font-semibold mb-2">Chưa có đặt bàn nào</h3>
        <p class="text-gray-600 mb-6">Bạn chưa có đặt bàn nào. Hãy đặt bàn để thưởng thức bữa ăn tuyệt vời!</p>
        
        <div class="space-x-4">
            <a href="{{ route('customer.select-table') }}" 
               class="inline-block px-6 py-3 bg-blue-500 text-white rounded hover:bg-blue-600">
                Chọn bàn đặt trước
            </a>
            <a href="{{ route('customer.dashboard') }}" 
               class="inline-block px-6 py-3 bg-gray-500 text-white rounded hover:bg-gray-600">
                Về Dashboard
            </a>
        </div>
    </div>
    @endif

    <!-- Thông tin liên hệ -->
    <div class="bg-white p-4 rounded shadow mt-6">
        <h3 class="font-semibold mb-2">📞 Thông tin liên hệ</h3>
        <div class="text-sm text-gray-600">
            <p><strong>Hotline:</strong> 0901 234 567</p>
            <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, TP.HCM</p>
            <p><strong>Giờ hoạt động:</strong> 8:00 - 23:00 hàng ngày</p>
        </div>
    </div>

    <!-- Nút quay lại -->
    <div class="text-center mt-6">
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-block px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ← Quay lại Dashboard
        </a>
    </div>
</div>
@endsection