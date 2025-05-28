@extends('customer.layouts.app')

@section('title', 'Trang chủ - Hiquila Restaurant')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-teal-600 text-white py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-2">🦐 Hiquila Restaurant</h1>
        <p class="text-xl mb-4">Nhà hàng hải sản tươi ngon hàng đầu</p>
        <p class="text-lg opacity-90">Xin chào, {{ Auth::user()->name }}! Chào mừng bạn đến với Hiquila</p>
    </div>
</div>

<div class="container mx-auto px-4 -mt-8">
    <!-- Thống kê -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center border-l-4 border-blue-500">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total_tables'] }}</div>
            <div class="text-sm text-gray-600">🪑 Tổng số bàn</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg text-center border-l-4 border-green-500">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['available_tables'] }}</div>
            <div class="text-sm text-gray-600">✅ Bàn còn trống</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg text-center border-l-4 border-orange-500">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ $stats['my_reservations'] }}</div>
            <div class="text-sm text-gray-600">📅 Đặt bàn của tôi</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg text-center border-l-4 border-purple-500">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ $stats['areas_count'] }}</div>
            <div class="text-sm text-gray-600">🏢 Khu vực phục vụ</div>
        </div>
    </div>

    <!-- Dịch vụ chính -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🍤 Dịch vụ của chúng tôi</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('customer.select-table') }}" 
               class="group bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🦞</div>
                    <div class="font-bold text-xl text-gray-800 mb-2">Đặt bàn mới</div>
                    <div class="text-gray-600">Đặt trước bàn để thưởng thức hải sản tươi ngon</div>
                    <div class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded-full text-sm group-hover:bg-blue-600 transition-colors">
                        Đặt ngay →
                    </div>
                </div>
            </a>
            
            <a href="{{ route('customer.reservations') }}" 
               class="group bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🦀</div>
                    <div class="font-bold text-xl text-gray-800 mb-2">Quản lý đặt bàn</div>
                    <div class="text-gray-600">Xem và quản lý các đặt bàn hiện tại của bạn</div>
                    <div class="mt-4 inline-block px-4 py-2 bg-green-500 text-white rounded-full text-sm group-hover:bg-green-600 transition-colors">
                        Xem chi tiết →
                    </div>
                </div>
            </a>
            
            <a href="{{ route('customer.floor-plan') }}" 
               class="group bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🐟</div>
                    <div class="font-bold text-xl text-gray-800 mb-2">Sơ đồ nhà hàng</div>
                    <div class="text-gray-600">Khám phá layout và các khu vực của Hiquila</div>
                    <div class="mt-4 inline-block px-4 py-2 bg-purple-500 text-white rounded-full text-sm group-hover:bg-purple-600 transition-colors">
                        Khám phá →
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Thông tin nhà hàng -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Đặc trưng nhà hàng -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="text-2xl mr-2">🌊</span>
                Đặc trưng Hiquila
            </h3>
            <div class="space-y-3">
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🦐</span>
                    <span>Hải sản tươi sống được chọn lọc hàng ngày</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">👨‍🍳</span>
                    <span>Đầu bếp chuyên nghiệp với kinh nghiệm 15+ năm</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🏖️</span>
                    <span>Không gian ven biển thoáng mát, view đẹp</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">⭐</span>
                    <span>Dịch vụ 5 sao, phục vụ chu đáo</span>
                </div>
            </div>
        </div>

        <!-- Món ăn nổi bật -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="text-2xl mr-2">🍽️</span>
                Món ăn đặc trưng
            </h3>
            <div class="space-y-3">
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🦞</span>
                    <span>Tôm hùm nướng phô mai - Đặc sản của nhà</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🦀</span>
                    <span>Cua rang me - Vị ngọt tự nhiên đậm đà</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🐟</span>
                    <span>Cá mú hấp xì dầu - Tươi ngon, thơm lừng</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <span class="text-lg mr-3">🦑</span>
                    <span>Mực nướng sa tế - Cay nồng, hấp dẫn</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to action -->
    <div class="bg-gradient-to-r from-teal-500 to-blue-600 text-white p-8 rounded-lg shadow-lg text-center">
        <h3 class="text-2xl font-bold mb-4">🎉 Sẵn sàng thưởng thức bữa ăn tuyệt vời?</h3>
        <p class="text-lg mb-6">Đặt bàn ngay hôm nay để không bỏ lỡ những món hải sản tươi ngon nhất!</p>
        <div class="space-x-4">
            <a href="{{ route('customer.select-table') }}" 
               class="inline-block px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-colors">
                🍤 Đặt bàn ngay
            </a>
            <a href="{{ route('customer.floor-plan') }}" 
               class="inline-block px-8 py-3 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-blue-600 transition-colors">
                🗺️ Xem sơ đồ
            </a>
        </div>
    </div>
</div>
@endsection