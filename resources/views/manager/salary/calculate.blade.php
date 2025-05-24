@extends('manager.layouts.master')
@section('content')
    <!-- Start Container Fluid -->
    <div class="container-xxl">

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">Bảng Tính Lương Nhân Viên</h4>

                        <form method="GET" action="{{ route('salary.calculate') }}" class="d-flex gap-2 me-2">
                            <select name="month" class="form-select form-select-sm">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                        Tháng {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <select name="year" class="form-select form-select-sm">
                                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                Tính Lương
                            </button>
                        </form>

                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Tháng {{ $month }}/{{ $year }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('salary.settings') }}" class="dropdown-item">Cài đặt lương</a>
                                <a href="{{ route('salary.history') }}" class="dropdown-item">Lịch sử lương</a>
                                <a href="#!" class="dropdown-item">Export Excel</a>
                            </div>
                        </div>
                    </div>
                    
                    @if (session('success'))
                        <div class="alert alert-success mx-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(isset($results) && count($results) > 0)
                        <form action="{{ route('salary.save') }}" method="POST" id="salaryForm">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            
                            <div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover table-centered">
                                        <thead class="bg-light-subtle">
                                            <tr>
                                                <th style="width: 20px;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="customCheck1">
                                                        <label class="form-check-label" for="customCheck1"></label>
                                                    </div>
                                                </th>
                                                <th>Nhân Viên</th>
                                                <th>Chức Vụ</th>
                                                <th>Giờ Làm Việc</th>
                                                <th>Chi Tiết Lương</th>
                                                <th>Thưởng</th>
                                                <th>Trừ</th>
                                                <th>Tổng Lương</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalSalaryAll = 0; @endphp
                                            @foreach($results as $result)
                                                @if(!isset($result['error']))
                                                    @php $totalSalaryAll += $result['total_salary']; @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $result['user']->id }}">
                                                                <label class="form-check-label" for="customCheck{{ $result['user']->id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                                    <img src="{{ asset('storage/' . $result['user']->avatar) }}" alt="" class="avatar-md">
                                                                </div>
                                                                <p class="text-dark fw-medium fs-15 mb-0">{{ $result['user']->name }}</p>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @switch($result['user']->role)
                                                                @case('manager')
                                                                    <span class="badge bg-primary">Quản lý</span>
                                                                    @break
                                                                @case('staff')
                                                                    <span class="badge bg-info">Nhân viên</span>
                                                                    @break
                                                                @case('chef')
                                                                    <span class="badge bg-warning">Đầu bếp</span>
                                                                    @break
                                                                @case('cashier')
                                                                    <span class="badge bg-success">Thu ngân</span>
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        <td onclick="toggleWorkDetails({{ $result['user']->id }})" style="cursor: pointer;">
                                                            <span class="fw-bold">{{ number_format($result['total_hours'], 1) }}h</span>
                                                            @if($result['overtime_hours'] > 0)
                                                                <small class="text-warning d-block">+{{ number_format($result['overtime_hours'], 1) }}h tăng ca</small>
                                                            @endif
                                                            <small class="text-muted">{{ $result['days_worked'] }} ngày</small>
                                                        </td>
                                                        <td style="cursor: pointer;">
                                                            <span class="fw-bold text-primary">{{ number_format($result['total_salary'], 0, ',', '.') }}₫</span>
                                                        </td>
                                                        {{-- <td onclick="toggleSalaryDetails({{ $result['user']->id }})" style="cursor: pointer;">
                                                            <span class="fw-bold text-primary">{{ number_format($result['total_salary'], 0, ',', '.') }}₫</span>
                                                            <small class="text-muted d-block">Click để xem chi tiết</small>
                                                        </td> --}}
                                                        <td>
                                                            <input type="text" 
                                                                   name="salary_data[{{ $result['user']->id }}][bonus]" 
                                                                   class="form-control form-control-sm money-input bonus-input"
                                                                   value="0"
                                                                   data-user-id="{{ $result['user']->id }}"
                                                                   placeholder="0"
                                                                   style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <input type="text" 
                                                                   name="salary_data[{{ $result['user']->id }}][deduction]" 
                                                                   class="form-control form-control-sm money-input deduction-input"
                                                                   value="0"
                                                                   data-user-id="{{ $result['user']->id }}"
                                                                   placeholder="0"
                                                                   style="width: 100px;">
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold text-success total-salary" 
                                                                  data-base="{{ $result['total_salary'] }}"
                                                                  data-user-id="{{ $result['user']->id }}">
                                                                {{ number_format($result['total_salary'], 0, ',', '.') }}₫
                                                            </span>
                                                            <div class="status-text text-muted" style="font-size: 0.75rem;">Chưa thay đổi</div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button type="button" class="btn btn-light btn-sm" onclick="viewDetails({{ $result['user']->id }})">
                                                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                                                </button>
                                                                {{-- <button type="button" class="btn btn-soft-primary btn-sm" onclick="editSalary({{ $result['user']->id }})">
                                                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                                </button> --}}
                                                            </div>
                                                        </td>
                                                        
                                                        <!-- Hidden inputs -->
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][base_salary]" value="{{ $result['base_salary'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][hourly_salary]" value="{{ $result['hourly_salary'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][overtime_salary]" value="{{ $result['overtime_salary'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][total_hours]" value="{{ $result['total_hours'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][overtime_hours]" value="{{ $result['overtime_hours'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][days_worked]" value="{{ $result['days_worked'] }}">
                                                        <input type="hidden" name="salary_data[{{ $result['user']->id }}][total_salary]" value="{{ $result['total_salary'] }}" class="final-total">
                                                    </tr>

                                                    <!-- Chi tiết giờ làm việc (ẩn) -->
                                                    <tr id="work-details-{{ $result['user']->id }}" style="display: none;">
                                                        <td colspan="9">
                                                            <div class="p-3" style="background-color: #f8f9fa;">
                                                                <h6 class="mb-2">Chi tiết giờ làm việc - {{ $result['user']->name }}</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <small class="text-muted">Giờ thường:</small>
                                                                        <div class="fw-bold">{{ number_format($result['total_hours'] - $result['overtime_hours'], 1) }} giờ</div>
                                                                    </div>
                                                                    @if($result['overtime_hours'] > 0)
                                                                        <div class="col-md-4">
                                                                            <small class="text-muted">Giờ tăng ca:</small>
                                                                            <div class="fw-bold text-warning">{{ number_format($result['overtime_hours'], 1) }} giờ</div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-4">
                                                                        <small class="text-muted">Tổng ngày làm:</small>
                                                                        <div class="fw-bold">{{ $result['days_worked'] }} ngày</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <!-- Chi tiết lương (ẩn) -->
                                                    <tr id="salary-details-{{ $result['user']->id }}" style="display: none;">
                                                        <td colspan="9">
                                                            <div class="p-3" style="background-color: #f0f9ff;">
                                                                <h6 class="mb-2">Chi tiết lương - {{ $result['user']->name }}</h6>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Lương cơ bản:</small>
                                                                        <div class="fw-bold">{{ number_format($result['base_salary'], 0, ',', '.') }}₫</div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Lương theo giờ:</small>
                                                                        <div class="fw-bold">{{ number_format($result['hourly_salary'], 0, ',', '.') }}₫</div>
                                                                    </div>
                                                                    @if($result['overtime_salary'] > 0)
                                                                        <div class="col-md-3">
                                                                            <small class="text-muted">Lương tăng ca:</small>
                                                                            <div class="fw-bold text-warning">{{ number_format($result['overtime_salary'], 0, ',', '.') }}₫</div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Tổng cộng:</small>
                                                                        <div class="fw-bold text-primary">{{ number_format($result['total_salary'], 0, ',', '.') }}₫</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="9" class="text-center text-danger p-4">
                                                            <strong>{{ $result['user']->name }}:</strong> {{ $result['error'] }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Validation Warning -->
                            <div id="validation-summary" style="display: none;" class="mx-3 mb-3">
                                <div class="alert alert-warning">
                                    <strong>Cảnh báo:</strong> <span id="validation-message"></span>
                                </div>
                            </div>

                            <div class="card-footer border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Tổng chi phí lương tháng {{ $month }}/{{ $year }}: </strong>
                                    <span class="text-success fw-bold fs-5" id="grand-total">
                                        {{ number_format($totalSalaryAll, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <button type="submit" class="btn btn-primary" id="save-btn">
                                    Lưu Bảng Lương
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="card-body text-center py-5">
                            <div class="text-muted">
                                <iconify-icon icon="solar:calculator-minimalistic-broken" class="fs-1 mb-3 opacity-50"></iconify-icon>
                                <h4>Chưa có dữ liệu tính lương</h4>
                                <p>Vui lòng chọn tháng và năm để tính toán lương nhân viên</p>
                                <a href="{{ route('salary.settings') }}" class="btn btn-primary">
                                    Cài đặt lương trước
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <!-- End Container Fluid -->

    <!-- ========== Footer Start ========== -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <script>
                        document.write(new Date().getFullYear())
                    </script> &copy; Larkon. Crafted by
                    <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon>
                    <a href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- ========== Footer End ========== -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatVNMoney(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }
    
    function parseVNMoney(value) {
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    }
    
    // Money input handlers
    document.querySelectorAll('.money-input').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = formatVNMoney(parseInt(value));
            }
            updateSalaryCalculation();
        });
    });
    
    function updateSalaryCalculation() {
        let grandTotal = 0;
        let hasNegative = false;
        let negativeCount = 0;
        
        document.querySelectorAll('tbody tr').forEach(row => {
            const bonusInput = row.querySelector('.bonus-input');
            const deductionInput = row.querySelector('.deduction-input');
            const totalSalarySpan = row.querySelector('.total-salary');
            const finalTotalInput = row.querySelector('.final-total');
            const statusText = row.querySelector('.status-text');
            
            if (bonusInput && deductionInput && totalSalarySpan) {
                const baseSalary = parseFloat(totalSalarySpan.dataset.base) || 0;
                const bonus = parseVNMoney(bonusInput.value);
                const deduction = parseVNMoney(deductionInput.value);
                const finalTotal = baseSalary + bonus - deduction;
                
                totalSalarySpan.textContent = formatVNMoney(finalTotal) + '₫';
                if (finalTotalInput) {
                    finalTotalInput.value = finalTotal;
                }
                
                // Update status and styling
                if (finalTotal < 0) {
                    totalSalarySpan.className = 'fw-bold text-danger total-salary';
                    statusText.textContent = 'Lương âm!';
                    statusText.className = 'status-text text-danger';
                    hasNegative = true;
                    negativeCount++;
                } else {
                    totalSalarySpan.className = 'fw-bold text-success total-salary';
                    statusText.className = 'status-text text-muted';
                    
                    if (bonus > 0 || deduction > 0) {
                        statusText.textContent = bonus > 0 && deduction > 0 ? 'Có thưởng & trừ' : 
                                               bonus > 0 ? 'Có thưởng' : 'Có trừ';
                    } else {
                        statusText.textContent = 'Chưa thay đổi';
                    }
                }
                
                grandTotal += finalTotal;
            }
        });
        
        // Update grand total
        const grandTotalElement = document.getElementById('grand-total');
        grandTotalElement.textContent = formatVNMoney(grandTotal) + '₫';
        
        if (grandTotal < 0) {
            grandTotalElement.className = 'text-danger fw-bold fs-5';
        } else {
            grandTotalElement.className = 'text-success fw-bold fs-5';
        }
        
        // Show/hide validation warning
        const validationSummary = document.getElementById('validation-summary');
        const validationMessage = document.getElementById('validation-message');
        const saveBtn = document.getElementById('save-btn');
        
        if (hasNegative) {
            validationSummary.style.display = 'block';
            validationMessage.textContent = `${negativeCount} nhân viên có lương âm. Vui lòng kiểm tra lại.`;
            saveBtn.disabled = true;
            saveBtn.className = 'btn btn-secondary';
        } else {
            validationSummary.style.display = 'none';
            saveBtn.disabled = false;
            saveBtn.className = 'btn btn-primary';
        }
    }
    
    // Form submission
    document.getElementById('salaryForm').addEventListener('submit', function(e) {
        let hasNegative = false;
        
        document.querySelectorAll('.final-total').forEach(input => {
            if (parseFloat(input.value) < 0) {
                hasNegative = true;
            }
        });
        
        if (hasNegative) {
            e.preventDefault();
            alert('Không thể lưu bảng lương có nhân viên bị lương âm!');
            return false;
        }
        
        // Convert formatted values back to numbers
        document.querySelectorAll('.money-input').forEach(input => {
            if (input.value) {
                input.value = parseVNMoney(input.value);
            }
        });
        
        return confirm('Bạn có chắc chắn muốn lưu bảng lương này?');
    });
    
    updateSalaryCalculation();
});

// Toggle functions for showing/hiding details
function toggleWorkDetails(userId) {
    const detailRow = document.getElementById('work-details-' + userId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function toggleSalaryDetails(userId) {
    const detailRow = document.getElementById('salary-details-' + userId);
    if (detailRow.style.display === 'none') {
        detailRow.style.display = 'table-row';
    } else {
        detailRow.style.display = 'none';
    }
}

function viewDetails(userId) {
    // Toggle both work and salary details
    toggleWorkDetails(userId);
    toggleSalaryDetails(userId);
}

function editSalary(userId) {
    const bonusInput = document.querySelector(`input[data-user-id="${userId}"].bonus-input`);
    const deductionInput = document.querySelector(`input[data-user-id="${userId}"].deduction-input`);
    
    if (bonusInput) bonusInput.focus();
}
</script>
@endsection