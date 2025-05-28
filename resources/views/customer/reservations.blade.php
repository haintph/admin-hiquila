@extends('customer.layouts.app')

@section('title', 'Đặt bàn của tôi - Hiquila Restaurant')

@section('content')
<!-- Hero Header -->
<div class="bg-gradient-to-r from-teal-600 to-blue-600 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-2">🦐 Đặt bàn của tôi</h1>
            <p class="text-xl opacity-90">Quản lý các đặt bàn tại Hiquila Restaurant</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Thống kê và Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border-l-4 border-teal-500">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <div class="text-4xl mr-4">📋</div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $reservations->count() }} đặt bàn</div>
                    <div class="text-gray-600">Tổng số đặt bàn hiện tại</div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customer.select-table') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-lg hover:from-blue-600 hover:to-teal-600 font-semibold transition-all transform hover:scale-105 flex items-center">
                    <span class="text-xl mr-2">🦞</span>
                    Đặt bàn mới
                </a>
                <a href="{{ route('customer.floor-plan') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-colors flex items-center">
                    <span class="text-xl mr-2">🗺️</span>
                    Xem sơ đồ
                </a>
            </div>
        </div>
    </div>

    <!-- Danh sách đặt bàn -->
    @if($reservations->count() > 0)
        <div class="space-y-6">
            @foreach($reservations as $table)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all hover:shadow-xl
                        {{ $table->status == 'Đến muộn' ? 'border-l-4 border-red-500' : 'border-l-4 border-green-500' }}">
                
                <!-- Header Card -->
                <div class="bg-gradient-to-r {{ $table->status == 'Đến muộn' ? 'from-red-500 to-red-600' : 'from-green-500 to-teal-500' }} text-white p-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="text-3xl mr-3">
                                @switch($table->table_type)
                                    @case('Bàn VIP')
                                        👑
                                        @break
                                    @case('Bàn tròn')
                                        ⭕
                                        @break
                                    @case('Bàn dài')
                                        📏
                                        @break
                                    @default
                                        🍽️
                                @endswitch
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Bàn {{ $table->table_number }}</h3>
                                <p class="opacity-90">{{ $table->area->name ?? 'Không xác định' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                                {{ $table->status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Thông tin chi tiết -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Thông tin bàn -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                <span class="text-xl mr-2">🪑</span>
                                Thông tin bàn
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Loại bàn:</span>
                                    <span class="font-semibold">{{ $table->table_type }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sức chứa:</span>
                                    <span class="font-semibold">{{ $table->capacity }} người</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Khu vực:</span>
                                    <span class="font-semibold">{{ $table->area->name ?? 'Không xác định' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin đặt bàn -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                <span class="text-xl mr-2">📅</span>
                                Chi tiết đặt bàn
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Thời gian:</span>
                                    <span class="font-semibold text-blue-600">
                                        {{ $table->reserved_time ? $table->reserved_time->format('d/m/Y H:i') : 'Chưa xác định' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Số người:</span>
                                    <span class="font-semibold">{{ $table->reserved_party_size ?? 'Chưa xác định' }} người</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Đặt lúc:</span>
                                    <span class="font-semibold">{{ $table->reserved_at ? $table->reserved_at->format('d/m/Y H:i') : 'Chưa xác định' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ghi chú -->
                    @if($table->reservation_notes)
                    <div class="bg-yellow-50 rounded-lg p-4 mb-4 border-l-4 border-yellow-400">
                        <h5 class="font-bold text-yellow-800 mb-2 flex items-center">
                            <span class="text-lg mr-2">📝</span>
                            Ghi chú đặc biệt
                        </h5>
                        <p class="text-sm text-yellow-700">{{ $table->reservation_notes }}</p>
                    </div>
                    @endif

                    <!-- Thông báo đặc biệt -->
                    @if($table->status == 'Đến muộn')
                    <div class="bg-red-50 rounded-lg p-4 mb-4 border-l-4 border-red-400">
                        <p class="text-sm text-red-700 flex items-center">
                            <span class="text-lg mr-2">⚠️</span>
                            <strong>Đến muộn:</strong> Vui lòng liên hệ nhà hàng để xác nhận bàn.
                        </p>
                    </div>
                    @endif

                    @if($table->reserved_time && $table->reserved_time->isFuture() && $table->status == 'Đã đặt')
                    <div class="bg-blue-50 rounded-lg p-4 mb-4 border-l-4 border-blue-400">
                        <p class="text-sm text-blue-700 flex items-center">
                            <span class="text-lg mr-2">ℹ️</span>
                            <strong>Nhắc nhở:</strong> Vui lòng đến đúng giờ hoặc sớm hơn 10 phút để thưởng thức hải sản tươi ngon nhất.
                        </p>
                    </div>
                    @endif

                    <!-- Nút hành động -->
                    <div class="flex justify-end">
                        @php
                            $canCancel = $table->reserved_time && $table->reserved_time->diffInMinutes(now(), false) >= 60;
                        @endphp
                        
                        @if(in_array($table->status, ['Đã đặt', 'Đến muộn']) && $canCancel)
                            <form method="POST" action="{{ route('customer.reservations.cancel', $table->table_id) }}" 
                                  onsubmit="return confirm('🤔 Bạn có chắc muốn hủy đặt bàn tại Hiquila Restaurant không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold transition-colors flex items-center">
                                    <span class="text-lg mr-2">❌</span>
                                    Hủy đặt bàn
                                </button>
                            </form>
                        @elseif(in_array($table->status, ['Đã đặt', 'Đến muộn']))
                            <div class="text-center">
                                <span class="px-6 py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold flex items-center">
                                    <span class="text-lg mr-2">🔒</span>
                                    Không thể hủy
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Chỉ có thể hủy trước 1 giờ
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
    <!-- Không có đặt bàn -->
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <div class="text-8xl mb-6">🦐</div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">Chưa có đặt bàn nào</h3>
        <p class="text-xl text-gray-600 mb-8">Bạn chưa có đặt bàn nào tại Hiquila Restaurant.<br>Hãy đặt bàn để thưởng thức hải sản tươi ngon nhất!</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('customer.select-table') }}" 
               class="px-8 py-4 bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-lg hover:from-blue-600 hover:to-teal-600 font-bold text-lg transition-all transform hover:scale-105 flex items-center justify-center">
                <span class="text-2xl mr-3">🦞</span>
                Chọn bàn đặt trước
            </a>
            <a href="{{ route('customer.dashboard') }}" 
               class="px-8 py-4 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold text-lg transition-colors flex items-center justify-center">
                <span class="text-2xl mr-3">🏠</span>
                Về trang chủ
            </a>
        </div>
    </div>
    @endif

    <!-- Thông tin liên hệ Hiquila -->
    <div class="bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-xl shadow-lg p-6 mt-8">
        <h3 class="text-2xl font-bold mb-4 flex items-center">
            <span class="text-3xl mr-3">📞</span>
            Liên hệ Hiquila Restaurant
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div class="flex items-center">
                <span class="text-2xl mr-3">☎️</span>
                <div>
                    <div class="font-semibold">Hotline đặt bàn</div>
                    <div class="opacity-90">0901 234 567</div>
                </div>
            </div>
            <div class="flex items-center">
                <span class="text-2xl mr-3">📍</span>
                <div>
                    <div class="font-semibold">Địa chỉ nhà hàng</div>
                    <div class="opacity-90">123 Đường Biển, Q1, TP.HCM</div>
                </div>
            </div>
            <div class="flex items-center">
                <span class="text-2xl mr-3">🕐</span>
                <div>
                    <div class="font-semibold">Giờ phục vụ</div>
                    <div class="opacity-90">8:00 - 23:00 hàng ngày</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="text-center mt-8">
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-colors">
            <span class="text-xl mr-2">←</span>
            Quay lại Dashboard
        </a>
    </div>
</div>
@endsection