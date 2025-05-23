@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Tạo Hóa Đơn Mới</h5>
            </div>
            <div class="card-body">
                <!-- Thông báo lỗi từ session -->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Lỗi:</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Thông báo thành công -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Thành công:</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Thông báo cảnh báo -->
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Cảnh báo:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Lỗi validation từ Laravel -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Vui lòng kiểm tra lại thông tin:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Thông báo thông tin -->
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Thông tin:</strong> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                    @csrf
                    
                    <!-- Thông tin khách hàng -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Thông tin khách hàng</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="customer_name" class="form-label">Tên khách hàng</label>
                                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                    value="{{ old('customer_name') }}" placeholder="Nhập tên khách hàng (tùy chọn)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="customer_phone" class="form-label">Số điện thoại</label>
                                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                                    value="{{ old('customer_phone') }}" placeholder="Nhập số điện thoại (tùy chọn)" pattern="[0-9]{10,11}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="party_size" class="form-label">Số lượng khách <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="party_size" name="party_size"
                                                    value="{{ old('party_size') }}" min="1" max="20" required placeholder="Nhập số lượng khách">
                                                <div class="form-text">Tối đa 20 khách</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="special_notes" class="form-label">Ghi chú đặc biệt</label>
                                                <textarea class="form-control" id="special_notes" name="special_notes" rows="2"
                                                    placeholder="Ghi chú về yêu cầu đặc biệt (tùy chọn)">{{ old('special_notes') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Hướng dẫn</h6>
                                </div>
                                <div class="card-body">
                                    <ol class="mb-0">
                                        <li>Nhập số lượng khách</li>
                                        <li>Chọn tầng và khu vực</li>
                                        <li>Chọn bàn phù hợp</li>
                                        <li>Nhấn "Tạo Hóa Đơn"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chọn tầng -->
                    <div class="mb-4">
                        <h5 class="mb-3">Chọn Tầng</h5>
                        <div class="btn-group" role="group" aria-label="Chọn tầng">
                            @foreach ($floors as $floor)
                                <a href="{{ route('invoices.create', ['floor' => $floor]) }}" 
                                   class="btn {{ $floor == $selectedFloor ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tầng {{ $floor }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chọn khu vực -->
                    <div class="mb-4">
                        <h5 class="mb-3">Chọn Khu Vực</h5>
                        <div class="row g-3">
                            @foreach ($areas as $area)
                                <div class="col-md-4">
                                    <a href="{{ route('invoices.create', ['floor' => $selectedFloor, 'area_id' => $area->area_id]) }}"
                                        class="text-decoration-none">
                                        <div class="card {{ $area->area_id == $area_id ? 'border-primary bg-light' : 'border-secondary' }}">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $area->name }}</h6>
                                                <div class="mb-2">
                                                    <span class="badge bg-primary">{{ $area->code }}</span>
                                                    @if ($area->is_smoking)
                                                        <span class="badge bg-warning text-dark">Hút thuốc</span>
                                                    @endif
                                                    @if ($area->is_vip)
                                                        <span class="badge bg-danger">VIP</span>
                                                    @endif
                                                </div>
                                                <span class="badge bg-success">
                                                    {{ $area->tables->where('status', 'Trống')->count() }} bàn trống
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chọn bàn -->
                    <div class="mb-4">
                        <h5 class="mb-3">Chọn Bàn</h5>
                        @if (count($tables) > 0)
                            <div class="row g-3">
                                @foreach ($tables as $table)
                                    <div class="col-md-3">
                                        <div class="card table-option">
                                            <div class="card-body text-center">
                                                <!-- Radio button ẩn -->
                                                <input type="radio" name="table_id" value="{{ $table->table_id }}" 
                                                       id="table_{{ $table->table_id }}" class="table-radio d-none"
                                                       {{ old('table_id') == $table->table_id ? 'checked' : '' }} required>
                                                
                                                <!-- Label chính để click -->
                                                <label for="table_{{ $table->table_id }}" class="table-label w-100 cursor-pointer">
                                                    <!-- Biểu tượng bàn -->
                                                    <div class="table-visual mb-2" 
                                                         style="background-color: {{ $tableTypes[$table->table_type]['color'] ?? '#f8f9fa' }}; 
                                                                border-radius: {{ $tableTypes[$table->table_type]['shape'] == 'circle' ? '50%' : 
                                                                  ($tableTypes[$table->table_type]['shape'] == 'rectangle' ? '8px/20px' : '8px') }};">
                                                        {{ $table->table_number }}
                                                    </div>
                                                    
                                                    <!-- Thông tin bàn -->
                                                    <h6 class="card-title">Bàn {{ $table->table_number }}</h6>
                                                    <p class="card-text mb-1">
                                                        <span class="badge bg-info">{{ $table->capacity }} người</span>
                                                    </p>
                                                    <p class="card-text">
                                                        <small class="text-muted">{{ $table->table_type }}</small>
                                                    </p>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Không có bàn trống trong khu vực này. Vui lòng chọn khu vực khác.
                            </div>
                        @endif
                    </div>

                    <!-- Chú thích loại bàn -->
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Chú thích loại bàn</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($tableTypes as $type => $style)
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="table-legend-icon"
                                                     style="background-color: {{ $style['color'] }}; 
                                                            border-radius: {{ $style['shape'] == 'circle' ? '50%' : 
                                                              ($style['shape'] == 'rectangle' ? '3px/10px' : '3px') }};"></div>
                                                <span class="ms-2">{{ $type }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút submit -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Tạo Hóa Đơn
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-lg ms-3">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .table-option {
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
        }

        .table-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Khi radio được chọn */
        .table-radio:checked + .table-label .table-option,
        .table-radio:checked ~ .card {
            border-color: #198754;
            background-color: #d1e7dd;
        }

        /* Style cho biểu tượng bàn */
        .table-visual {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #212529;
            border: 2px solid #fff;
        }

        /* Style cho chú thích */
        .table-legend-icon {
            width: 20px;
            height: 20px;
            border: 1px solid #ddd;
        }

        // CSS cho các alert đẹp hơn
        .alert {
            border-left: 4px solid;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .alert-danger {
            border-left-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            border-left-color: #198754;
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .alert-warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
            color: #664d03;
        }

        .alert-info {
            border-left-color: #0dcaf0;
            background-color: #cff4fc;
            color: #055160;
        }

        /* Animation cho alert */
        .alert.fade.show {
            animation: slideInDown 0.3s ease-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Loading state cho submit button */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Đánh dấu bàn được chọn */
        .table-radio:checked + .table-label {
            color: #198754;
        }

        .table-radio:checked + .table-label .card {
            border-color: #198754 !important;
            background-color: #d1e7dd !important;
        }

        .table-radio:checked + .table-label .table-visual {
            border-color: #198754;
            box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
        }
    </style>

    <script>
        // Chỉ cần một chút JavaScript để cải thiện UX
        document.addEventListener('DOMContentLoaded', function() {
            // Tự động focus vào trường số lượng khách
            const partySizeInput = document.getElementById('party_size');
            if (partySizeInput && !partySizeInput.value) {
                partySizeInput.focus();
            }

            // Kiểm tra sức chứa bàn khi chọn
            const tableRadios = document.querySelectorAll('.table-radio');
            const partySizeField = document.getElementById('party_size');

            function checkTableCapacity() {
                const partySize = parseInt(partySizeField.value) || 0;
                
                tableRadios.forEach(radio => {
                    const label = radio.nextElementSibling;
                    const card = label.querySelector('.card') || label.closest('.card');
                    const capacityBadge = label.querySelector('.badge');
                    
                    if (capacityBadge) {
                        const capacity = parseInt(capacityBadge.textContent.match(/\d+/)[0]);
                        
                        // Reset styles
                        card.style.opacity = '1';
                        radio.disabled = false;
                        
                        if (partySize > 0) {
                            if (partySize > capacity) {
                                // Bàn không đủ chỗ
                                card.style.opacity = '0.5';
                                radio.disabled = true;
                                if (radio.checked) {
                                    radio.checked = false;
                                }
                            }
                        }
                    }
                });
            }

            // Kiểm tra khi thay đổi số lượng khách
            if (partySizeField) {
                partySizeField.addEventListener('input', checkTableCapacity);
                // Kiểm tra ngay khi load trang
                checkTableCapacity();
            }

            // Hàm hiển thị thông báo lỗi tùy chỉnh
            function showAlert(type, title, message, autoHide = true) {
                const alertContainer = document.querySelector('.card-body');
                const alertId = 'alert-' + Date.now();
                
                const iconMap = {
                    'error': 'fas fa-exclamation-triangle',
                    'success': 'fas fa-check-circle', 
                    'warning': 'fas fa-exclamation-circle',
                    'info': 'fas fa-info-circle'
                };
                
                const alertClass = type === 'error' ? 'danger' : type;
                const icon = iconMap[type] || 'fas fa-info-circle';
                
                const alertHTML = `
                    <div id="${alertId}" class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                        <i class="${icon} me-2"></i>
                        <strong>${title}:</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                // Thêm alert vào đầu card-body
                alertContainer.insertAdjacentHTML('afterbegin', alertHTML);
                
                // Tự động ẩn sau 5 giây nếu autoHide = true
                if (autoHide && type !== 'error') {
                    setTimeout(() => {
                        const alertElement = document.getElementById(alertId);
                        if (alertElement) {
                            const bsAlert = new bootstrap.Alert(alertElement);
                            bsAlert.close();
                        }
                    }, 5000);
                }
                
                // Scroll to top để hiển thị alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Validation chi tiết cho form
            function validateForm() {
                const partySize = parseInt(partySizeField.value) || 0;
                const selectedTable = document.querySelector('.table-radio:checked');
                const customerName = document.getElementById('customer_name').value.trim();
                const customerPhone = document.getElementById('customer_phone').value.trim();
                
                // Kiểm tra số lượng khách
                if (partySize === 0) {
                    showAlert('error', 'Lỗi nhập liệu', 'Vui lòng nhập số lượng khách hợp lệ');
                    partySizeField.focus();
                    return false;
                }
                
                if (partySize < 1 || partySize > 20) {
                    showAlert('error', 'Lỗi nhập liệu', 'Số lượng khách phải từ 1 đến 20 người');
                    partySizeField.focus();
                    return false;
                }
                
                // Kiểm tra chọn bàn
                if (!selectedTable) {
                    showAlert('error', 'Chưa chọn bàn', 'Vui lòng chọn một bàn phù hợp');
                    document.querySelector('.table-option').scrollIntoView({ behavior: 'smooth' });
                    return false;
                }
                
                // Kiểm tra sức chứa bàn
                const capacityBadge = selectedTable.nextElementSibling.querySelector('.badge');
                if (capacityBadge) {
                    const capacity = parseInt(capacityBadge.textContent.match(/\d+/)[0]);
                    if (partySize > capacity) {
                        showAlert('error', 'Sức chứa không đủ', 
                            `Bàn này chỉ có sức chứa ${capacity} người, không đủ cho ${partySize} khách. Vui lòng chọn bàn khác hoặc giảm số lượng khách.`);
                        return false;
                    }
                }
                
                // Validation số điện thoại (nếu có nhập)
                if (customerPhone && !/^[0-9]{10,11}$/.test(customerPhone)) {
                    showAlert('warning', 'Định dạng không đúng', 'Số điện thoại phải có 10-11 chữ số');
                    document.getElementById('customer_phone').focus();
                    return false;
                }
                
                // Thông báo thành công validation
                showAlert('success', 'Thông tin hợp lệ', 'Đang tạo hóa đơn...', false);
                return true;
            }
        });
    </script>
@endsection