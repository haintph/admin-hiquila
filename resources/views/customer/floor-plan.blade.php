@extends('customer.layouts.app')


@section('title', 'Sơ đồ tầng')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Sơ đồ tầng</h1>
        <p class="text-gray-600">Chọn bàn và đặt bàn trực tiếp</p>
    </div>

    <!-- Floor Selector -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Chọn tầng</h2>
                <p class="text-gray-600">Hiện tại đang xem: Tầng {{ $floor }}</p>
            </div>
            <div class="flex space-x-2">
                @foreach($floors as $floorNum)
                <a href="{{ route('customer.floor-plan', $floorNum) }}" 
                   class="px-4 py-2 rounded-lg {{ $floor == $floorNum ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Tầng {{ $floorNum }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Chú thích</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm">Trống</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                <span class="text-sm">Đã đặt</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-sm">Đến muộn / Đang phục vụ</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-gray-500 rounded mr-2"></div>
                <span class="text-sm">Không hoạt động</span>
            </div>
        </div>
    </div>

    <!-- Floor Plan -->
    @if($areas->count() > 0)
    <div class="grid gap-6">
        @foreach($areas as $area)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                {{ $area->name }} ({{ $area->code }})
            </h3>
            
            @if($area->tables->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($area->tables as $table)
                <div class="relative">
                    <button onclick="selectTable({{ $table->table_id }})" 
                            class="w-full aspect-square rounded-lg border-2 transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500
                                   {{ $table->status == 'Trống' ? 'bg-green-100 border-green-300 hover:bg-green-200' : '' }}
                                   {{ $table->status == 'Đã đặt' ? 'bg-yellow-100 border-yellow-300' : '' }}
                                   {{ in_array($table->status, ['Đến muộn', 'Đang phục vụ']) ? 'bg-red-100 border-red-300' : '' }}
                                   {{ in_array($table->status, ['Bảo trì', 'Không hoạt động']) ? 'bg-gray-100 border-gray-300' : '' }}">
                        <div class="text-center p-2">
                            <div class="font-bold text-lg">{{ $table->table_number }}</div>
                            <div class="text-xs text-gray-600">{{ $table->capacity }} người</div>
                            <div class="text-xs mt-1 font-medium
                                        {{ $table->status == 'Trống' ? 'text-green-700' : '' }}
                                        {{ $table->status == 'Đã đặt' ? 'text-yellow-700' : '' }}
                                        {{ in_array($table->status, ['Đến muộn', 'Đang phục vụ']) ? 'text-red-700' : '' }}
                                        {{ in_array($table->status, ['Bảo trì', 'Không hoạt động']) ? 'text-gray-700' : '' }}">
                                {{ $table->status }}
                            </div>
                            @if($table->reserved_time && in_array($table->status, ['Đã đặt', 'Đến muộn']))
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $table->reserved_time->format('H:i') }}
                            </div>
                            @endif
                        </div>
                    </button>
                    
                    <!-- Table Type Icon -->
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-xs">
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
                                🪑
                        @endswitch
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Không có bàn nào trong khu vực này</p>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <div class="text-6xl mb-4">🏢</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Không có khu vực nào</h3>
        <p class="text-gray-600">Tầng này chưa có khu vực nào được thiết lập.</p>
    </div>
    @endif
</div>

<!-- Table Details Modal -->
<div id="tableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Chi tiết bàn</h3>
            <div id="tableDetails">
                <!-- Table details will be loaded here -->
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeTableModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Đóng
                </button>
                <button id="reserveButton" onclick="openReservationForm()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 hidden">
                    Đặt bàn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Form Modal -->
<div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Đặt bàn</h3>
            <form id="reservationForm" onsubmit="submitReservation(event)">
                @csrf
                <input type="hidden" id="selectedTableId" name="table_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên khách hàng *</label>
                        <input type="text" name="reserved_by" value="{{ Auth::user()->name }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại *</label>
                        <input type="tel" name="reserved_phone" value="{{ Auth::user()->phone ?? '' }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ngày đặt *</label>
                        <input type="date" name="reservation_date" required
                               value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giờ đặt *</label>
                        <input type="time" name="reservation_time" required
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số người *</label>
                        <select name="party_size" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Chọn số người</option>
                            @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} người</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ghi chú</label>
                        <textarea name="notes" rows="3" 
                                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                                  placeholder="Yêu cầu đặc biệt (không bắt buộc)"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeReservationModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Xác nhận đặt bàn
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
    // Get table info via AJAX
    fetch(`/customer/api/table-info/${tableId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTableDetails(data.table);
                selectedTable = data.table;
            } else {
                alert('Không thể lấy thông tin bàn');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: show basic modal
            showBasicTableModal(tableId);
        });
}

function showTableDetails(table) {
    const modal = document.getElementById('tableModal');
    const details = document.getElementById('tableDetails');
    const reserveButton = document.getElementById('reserveButton');
    
    details.innerHTML = `
        <div class="space-y-3">
            <div class="text-center">
                <h4 class="text-2xl font-bold">Bàn ${table.table_number}</h4>
                <p class="text-gray-600">${table.area ? table.area.name : 'Không xác định'}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Loại bàn:</strong><br>
                    ${table.table_type}
                </div>
                <div>
                    <strong>Sức chứa:</strong><br>
                    ${table.capacity} người
                </div>
                <div>
                    <strong>Trạng thái:</strong><br>
                    <span class="px-2 py-1 rounded text-xs ${getStatusClass(table.status)}">
                        ${table.status}
                    </span>
                </div>
                <div>
                    <strong>Có thể đặt:</strong><br>
                    ${table.is_reservable ? 'Có' : 'Không'}
                </div>
            </div>
            
            ${table.reserved_by ? `
            <div class="bg-yellow-50 p-3 rounded-md">
                <strong>Thông tin đặt bàn:</strong><br>
                <small>Khách: ${table.reserved_by}</small><br>
                <small>SĐT: ${table.reserved_phone || 'N/A'}</small><br>
                <small>Thời gian: ${table.reserved_time ? new Date(table.reserved_time).toLocaleString('vi-VN') : 'N/A'}</small><br>
                <small>Số người: ${table.reserved_party_size || 'N/A'}</small>
            </div>
            ` : ''}
            
            ${table.notes ? `
            <div class="bg-gray-50 p-3 rounded-md">
                <strong>Ghi chú:</strong><br>
                <small>${table.notes}</small>
            </div>
            ` : ''}
        </div>
    `;
    
    // Show reserve button only if table is available
    if (table.status === 'Trống' && table.is_reservable) {
        reserveButton.classList.remove('hidden');
    } else {
        reserveButton.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
}

function showBasicTableModal(tableId) {
    // Basic fallback when AJAX fails
    document.getElementById('tableDetails').innerHTML = `
        <div class="text-center">
            <h4 class="text-2xl font-bold">Bàn #${tableId}</h4>
            <p class="text-gray-600">Đang tải thông tin...</p>
        </div>
    `;
    document.getElementById('reserveButton').classList.add('hidden');
    document.getElementById('tableModal').classList.remove('hidden');
}

function getStatusClass(status) {
    switch(status) {
        case 'Trống': return 'bg-green-100 text-green-800';
        case 'Đã đặt': return 'bg-yellow-100 text-yellow-800';
        case 'Đến muộn': return 'bg-red-100 text-red-800';
        case 'Đang phục vụ': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
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
        
        // Set default time to current time + 1 hour
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
            alert(data.message);
            closeReservationModal();
            location.reload(); // Refresh to show updated table status
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đặt bàn');
    });
}

// Auto refresh every 30 seconds to update table status
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush