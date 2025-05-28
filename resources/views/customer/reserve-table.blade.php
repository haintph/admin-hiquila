@extends('customer.layouts.app')

@section('title', 'Đặt bàn - Hiquila Restaurant')

@section('content')
<!-- Hero Header -->
<div class="bg-gradient-to-r from-blue-600 to-teal-600 text-white py-8">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-2">🦞 Đặt bàn {{ $tableInfo['table_number'] }}</h1>
        <p class="text-xl opacity-90">Thưởng thức hải sản tươi ngon tại Hiquila Restaurant</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Thông tin bàn -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-teal-500 to-blue-500 text-white p-6">
                <h3 class="text-2xl font-bold flex items-center">
                    @switch($tableInfo['table_type'])
                        @case('Bàn VIP')
                            <span class="text-3xl mr-3">👑</span>
                            @break
                        @case('Bàn tròn')
                            <span class="text-3xl mr-3">⭕</span>
                            @break
                        @case('Bàn dài')
                            <span class="text-3xl mr-3">📏</span>
                            @break
                        @default
                            <span class="text-3xl mr-3">🍽️</span>
                    @endswitch
                    Thông tin bàn
                </h3>
                <p class="opacity-90 mt-1">Chi tiết bàn bạn đã chọn</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🔢</span>
                            <span class="font-semibold text-gray-700">Số bàn</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">{{ $tableInfo['table_number'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🏢</span>
                            <span class="font-semibold text-gray-700">Khu vực</span>
                        </div>
                        <span class="font-bold text-green-600">{{ $tableInfo['area_name'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🪑</span>
                            <span class="font-semibold text-gray-700">Loại bàn</span>
                        </div>
                        <span class="font-bold text-yellow-600">{{ $tableInfo['table_type'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">👥</span>
                            <span class="font-semibold text-gray-700">Sức chứa tối đa</span>
                        </div>
                        <span class="font-bold text-purple-600">{{ $tableInfo['capacity'] }} người</span>
                    </div>
                </div>

                @if($tableInfo['notes'])
                <div class="mt-4 p-4 bg-gray-50 rounded-lg border-l-4 border-gray-400">
                    <h5 class="font-bold text-gray-700 mb-2 flex items-center">
                        <span class="text-lg mr-2">📝</span>
                        Ghi chú bàn
                    </h5>
                    <p class="text-sm text-gray-600">{{ $tableInfo['notes'] }}</p>
                </div>
                @endif

                <!-- Đặc sản Hiquila -->
                <div class="mt-6 p-4 bg-gradient-to-r from-teal-50 to-blue-50 rounded-lg border-l-4 border-teal-400">
                    <h5 class="font-bold text-teal-800 mb-3 flex items-center">
                        <span class="text-xl mr-2">🦐</span>
                        Món đặc trưng Hiquila
                    </h5>
                    <div class="text-sm text-teal-700 space-y-1">
                        <div>🦞 Tôm hùm nướng phô mai</div>
                        <div>🦀 Cua rang me đặc biệt</div>
                        <div>🐟 Cá mú hấp xì dầu</div>
                        <div>🦑 Mực nướng sa tế</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form đặt bàn -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-teal-500 text-white p-6">
                <h3 class="text-2xl font-bold flex items-center">
                    <span class="text-3xl mr-3">📋</span>
                    Thông tin đặt bàn
                </h3>
                <p class="opacity-90 mt-1">Vui lòng điền đầy đủ thông tin</p>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('customer.reservations.store') }}">
                    @csrf
                    <input type="hidden" name="table_id" value="{{ $tableInfo['table_id'] }}">
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <span class="text-lg mr-2">👤</span>
                                Tên khách hàng *
                            </label>
                            <input type="text" 
                                   name="reserved_by" 
                                   value="{{ Auth::user()->name }}" 
                                   required
                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <span class="text-lg mr-2">📞</span>
                                Số điện thoại *
                            </label>
                            <input type="tel" 
                                   name="reserved_phone" 
                                   value="{{ Auth::user()->phone ?? '' }}" 
                                   required
                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">📅</span>
                                    Ngày đặt *
                                </label>
                                <input type="date" 
                                       name="reservation_date" 
                                       required
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                    <span class="text-lg mr-2">🕐</span>
                                    Giờ đặt *
                                </label>
                                <select name="reservation_time" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                                    <option value="">Chọn giờ</option>
                                    @for($hour = 8; $hour <= 22; $hour++)
                                        @foreach(['00', '30'] as $minute)
                                            @php 
                                                $time = sprintf('%02d:%s', $hour, $minute);
                                                $displayTime = sprintf('%02d:%s', $hour, $minute);
                                            @endphp
                                            <option value="{{ $time }}">{{ $displayTime }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <span class="text-lg mr-2">👥</span>
                                Số người * <span class="text-xs text-gray-500 ml-2">(Mặc định: {{ $tableInfo['capacity'] }} người)</span>
                            </label>
                            <select name="party_size" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                                @for($i = 1; $i <= $tableInfo['capacity']; $i++)
                                <option value="{{ $i }}" {{ $i == $tableInfo['capacity'] ? 'selected' : '' }}>
                                    {{ $i }} người {{ $i == $tableInfo['capacity'] ? '(Khuyến nghị)' : '' }}
                                </option>
                                @endfor
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                💡 Bàn này phù hợp nhất cho {{ $tableInfo['capacity'] }} người
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <span class="text-lg mr-2">📝</span>
                                Ghi chú đặc biệt
                            </label>
                            <textarea name="notes" 
                                      rows="4" 
                                      placeholder="VD: Sinh nhật, kỷ niệm, món ăn yêu thích, dị ứng thực phẩm..."
                                      class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors"></textarea>
                        </div>
                    </div>
                    
                    <!-- Cam kết -->
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                        <h5 class="font-bold text-yellow-800 mb-2 flex items-center">
                            <span class="text-lg mr-2">⚠️</span>
                            Lưu ý quan trọng
                        </h5>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Vui lòng đến đúng giờ hoặc sớm hơn 10 phút</li>
                            <li>• Chỉ có thể hủy đặt bàn trước 1 giờ</li>
                            <li>• Liên hệ 0901 234 567 nếu có thay đổi</li>
                            <li>• Hải sản tươi sống được chọn lọc hàng ngày</li>
                        </ul>
                    </div>
                    
                    <div class="flex gap-4 mt-8">
                        <a href="{{ route('customer.select-table') }}" 
                           class="flex-1 text-center py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors flex items-center justify-center">
                            <span class="text-lg mr-2">←</span>
                            Chọn bàn khác
                        </a>
                        
                        <button type="submit" 
                                class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-lg hover:from-blue-600 hover:to-teal-600 font-bold transition-all transform hover:scale-105 flex items-center justify-center">
                            <span class="text-lg mr-2">✅</span>
                            Xác nhận đặt bàn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Call to action -->
    <div class="mt-8 text-center">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="text-4xl mb-4">🎉</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Cảm ơn bạn đã chọn Hiquila!</h3>
            <p class="text-gray-600">Chúng tôi cam kết mang đến trải nghiệm ẩm thực hải sản tuyệt vời nhất</p>
        </div>
    </div>
</div>
@endsection