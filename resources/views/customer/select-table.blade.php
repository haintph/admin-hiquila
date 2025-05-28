@extends('customer.layouts.app')

@section('title', 'Chọn bàn - Hiquila Restaurant')

@section('content')
<!-- Hero Header -->
<div class="bg-gradient-to-r from-blue-600 to-teal-600 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-2">🦞 Chọn bàn tại Hiquila</h1>
            <p class="text-xl opacity-90">Tìm bàn phù hợp để thưởng thức hải sản tươi ngon</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Thống kê nhanh -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border-l-4 border-blue-500">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-2xl mr-3">📊</span>
            Tình trạng bàn hiện tại
        </h2>
        <div class="grid grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total_tables'] }}</div>
                <div class="text-sm font-semibold text-gray-700 flex items-center justify-center">
                    <span class="text-lg mr-1">🪑</span>
                    Tổng số bàn
                </div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['available_tables'] }}</div>
                <div class="text-sm font-semibold text-gray-700 flex items-center justify-center">
                    <span class="text-lg mr-1">✅</span>
                    Có thể đặt
                </div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $stats['reserved_tables'] }}</div>
                <div class="text-sm font-semibold text-gray-700 flex items-center justify-center">
                    <span class="text-lg mr-1">📅</span>
                    Đã được đặt
                </div>
            </div>
        </div>
    </div>

    <!-- Chú thích trạng thái -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-xl mr-2">🎨</span>
            Chú thích trạng thái bàn
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="flex items-center p-3 bg-green-50 rounded-lg">
                <div class="w-6 h-6 bg-gradient-to-r from-green-400 to-green-500 rounded-full mr-3 shadow-sm"></div>
                <div>
                    <div class="font-semibold text-green-700">Bàn trống</div>
                    <div class="text-xs text-green-600">Sẵn sàng đặt bàn</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                <div class="w-6 h-6 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full mr-3 shadow-sm"></div>
                <div>
                    <div class="font-semibold text-yellow-700">Đã đặt trước</div>
                    <div class="text-xs text-yellow-600">Có khách đặt bàn</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-red-50 rounded-lg">
                <div class="w-6 h-6 bg-gradient-to-r from-red-400 to-red-500 rounded-full mr-3 shadow-sm"></div>
                <div>
                    <div class="font-semibold text-red-700">Không thể đặt</div>
                    <div class="text-xs text-red-600">Đang phục vụ/bảo trì</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách bàn theo khu vực -->
    @foreach($tablesByArea as $areaName => $tables)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <!-- Area Header -->
        <div class="bg-gradient-to-r from-teal-500 to-blue-500 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold flex items-center">
                        <span class="text-3xl mr-3">🍽️</span>
                        {{ $areaName }}
                    </h2>
                    <p class="opacity-90 mt-1">Khu vực phục vụ hải sản tươi ngon</p>
                </div>
                <div class="text-right">
                    <div class="text-sm opacity-80">Tổng bàn khu vực</div>
                    <div class="text-2xl font-bold">{{ count($tables) }}</div>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
                @foreach($tables as $table)
                <div class="group relative">
                    <div class="border-2 rounded-xl p-4 text-center transition-all duration-300 hover:scale-105 hover:shadow-lg
                                {{ $table['is_available'] ? 'border-green-300 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200' : 
                                   ($table['status'] == 'Đã đặt' ? 'border-yellow-300 bg-gradient-to-br from-yellow-50 to-yellow-100' : 
                                   'border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100') }}">
                        
                        <!-- Table Number & Icon -->
                        <div class="relative mb-2">
                            <div class="font-bold text-xl mb-1">{{ $table['table_number'] }}</div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-sm">
                                @switch($table['table_type'])
                                    @case('Bàn VIP')
                                        👑
                                        @break
                                    @case('Bàn tròn')
                                        ⭕
                                        @break
                                    @case('Bàn dài')
                                        📏
                                        @break
                                    @case('Bàn đôi')
                                        💕
                                        @break
                                    @default
                                        🪑
                                @endswitch
                            </div>
                        </div>

                        <!-- Capacity -->
                        <div class="text-sm text-gray-600 mb-2 flex items-center justify-center">
                            <span class="mr-1">👥</span>{{ $table['capacity'] }} người
                        </div>

                        <!-- Table Type -->
                        <div class="text-xs text-gray-500 mb-2 truncate">{{ $table['table_type'] }}</div>

                        <!-- Status -->
                        <div class="text-xs font-medium px-2 py-1 rounded-full mb-3
                                    {{ $table['is_available'] ? 'text-green-700 bg-green-200' : 
                                       ($table['status'] == 'Đã đặt' ? 'text-yellow-700 bg-yellow-200' : 'text-gray-700 bg-gray-200') }}">
                            {{ $table['status'] }}
                        </div>

                        <!-- Reserved Time -->
                        @if($table['reserved_time'])
                        <div class="text-xs text-blue-600 mb-3 flex items-center justify-center">
                            <span class="mr-1">🕐</span>{{ $table['reserved_time'] }}
                        </div>
                        @endif

                        <!-- Action Button -->
                        @if($table['is_available'])
                            <a href="{{ route('customer.reserve-table', $table['table_id']) }}"
                               class="block w-full py-2 px-3 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg text-sm font-semibold hover:from-green-600 hover:to-teal-600 transition-all transform group-hover:scale-105">
                                🦐 Đặt ngay
                            </a>
                        @else
                            <div class="w-full py-2 px-3 bg-gray-200 text-gray-500 rounded-lg text-sm font-semibold">
                                ❌ Không thể đặt
                            </div>
                        @endif

                        <!-- Available indicator -->
                        @if($table['is_available'])
                        <div class="absolute -top-1 -left-1 w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-2xl font-bold mb-4 flex items-center">
            <span class="text-3xl mr-3">⚡</span>
            Hành động nhanh
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('customer.floor-plan') }}" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition-all transform hover:scale-105 flex items-center">
                <span class="text-2xl mr-3">🗺️</span>
                <div>
                    <div class="font-semibold">Xem sơ đồ tầng</div>
                    <div class="text-sm opacity-80">Layout chi tiết nhà hàng</div>
                </div>
            </a>
            <a href="{{ route('customer.reservations') }}" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition-all transform hover:scale-105 flex items-center">
                <span class="text-2xl mr-3">📋</span>
                <div>
                    <div class="font-semibold">Đặt bàn của tôi</div>
                    <div class="text-sm opacity-80">Quản lý các đặt bàn</div>
                </div>
            </a>
            <a href="{{ route('customer.dashboard') }}" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition-all transform hover:scale-105 flex items-center">
                <span class="text-2xl mr-3">🏠</span>
                <div>
                    <div class="font-semibold">Về trang chủ</div>
                    <div class="text-sm opacity-80">Dashboard Hiquila</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Đặc sản Hiquila -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-3xl mr-3">🦐</span>
            Những món không thể bỏ lỡ tại Hiquila
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-teal-50 p-4 rounded-lg text-center">
                <div class="text-3xl mb-2">🦞</div>
                <div class="font-semibold text-gray-800">Tôm hùm nướng</div>
                <div class="text-sm text-gray-600">Phô mai đặc biệt</div>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-lg text-center">
                <div class="text-3xl mb-2">🦀</div>
                <div class="font-semibold text-gray-800">Cua rang me</div>
                <div class="text-sm text-gray-600">Vị ngọt tự nhiên</div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-blue-50 p-4 rounded-lg text-center">
                <div class="text-3xl mb-2">🐟</div>
                <div class="font-semibold text-gray-800">Cá mú hấp</div>
                <div class="text-sm text-gray-600">Xì dầu thơm lừng</div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-lg text-center">
                <div class="text-3xl mb-2">🦑</div>
                <div class="font-semibold text-gray-800">Mực nướng</div>
                <div class="text-sm text-gray-600">Sa tế cay nồng</div>
            </div>
        </div>
    </div>

    <!-- Tips & Info -->
    <div class="bg-yellow-50 rounded-xl shadow-lg p-6 border-l-4 border-yellow-400">
        <h3 class="text-xl font-bold text-yellow-800 mb-4 flex items-center">
            <span class="text-2xl mr-3">💡</span>
            Gợi ý chọn bàn
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-yellow-700">
            <div class="flex items-start">
                <span class="text-lg mr-2">👑</span>
                <div>
                    <div class="font-semibold">Bàn VIP</div>
                    <div>Không gian riêng tư, view đẹp, phục vụ chu đáo</div>
                </div>
            </div>
            <div class="flex items-start">
                <span class="text-lg mr-2">⭕</span>
                <div>
                    <div class="font-semibold">Bàn tròn</div>
                    <div>Phù hợp gia đình, nhóm bạn, dễ trò chuyện</div>
                </div>
            </div>
            <div class="flex items-start">
                <span class="text-lg mr-2">📏</span>
                <div>
                    <div class="font-semibold">Bàn dài</div>
                    <div>Thoải mái, phù hợp nhóm đông người</div>
                </div>
            </div>
            <div class="flex items-start">
                <span class="text-lg mr-2">💕</span>
                <div>
                    <div class="font-semibold">Bàn đôi</div>
                    <div>Lãng mạn, thích hợp cho cặp đôi</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection