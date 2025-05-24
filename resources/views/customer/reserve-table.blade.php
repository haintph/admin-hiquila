@extends('customer.layouts.app')

@section('title', 'Đặt bàn')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
    <h1 class="text-2xl font-bold mb-6">Đặt bàn {{ $tableInfo['table_number'] }}</h1>

    <!-- Thông tin bàn -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <h3 class="font-semibold mb-3">Thông tin bàn</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Số bàn:</span>
                <span class="font-semibold">{{ $tableInfo['table_number'] }}</span>
            </div>
            <div class="flex justify-between">
                <span>Khu vực:</span>
                <span>{{ $tableInfo['area_name'] }}</span>
            </div>
            <div class="flex justify-between">
                <span>Loại bàn:</span>
                <span>{{ $tableInfo['table_type'] }}</span>
            </div>
            <div class="flex justify-between">
                <span>Sức chứa:</span>
                <span>{{ $tableInfo['capacity'] }} người</span>
            </div>
        </div>
    </div>

    <!-- Form đặt bàn -->
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">Thông tin đặt bàn</h3>
        
        <form method="POST" action="{{ route('customer.reservations.store') }}">
            @csrf
            <input type="hidden" name="table_id" value="{{ $tableInfo['table_id'] }}">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tên khách hàng *</label>
                    <input type="text" 
                           name="reserved_by" 
                           value="{{ Auth::user()->name }}" 
                           required
                           class="w-full border rounded px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Số điện thoại *</label>
                    <input type="tel" 
                           name="reserved_phone" 
                           value="{{ Auth::user()->phone ?? '' }}" 
                           required
                           class="w-full border rounded px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Ngày đặt *</label>
                    <input type="date" 
                           name="reservation_date" 
                           required
                           min="{{ date('Y-m-d') }}"
                           value="{{ date('Y-m-d') }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Giờ đặt *</label>
                    <select name="reservation_time" required class="w-full border rounded px-3 py-2">
                        <option value="">Chọn giờ</option>
                        @for($hour = 8; $hour <= 22; $hour++)
                            @foreach(['00', '30'] as $minute)
                                @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Số người *</label>
                    <select name="party_size" required class="w-full border rounded px-3 py-2">
                        <option value="">Chọn số người</option>
                        @for($i = 1; $i <= $tableInfo['capacity']; $i++)
                        <option value="{{ $i }}">{{ $i }} người</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Ghi chú</label>
                    <textarea name="notes" 
                              rows="3" 
                              placeholder="Yêu cầu đặc biệt..."
                              class="w-full border rounded px-3 py-2"></textarea>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <a href="{{ route('customer.select-table') }}" 
                   class="flex-1 text-center py-2 border rounded text-gray-600 hover:bg-gray-50">
                    Chọn bàn khác
                </a>
                
                <button type="submit" 
                        class="flex-1 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Đặt bàn
                </button>
            </div>
        </form>
    </div>
</div>
@endsection