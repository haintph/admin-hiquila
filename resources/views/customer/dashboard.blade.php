@extends('customer.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Xin chào, {{ Auth::user()->name }}!</h1>

    <!-- Thống kê -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_tables'] }}</div>
            <div class="text-sm text-gray-600">Tổng bàn</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['available_tables'] }}</div>
            <div class="text-sm text-gray-600">Bàn trống</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['my_reservations'] }}</div>
            <div class="text-sm text-gray-600">Đặt bàn của tôi</div>
        </div>
        <div class="bg-white p-4 rounded shadow text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['areas_count'] }}</div>
            <div class="text-sm text-gray-600">Khu vực</div>
        </div>
    </div>

    <!-- Menu nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('customer.select-table') }}" 
           class="bg-white p-6 rounded shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="text-3xl mb-2">🍽️</div>
                <div class="font-semibold">Đặt bàn mới</div>
                <div class="text-sm text-gray-600">Chọn bàn và đặt trước</div>
            </div>
        </a>
        
        <a href="{{ route('customer.reservations') }}" 
           class="bg-white p-6 rounded shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="text-3xl mb-2">📋</div>
                <div class="font-semibold">Đặt bàn của tôi</div>
                <div class="text-sm text-gray-600">Xem và quản lý đặt bàn</div>
            </div>
        </a>
        
        <a href="{{ route('customer.floor-plan') }}" 
           class="bg-white p-6 rounded shadow hover:shadow-md transition-shadow">
            <div class="text-center">
                <div class="text-3xl mb-2">🗺️</div>
                <div class="font-semibold">Sơ đồ tầng</div>
                <div class="text-sm text-gray-600">Xem layout nhà hàng</div>
            </div>
        </a>
    </div>

    <!-- Đặt bàn hiện tại -->
    @if($myReservations->count() > 0)
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Đặt bàn hiện tại</h2>
        
        @foreach($myReservations as $table)
        <div class="border-b p-3 last:border-b-0">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-semibold">
                        Bàn {{ $table->table_number }}
                        @if($table->status == 'Đến muộn')
                            <span class="text-red-600 text-sm">(Đến muộn)</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-600">{{ $table->area->name ?? 'Không xác định' }}</div>
                    <div class="text-sm text-gray-500">
                        <strong>Thời gian:</strong> 
                        {{ $table->reserved_time ? $table->reserved_time->format('d/m/Y H:i') : 'Chưa xác định' }}
                    </div>
                    <div class="text-sm text-gray-500">
                        <strong>Số người:</strong> {{ $table->reserved_party_size ?? 'Chưa xác định' }} người
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('customer.reservations.cancel', $table->table_id) }}" 
                          onsubmit="return confirm('Bạn có chắc muốn hủy đặt bàn này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                            Hủy
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white p-8 rounded shadow text-center">
        <div class="text-4xl mb-4">🍽️</div>
        <h3 class="text-lg font-semibold mb-2">Chưa có đặt bàn nào</h3>
        <p class="text-gray-600 mb-4">Hãy đặt bàn để thưởng thức bữa ăn tuyệt vời!</p>
        <a href="{{ route('customer.select-table') }}" 
           class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Đặt bàn ngay
        </a>
    </div>
    @endif
</div>
@endsection