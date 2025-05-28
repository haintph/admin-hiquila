@extends('customer.layouts.app')

@section('title', 'Sơ đồ tầng - Hiquila Restaurant')

@section('content')
<!-- Hero Header -->
<div class="bg-gradient-to-r from-blue-600 to-teal-600 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-2">🗺️ Sơ đồ nhà hàng Hiquila</h1>
            <p class="text-xl opacity-90">Khám phá không gian và chọn bàn yêu thích</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6 space-y-8">
    <!-- Floor Selector -->
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="text-3xl mr-3">🏢</span>
                    Chọn tầng khám phá
                </h2>
                <p class="text-gray-600 ml-12">Hiện tại: <strong class="text-blue-600">Tầng {{ $floor }}</strong></p>
        </div>
            <div class="flex flex-wrap gap-3 ml-12 md:ml-0">
                @foreach($floors as $floorNum)
                <a href="{{ route('customer.floor-plan', $floorNum) }}" 
                   class="px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105
                          {{ $floor == $floorNum ? 'bg-gradient-to-r from-blue-500 to-teal-500 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    🏢 Tầng {{ $floorNum }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <span class="text-2xl mr-3">🎨</span>
            Chú thích trạng thái bàn
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
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
                    <div class="font-semibold text-red-700">Đang phục vụ</div>
                    <div class="text-xs text-red-600">Có khách ngồi</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-6 h-6 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full mr-3 shadow-sm"></div>
                <div>
                    <div class="font-semibold text-gray-700">Không hoạt động</div>
                    <div class="text-xs text-gray-600">Đang bảo trì</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floor Plan -->
    @if($areas->count() > 0)
    <div class="grid gap-8">
        @foreach($areas as $area)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Area Header -->
            <div class="bg-gradient-to-r from-teal-500 to-blue-500 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold flex items-center">
                            @if($area->is_vip)
                                <span class="text-3xl mr-3">👑</span>
                            @elseif($area->is_smoking)
                                <span class="text-3xl mr-3">🚬</span>
                            @else
                                <span class="text-3xl mr-3">🍽️</span>
                            @endif
                            {{ $area->name }} 
                            <span class="ml-2 text-lg opacity-80">({{ $area->code }})</span>
                        </h3>
                        <p class="opacity-90 mt-1">{{ $area->description ?? 'Khu vực phục vụ hải sản tươi ngon' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-80">Sức chứa khu vực</div>
                        <div class="text-2xl font-bold">{{ $area->capacity ?? 20 }} người</div>
                    </div>
                </div>
            </div>
            
            <!-- Tables Grid -->
            <div class="p-6">
                @if($area->tables->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
                    @foreach($area->tables as $table)
                    <div class="relative group">
                        <button onclick="selectTable({{ $table->table_id }})" 
                                class="w-full aspect-square rounded-xl border-2 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300 transform group-hover:shadow-xl
                                       {{ $table->status == 'Trống' ? 'bg-gradient-to-br from-green-100 to-green-200 border-green-300 hover:from-green-200 hover:to-green-300' : '' }}
                                       {{ $table->status == 'Đã đặt' ? 'bg-gradient-to-br from-yellow-100 to-yellow-200 border-yellow-300 hover:from-yellow-200 hover:to-yellow-300' : '' }}
                                       {{ in_array($table->status, ['Đến muộn', 'Đang phục vụ']) ? 'bg-gradient-to-br from-red-100 to-red-200 border-red-300 hover:from-red-200 hover:to-red-300' : '' }}
                                       {{ in_array($table->status, ['Bảo trì', 'Không hoạt động']) ? 'bg-gradient-to-br from-gray-100 to-gray-200 border-gray-300' : '' }}">
                            <div class="text-center p-3">
                                <div class="font-bold text-xl mb-1">{{ $table->table_number }}</div>
                                <div class="text-xs text-gray-600 mb-2 flex items-center justify-center">
                                    <span class="mr-1">👥</span>{{ $table->capacity }}
                                </div>
                                <div class="text-xs font-medium px-2 py-1 rounded-full
                                            {{ $table->status == 'Trống' ? 'text-green-700 bg-green-200' : '' }}
                                            {{ $table->status == 'Đã đặt' ? 'text-yellow-700 bg-yellow-200' : '' }}
                                            {{ in_array($table->status, ['Đến muộn', 'Đang phục vụ']) ? 'text-red-700 bg-red-200' : '' }}
                                            {{ in_array($table->status, ['Bảo trì', 'Không hoạt động']) ? 'text-gray-700 bg-gray-200' : '' }}">
                                    {{ $table->status }}
                                </div>
                                @if($table->reserved_time && in_array($table->status, ['Đã đặt', 'Đến muộn']))
                                <div class="text-xs text-gray-500 mt-2 flex items-center justify-center">
                                    <span class="mr-1">🕐</span>{{ $table->reserved_time->format('H:i') }}
                                </div>
                                @endif
                            </div>
                        </button>
                        
                        <!-- Table Type Icon with animation -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full shadow-lg flex items-center justify-center text-lg transform transition-transform group-hover:scale-125">
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
                                @case('Bàn đôi')
                                    💕
                                    @break
                                @default
                                    🪑
                            @endswitch
                        </div>

                        <!-- Available indicator for empty tables -->
                        @if($table->status == 'Trống')
                        <div class="absolute -top-1 -left-1 w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🍽️</div>
                    <h4 class="text-xl font-semibold text-gray-600 mb-2">Chưa có bàn nào</h4>
                    <p class="text-gray-500">Khu vực này đang được chuẩn bị</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <div class="text-8xl mb-6">🏢</div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">Tầng đang chuẩn bị</h3>
        <p class="text-xl text-gray-600 mb-6">Tầng này chưa có khu vực nào được thiết lập</p>
        <a href="{{ route('customer.dashboard') }}" 
           class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            🏠 Về trang chủ
        </a>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-md text-center border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600">{{ $areas->sum(function($area) { return $area->tables->where('status', 'Trống')->count(); }) }}</div>
            <div class="text-sm text-gray-600">Bàn trống</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ $areas->sum(function($area) { return $area->tables->where('status', 'Đã đặt')->count(); }) }}</div>
            <div class="text-sm text-gray-600">Đã đặt</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center border-l-4 border-red-500">
            <div class="text-2xl font-bold text-red-600">{{ $areas->sum(function($area) { return $area->tables->whereIn('status', ['Đang phục vụ', 'Đến muộn'])->count(); }) }}</div>
            <div class="text-sm text-gray-600">Đang phục vụ</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-md text-center border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-600">{{ $areas->sum(function($area) { return $area->tables->count(); }) }}</div>
            <div class="text-sm text-gray-600">Tổng bàn</div>
        </div>
    </div>
</div>

<!-- Enhanced Table Details Modal -->
<div id="tableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl transform transition-all">
        <div class="bg-gradient-to-r from-blue-500 to-teal-500 text-white p-6 rounded-t-2xl">
            <h3 class="text-2xl font-bold flex items-center">
                <span class="text-3xl mr-3">🍽️</span>
                Chi tiết bàn
            </h3>
        </div>
        <div class="p-6">
            <div id="tableDetails">
                <!-- Table details will be loaded here -->
            </div>
            <div class="flex justify-end space-x-3 mt-8">
                <button onclick="closeTableModal()" 
                        class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                    ❌ Đóng
                </button>
                <button id="reserveButton" onclick="openReservationForm()" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-500 text-white rounded-lg hover:from-blue-600 hover:to-teal-600 font-semibold transition-all transform hover:scale-105 hidden">
                    🦐 Đặt bàn ngay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Reservation Form Modal -->
<div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all">
        <div class="bg-gradient-to-r from-teal-500 to-blue-500 text-white p-6 rounded-t-2xl">
            <h3 class="text-2xl font-bold flex items-center">
                <span class="text-3xl mr-3">🦞</span>
                Đặt bàn Hiquila
            </h3>
            <p class="opacity-90 mt-1">Thưởng thức hải sản tươi ngon</p>
        </div>
        <div class="p-6">
            <form id="reservationForm" onsubmit="submitReservation(event)">
                @csrf
                <input type="hidden" id="selectedTableId" name="table_id">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">👤 Tên khách hàng *</label>
                        <input type="text" name="reserved_by" value="{{ Auth::user()->name }}" required
                               class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">📞 Số điện thoại *</label>
                        <input type="tel" name="reserved_phone" value="{{ Auth::user()->phone ?? '' }}" required
                               class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Ngày đặt *</label>
                            <input type="date" name="reservation_date" required
                                   value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">🕐 Giờ đặt *</label>
                            <input type="time" name="reservation_time" required
                                   class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">👥 Số người *</label>
                        <select name="party_size" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="">Chọn số người</option>
                            @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} người</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">📝 Ghi chú đặc biệt</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors"
                                  placeholder="VD: Sinh nhật, kỷ niệm, món ăn yêu thích..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeReservationModal()" 
                            class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                        ❌ Hủy bỏ
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-teal-500 to-blue-500 text-white rounded-lg hover:from-teal-600 hover:to-blue-600 font-semibold transition-all transform hover:scale-105">
                        ✅ Xác nhận đặt bàn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedTable = null;

function selectTable(tableId) {
    fetch(`/customer/api/table-info/${tableId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTableDetails(data.table);
                selectedTable = data.table;
            } else {
                alert('❌ Không thể lấy thông tin bàn');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showBasicTableModal(tableId);
        });
}

function showTableDetails(table) {
    const modal = document.getElementById('tableModal');
    const details = document.getElementById('tableDetails');
    const reserveButton = document.getElementById('reserveButton');
    
    details.innerHTML = `
        <div class="space-y-6">
            <div class="text-center bg-gray-50 p-4 rounded-xl">
                <h4 class="text-3xl font-bold text-gray-800">🍽️ Bàn ${table.table_number}</h4>
                <p class="text-gray-600 text-lg mt-1">${table.area ? table.area.name : 'Không xác định'}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl mb-1">🪑</div>
                    <div class="font-semibold text-gray-700">Loại bàn</div>
                    <div class="text-sm text-gray-600">${table.table_type}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl mb-1">👥</div>
                    <div class="font-semibold text-gray-700">Sức chứa</div>
                    <div class="text-sm text-gray-600">${table.capacity} người</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg text-center">
                    <div class="text-2xl mb-1">📊</div>
                    <div class="font-semibold text-gray-700">Trạng thái</div>
                    <div class="text-sm ${getStatusTextClass(table.status)}">${table.status}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <div class="text-2xl mb-1">${table.is_reservable ? '✅' : '❌'}</div>
                    <div class="font-semibold text-gray-700">Có thể đặt</div>
                    <div class="text-sm text-gray-600">${table.is_reservable ? 'Có' : 'Không'}</div>
                </div>
            </div>
            
            ${table.reserved_by ? `
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-xl border-l-4 border-yellow-400">
                <h5 class="font-bold text-yellow-800 mb-3 flex items-center">
                    <span class="text-xl mr-2">📋</span>
                    Thông tin đặt bàn
                </h5>
                <div class="space-y-2 text-sm">
                    <div><strong>👤 Khách:</strong> ${table.reserved_by}</div>
                    <div><strong>📞 SĐT:</strong> ${table.reserved_phone || 'N/A'}</div>
                    <div><strong>🕐 Thời gian:</strong> ${table.reserved_time ? new Date(table.reserved_time).toLocaleString('vi-VN') : 'N/A'}</div>
                    <div><strong>👥 Số người:</strong> ${table.reserved_party_size || 'N/A'} người</div>
                </div>
            </div>
            ` : ''}
            
            ${table.notes ? `
            <div class="bg-gray-50 p-4 rounded-xl border-l-4 border-gray-400">
                <h5 class="font-bold text-gray-700 mb-2 flex items-center">
                    <span class="text-xl mr-2">📝</span>
                    Ghi chú bàn
                </h5>
                <div class="text-sm text-gray-600">${table.notes}</div>
            </div>
            ` : ''}
        </div>
    `;
    
    if (table.status === 'Trống' && table.is_reservable) {
        reserveButton.classList.remove('hidden');
    } else {
        reserveButton.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
}

function showBasicTableModal(tableId) {
    document.getElementById('tableDetails').innerHTML = `
        <div class="text-center py-8">
            <div class="text-6xl mb-4">🍽️</div>
            <h4 class="text-2xl font-bold text-gray-800">Bàn #${tableId}</h4>
            <p class="text-gray-600 mt-2">Đang tải thông tin...</p>
        </div>
    `;
    document.getElementById('reserveButton').classList.add('hidden');
    document.getElementById('tableModal').classList.remove('hidden');
}

function getStatusTextClass(status) {
    switch(status) {
        case 'Trống': return 'text-green-600 font-semibold';
        case 'Đã đặt': return 'text-yellow-600 font-semibold';
        case 'Đến muộn': return 'text-red-600 font-semibold';
        case 'Đang phục vụ': return 'text-red-600 font-semibold';
        default: return 'text-gray-600';
    }
}

function closeTableModal() {
    document.getElementById('tableModal').classList.add('hidden');
}

function openReservationForm() {
    if (selectedTable) {
        document.getElementById('selectedTableId').value = selectedTable.table_id;
        closeTableModal();
        document.getElementById('reservationModal').classList.remove('hidden');
        
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const timeString = now.toTimeString().slice(0, 5);
        document.querySelector('input[name="reservation_time"]').value = timeString;
    }
}

function closeReservationModal() {
    document.getElementById('reservationModal').classList.add('hidden');
}

function submitReservation(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    fetch('/customer/reservations', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('🎉 ' + data.message);
            closeReservationModal();
            location.reload();
        } else {
            alert('❌ Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Có lỗi xảy ra khi đặt bàn');
    });
}

// Auto refresh every 60 seconds instead of 30 for better UX
setInterval(function() {
    location.reload();
}, 60000);
</script>
@endpush