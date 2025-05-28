@extends('customer.layouts.app')

@section('title', 'Chọn bàn')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Chọn bàn để đặt</h1>

    <!-- Stats đơn giản -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-xl font-bold text-blue-600">{{ $stats['total_tables'] }}</div>
                <div class="text-sm text-gray-600">Tổng bàn</div>
            </div>
            <div>
                <div class="text-xl font-bold text-green-600">{{ $stats['available_tables'] }}</div>
                <div class="text-sm text-gray-600">Có thể đặt</div>
            </div>
            <div>
                <div class="text-xl font-bold text-yellow-600">{{ $stats['reserved_tables'] }}</div>
                <div class="text-sm text-gray-600">Đã đặt</div>
            </div>
        </div>
    </div>

    <!-- Chú thích -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <div class="flex gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span>Trống</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                <span>Đã đặt</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span>Không thể đặt</span>
            </div>
        </div>
    </div>

    <!-- Danh sách bàn theo khu vực -->
    @foreach($tablesByArea as $areaName => $tables)
    <div class="bg-white p-4 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ $areaName }}</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($tables as $table)
            <div class="border p-3 rounded text-center
                {{ $table['is_available'] ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                
                <div class="font-bold text-lg">{{ $table['table_number'] }}</div>
                <div class="text-sm text-gray-600">{{ $table['capacity'] }} người</div>
                <div class="text-xs text-gray-500 mb-2">{{ $table['table_type'] }}</div>
                
                <div class="text-xs mb-2
                    {{ $table['is_available'] ? 'text-green-700' : 'text-gray-700' }}">
                    {{ $table['status'] }}
                </div>
                
                @if($table['reserved_time'])
                <div class="text-xs text-blue-600 mb-2">{{ $table['reserved_time'] }}</div>
                @endif
                
                @if($table['is_available'])
                    <a href="{{ route('customer.reserve-table', $table['table_id']) }}" 
                       class="block w-full py-1 px-2 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                        Đặt bàn
                    </a>
                @else
                    <div class="py-1 px-2 bg-gray-300 text-gray-500 rounded text-sm">
                        Không thể đặt
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Nút quay lại -->
    <div class="text-center">
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Quay lại Dashboard
        </a>
    </div>
</div>
@endsection