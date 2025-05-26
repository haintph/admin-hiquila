@extends('manager.layouts.master')
@section('content')
    <style>
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .role-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .role-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }

        .role-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px 12px 0 0;
        }

        .role-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 1rem;
        }

        .role-manager .role-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .role-staff .role-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .role-chef .role-icon {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .role-cashier .role-icon {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-control {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group-text {
            background: #f8fafc;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 8px 0 0 8px;
            font-weight: 600;
            color: #059669;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .preview-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 1rem;
        }

        .preview-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.25rem 0;
            font-size: 0.875rem;
        }

        .preview-total {
            border-top: 1px solid #0369a1;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            font-weight: 700;
            color: #0369a1;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .card-footer {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            border-radius: 0 0 12px 12px;
            padding: 1.5rem;
        }

        .money-format {
            text-align: right;
            font-weight: 600;
        }

        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }
    </style>

    <!-- Start Container Fluid -->
    <div class="container-xxl">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Cài Đặt Lương Nhân Viên</h4>
                            <p class="text-muted mb-0">Thiết lập lương cơ bản và lương theo giờ cho từng vị trí</p>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown">
                                Tùy chọn
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('salary.calculate') }}" class="dropdown-item">
                                    <i class="fas fa-calculator me-2"></i>Tính lương
                                </a>
                                <a href="{{ route('salary.history') }}" class="dropdown-item">
                                    <i class="fas fa-history me-2"></i>Lịch sử lương
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mx-3 mt-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('salary.updateSettings') }}" method="POST" id="salaryForm">
                        @csrf
                        <div class="card-body">
                            <div class="row g-4">
                                @php
                                    $roleData = [
                                        'manager' => ['name' => 'Quản Lý', 'icon' => '👨‍💼', 'class' => 'role-manager'],
                                        'staff' => ['name' => 'Nhân Viên', 'icon' => '👥', 'class' => 'role-staff'],
                                        'chef' => ['name' => 'Đầu Bếp', 'icon' => '👨‍🍳', 'class' => 'role-chef'],
                                        'cashier' => ['name' => 'Thu Ngân', 'icon' => '💰', 'class' => 'role-cashier'],
                                    ];
                                @endphp

                                @foreach ($roles as $role)
                                    @php
                                        $setting = $settings[$role] ?? null;
                                        $data = $roleData[$role];
                                    @endphp

                                    <div class="col-lg-6">
                                        <div class="card role-card {{ $data['class'] }}">
                                            <div class="role-header">
                                                <div class="d-flex align-items-center">
                                                    <div class="role-icon text-white">
                                                        {{ $data['icon'] }}
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0 fw-bold">{{ $data['name'] }}</h5>
                                                        <small class="text-muted">Cài đặt mức lương</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    <!-- Lương cơ bản -->
                                                    <div class="col-12">
                                                        <label class="form-label">
                                                            {{-- <i class="fas fa-money-bill-wave me-1 text-success"></i> --}}
                                                            Lương Cơ Bản (tháng)
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="text"
                                                                name="settings[{{ $role }}][base_salary]"
                                                                class="form-control salary-input money-format"
                                                                value="{{ $setting && $setting->base_salary > 0 ? number_format($setting->base_salary, 0, ',', '.') : '' }}"
                                                                data-role="{{ $role }}" data-type="base">
                                                        </div>
                                                    </div>

                                                    <!-- Lương theo giờ -->
                                                    <div class="col-12">
                                                        <label class="form-label">
                                                            {{-- <i class="fas fa-clock me-1 text-primary"></i> --}}
                                                            Lương Theo Giờ
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="text"
                                                                name="settings[{{ $role }}][hourly_rate]"
                                                                class="form-control salary-input money-format"
                                                                value="{{ $setting && $setting->hourly_rate > 0 ? number_format($setting->hourly_rate, 0, ',', '.') : '' }}"
                                                                data-role="{{ $role }}" data-type="hourly">
                                                        </div>
                                                    </div>

                                                    <!-- Giờ chuẩn -->
                                                    <div class="col-6">
                                                        <label class="form-label">
                                                            {{-- <i class="fas fa-calendar-alt me-1 text-info"></i> --}}
                                                            Giờ Chuẩn/Tháng
                                                        </label>
                                                        <input type="number"
                                                            name="settings[{{ $role }}][required_hours_per_month]"
                                                            class="form-control salary-input"
                                                            value="{{ $setting && $setting->required_hours_per_month > 0 ? $setting->required_hours_per_month : '' }}"
                                                            min="0" max="500" data-role="{{ $role }}"
                                                            data-type="hours">
                                                    </div>

                                                    <!-- Lương tăng ca -->
                                                    <div class="col-6">
                                                        <label class="form-label">
                                                            {{-- <i class="fas fa-fire me-1 text-warning"></i> --}}
                                                            Lương Tăng Ca (giờ)
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="text"
                                                                name="settings[{{ $role }}][overtime_rate]"
                                                                class="form-control salary-input money-format"
                                                                value="{{ $setting && $setting->overtime_rate > 0 ? number_format($setting->overtime_rate, 0, ',', '.') : '' }}"
                                                                data-role="{{ $role }}" data-type="overtime">
                                                        </div>
                                                    </div>

                                                    <!-- Preview tính lương -->
                                                    <div class="col-12 mt-4">
                                                        <div class="preview-card">
                                                            <div class="salary-preview" data-role="{{ $role }}">
                                                                <div class="preview-item">
                                                                    <span>Lương cơ bản:</span>
                                                                    <span class="fw-bold text-success base-amount">
                                                                        {{ number_format($setting->base_salary ?? 0, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                                <div class="preview-item">
                                                                    <span>Lương theo giờ:</span>
                                                                    <span class="fw-bold text-info hourly-amount">
                                                                        {{ number_format(($setting->hourly_rate ?? 0) * ($setting->required_hours_per_month ?? 240), 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                                <div class="preview-item preview-total">
                                                                    <span>Tổng ước tính:</span>
                                                                    <span class="fw-bold total-amount">
                                                                        {{ number_format(($setting->base_salary ?? 0) + ($setting->hourly_rate ?? 0) * ($setting->required_hours_per_month ?? 240), 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{-- <i class="fas fa-save me-2"></i>--}}Lưu Cài Đặt Lương
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container Fluid -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format tiền tệ cho input
            document.querySelectorAll('.money-format').forEach(input => {
                input.addEventListener('input', function(e) {
                    formatMoneyInput(this);
                    updateSalaryPreview(this.dataset.role);
                });

                input.addEventListener('blur', function(e) {
                    formatMoneyInput(this);
                });
            });

            // Format số giờ
            document.querySelectorAll('input[data-type="hours"]').forEach(input => {
                input.addEventListener('input', function() {
                    updateSalaryPreview(this.dataset.role);

                    // Cập nhật text hiển thị số giờ
                    const roleCard = this.closest('.role-card');
                    const requiredHoursSpan = roleCard.querySelector('.required-hours');
                    if (requiredHoursSpan) {
                        requiredHoursSpan.textContent = this.value || '240';
                    }
                });
            });

            function formatMoneyInput(input) {
                // Lấy giá trị và loại bỏ tất cả ký tự không phải số
                let value = input.value.replace(/[^\d]/g, '');

                if (value === '') {
                    input.value = '';
                    return;
                }

                // Chuyển thành số và format
                let number = parseInt(value);
                input.value = formatVNMoney(number);
            }

            function formatVNMoney(number) {
                return new Intl.NumberFormat('vi-VN', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            }

            function parseVNMoney(formatted) {
                if (!formatted || formatted.trim() === '') return 0;
                return parseInt(formatted.replace(/[^\d]/g, '')) || 0;
            }

            function updateSalaryPreview(role) {
                const baseSalaryInput = document.querySelector(`input[name="settings[${role}][base_salary]"]`);
                const hourlyRateInput = document.querySelector(`input[name="settings[${role}][hourly_rate]"]`);
                const requiredHoursInput = document.querySelector(
                    `input[name="settings[${role}][required_hours_per_month]"]`);

                const baseSalary = parseVNMoney(baseSalaryInput.value);
                const hourlyRate = parseVNMoney(hourlyRateInput.value);
                const requiredHours = parseInt(requiredHoursInput.value) || 240; // Mặc định 240

                const hourlyTotal = hourlyRate * requiredHours;
                const total = baseSalary + hourlyTotal;

                const preview = document.querySelector(`[data-role="${role}"]`);
                if (preview) {
                    const baseAmount = preview.querySelector('.base-amount');
                    const hourlyAmount = preview.querySelector('.hourly-amount');
                    const totalAmount = preview.querySelector('.total-amount');

                    if (baseAmount) baseAmount.textContent = formatVNMoney(baseSalary) + '₫';
                    if (hourlyAmount) hourlyAmount.textContent = formatVNMoney(hourlyTotal) + '₫';
                    if (totalAmount) totalAmount.textContent = formatVNMoney(total) + '₫';
                }
            }

            // Auto-calculate overtime rate khi thay đổi hourly rate
            document.querySelectorAll('input[data-type="hourly"]').forEach(input => {
                input.addEventListener('input', function() {
                    const role = this.dataset.role;
                    const overtimeInput = document.querySelector(
                        `input[name="settings[${role}][overtime_rate]"]`);
                    const hourlyRate = parseVNMoney(this.value);

                    if (overtimeInput && hourlyRate > 0) {
                        const overtimeRate = Math.round(hourlyRate * 1.5);
                        overtimeInput.value = formatVNMoney(overtimeRate);
                    }
                });
            });

            // Trước khi submit form, chuyển về số thô
            document.getElementById('salaryForm').addEventListener('submit', function(e) {
                document.querySelectorAll('.money-format').forEach(input => {
                    if (input.value) {
                        const rawValue = parseVNMoney(input.value);
                        input.value = rawValue;
                    }
                });

                console.log('Form submitted - empty values allowed');
            });
        });
    </script>
@endsection
