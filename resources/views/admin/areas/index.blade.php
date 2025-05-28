@extends('admin.layouts.master')

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý toast notifications
            setTimeout(function() {
                const toasts = document.querySelectorAll('.toast.show');
                toasts.forEach(toast => {
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.hide();
                });
            }, 5000);

            // Xử lý lỗi khi tải ảnh
            const images = document.querySelectorAll('img.avatar-md');
            images.forEach(img => {
                img.onerror = function() {
                    // Thay thế ảnh lỗi bằng icon
                    const parent = this.parentElement;
                    this.style.display = 'none';
                    const icon = document.createElement('iconify-icon');
                    icon.setAttribute('icon', 'solar:map-point-cafe-broken');
                    icon.setAttribute('class', 'fs-24');
                    parent.appendChild(icon);
                };
            });

            // Xử lý bulk delete
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const checkAllBox = document.getElementById('checkAll');
            const tableCheckboxes = document.querySelectorAll('input[name="area_ids[]"]');

            if (checkAllBox && tableCheckboxes.length > 0 && bulkDeleteBtn) {
                // Xử lý select all
                checkAllBox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    tableCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkDeleteButton();
                });

                // Xử lý từng checkbox
                tableCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateBulkDeleteButton();
                        // Kiểm tra nếu tất cả được chọn, thì chọn "checkAll"
                        const allChecked = Array.from(tableCheckboxes).every(cb => cb.checked);
                        checkAllBox.checked = allChecked;
                    });
                });

                // Xử lý nút xóa hàng loạt
                bulkDeleteBtn.addEventListener('click', function() {
                    const checkedCount = document.querySelectorAll('input[name="area_ids[]"]:checked')
                        .length;
                    if (checkedCount > 0 && confirm(
                            `Bạn có chắc chắn muốn xóa ${checkedCount} khu vực đã chọn?`)) {
                        bulkDeleteForm.submit();
                    }
                });

                function updateBulkDeleteButton() {
                    const checkedCount = document.querySelectorAll('input[name="area_ids[]"]:checked').length;
                    bulkDeleteBtn.disabled = checkedCount === 0;
                    bulkDeleteBtn.innerHTML = checkedCount > 0 ?
                        `<iconify-icon icon="solar:trash-bin-trash-broken" class="me-1"></iconify-icon> Xóa ${checkedCount} khu vực đã chọn` :
                        `<iconify-icon icon="solar:trash-bin-trash-broken" class="me-1"></iconify-icon> Xóa đã chọn`;
                }
            }
            
            // Xử lý click vào ô giờ hoạt động - Chuyển đến trang quản lý giờ riêng
            document.querySelectorAll('.hours-column').forEach(function(cell) {
                cell.addEventListener('click', function() {
                    const areaId = this.getAttribute('data-area-id');
                    if (areaId) {
                        // Chuyển đến trang quản lý giờ hoạt động riêng
                        window.location.href = `/admin/areas/${areaId}/manage-hours`;
                    }
                });
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .time-slot-item {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-active {
            background-color: #198754;
        }

        .status-inactive {
            background-color: #dc3545;
        }

        /* Style cho ô giờ hoạt động */
        .hours-column {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .hours-column:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .surcharge-badge {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .capacity-info {
            font-size: 0.9rem;
        }

        .floor-badge {
            min-width: 35px;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-xl-12">
                <!-- Hiển thị lỗi từ Laravel Validator -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-2">
                                <iconify-icon icon="solar:danger-triangle-bold" style="font-size: 24px;"></iconify-icon>
                            </div>
                            <div>
                                <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Hiển thị thông báo từ session -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-2">
                                <iconify-icon icon="solar:check-circle-bold" style="font-size: 24px;"></iconify-icon>
                            </div>
                            <div>
                                <strong>Thành công!</strong> {{ session('success') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-2">
                                <iconify-icon icon="solar:danger-triangle-bold" style="font-size: 24px;"></iconify-icon>
                            </div>
                            <div>
                                <strong>Lỗi!</strong> {{ session('error') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-2">
                                <iconify-icon icon="solar:info-circle-bold" style="font-size: 24px;"></iconify-icon>
                            </div>
                            <div>
                                <strong>Cảnh báo!</strong> {{ session('warning') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Hiển thị lỗi database nếu có -->
                @if (session('db_error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-2">
                                <iconify-icon icon="solar:database-broken" style="font-size: 24px;"></iconify-icon>
                            </div>
                            <div>
                                <strong>Lỗi cơ sở dữ liệu!</strong> {{ session('db_error') }}
                                @if(session('db_error_details'))
                                    <div class="mt-2">
                                        <small class="text-muted">{{ session('db_error_details') }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h4 class="card-title mb-0">
                                <iconify-icon icon="solar:buildings-2-broken" class="me-2"></iconify-icon>
                                Danh Sách Khu Vực
                            </h4>
                            <p class="text-muted mb-0 small">
                                <iconify-icon icon="solar:clock-circle-broken" class="me-1"></iconify-icon>
                                Thời gian hiện tại: {{ $currentTime ?? now()->format('H:i') }}
                                <span class="ms-3">
                                    <iconify-icon icon="solar:home-broken" class="me-1"></iconify-icon>
                                    Tổng: {{ $areas->total() ?? $areas->count() }} khu vực
                                </span>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Button to update area statuses based on operating hours -->
                            @if (Route::has('areas.updateAreaStatuses'))
                                <a href="{{ route('areas.updateAreaStatuses') }}" class="btn btn-sm btn-info">
                                    <iconify-icon icon="solar:clock-circle-broken" class="me-1"></iconify-icon>
                                    Cập nhật trạng thái theo giờ
                                </a>
                            @endif
                            <a href="{{ route('areas.create') }}" class="btn btn-sm btn-primary">
                                <iconify-icon icon="solar:add-circle-broken" class="me-1"></iconify-icon>
                                Thêm Khu Vực
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover table-centered">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        @if (Route::has('areas.bulkDelete'))
                                            <th scope="col" width="40" class="px-3 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                                </div>
                                            </th>
                                        @endif
                                        <th scope="col" class="px-3">ID</th>
                                        <th scope="col">Mã / Tầng</th>
                                        <th scope="col">Tên Khu Vực</th>
                                        <th scope="col">Ảnh</th>
                                        <th scope="col">Sức chứa</th>
                                        <th scope="col">Số Bàn</th>
                                        <th scope="col">Trạng Thái</th>
                                        <th scope="col">Giờ hoạt động</th>
                                        <th scope="col">Phụ thu</th>
                                        <th scope="col">Tính năng</th>
                                        <th scope="col" class="text-end pe-3">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($areas as $area)
                                        @php
                                            // Lấy trạng thái hiện tại
                                            $currentStatus = $area->current_status ?? $area->status;

                                            // Kiểm tra xem khu vực có đang hoạt động theo giờ không
                                            $isInOperatingHours = true; // Mặc định là trong giờ hoạt động
                                            if ($area->hourSetting && $area->hourSetting->has_operating_hours) {
                                                try {
                                                    $isInOperatingHours = $area->isWithinOperatingHours();
                                                } catch (\Exception $e) {
                                                    // Nếu có lỗi, giả định là trong giờ hoạt động
                                                    $isInOperatingHours = true;
                                                }
                                            }

                                            // Định nghĩa lớp CSS và màu sắc cho trạng thái
                                            $statusClass =
                                                $currentStatus == 'Hoạt động'
                                                    ? 'bg-success-subtle text-success'
                                                    : ($currentStatus == 'Bảo trì'
                                                        ? 'bg-warning-subtle text-warning'
                                                        : 'bg-danger-subtle text-danger');
                                        @endphp
                                        <tr class="{{ $area->hourSetting && $area->hourSetting->has_operating_hours && !$isInOperatingHours ? 'table-light' : '' }}">
                                            @if (Route::has('areas.bulkDelete'))
                                                <td class="px-3 text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="area_ids[]"
                                                            value="{{ $area->area_id }}">
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="px-3">
                                                <span class="text-muted small">#{{ $area->area_id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary-subtle text-primary me-2">
                                                        {{ $area->code ?? 'N/A' }}
                                                    </span>
                                                    @if($area->floor)
                                                        <span class="badge bg-secondary-subtle text-secondary floor-badge">
                                                            T{{ $area->floor }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $area->name }}</strong>
                                                    @if($area->description)
                                                        <div class="small text-muted">{{ Str::limit($area->description, 50) }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                    @if ($area->image && Storage::disk('public')->exists($area->image))
                                                        <img src="{{ asset('storage/' . $area->image) }}"
                                                            alt="{{ $area->name }}" class="avatar-md rounded">
                                                    @else
                                                        <iconify-icon icon="solar:map-point-cafe-broken"
                                                            class="fs-24 text-muted"></iconify-icon>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($area->capacity)
                                                    <span class="capacity-info">
                                                        <iconify-icon icon="solar:users-group-rounded-broken" class="me-1 text-muted"></iconify-icon>
                                                        <strong>{{ $area->capacity }}</strong> người
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Chưa xác định</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (Route::has('areas.show'))
                                                    <a href="{{ route('areas.show', $area->area_id) }}"
                                                        class="btn btn-sm btn-soft-info">
                                                        <iconify-icon icon="solar:table-2-broken" class="me-1"></iconify-icon>
                                                        {{ $area->tables_count ?? 0 }} bàn
                                                    </a>
                                                @else
                                                    <span class="badge bg-info-subtle text-info">
                                                        <iconify-icon icon="solar:table-2-broken" class="me-1"></iconify-icon>
                                                        {{ $area->tables_count ?? 0 }} bàn
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusClass }} fs-12">
                                                    {{ $currentStatus }}
                                                </span>

                                                @if ($area->hourSetting && $area->hourSetting->has_operating_hours && !$isInOperatingHours)
                                                    <div class="small text-muted mt-1">
                                                        <iconify-icon icon="solar:clock-circle-broken"
                                                            class="fs-12 me-1"></iconify-icon>
                                                        Ngoài giờ hoạt động
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="hours-column" data-area-id="{{ $area->area_id }}">
                                                @if ($area->hourSetting && $area->hourSetting->has_operating_hours && $area->operatingHours->count() > 0)
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="status-indicator {{ $isInOperatingHours ? 'status-active' : 'status-inactive' }}"></span>
                                                        <span class="small">
                                                            {{ $area->operatingHours->count() }} khung giờ
                                                            <iconify-icon icon="solar:pen-2-linear"
                                                                class="fs-12 ms-1"></iconify-icon>
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">
                                                        Không có
                                                        <iconify-icon icon="solar:add-circle-linear"
                                                            class="fs-12 ms-1"></iconify-icon>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($area->surcharge && $area->surcharge > 0)
                                                    <span class="badge bg-warning-subtle text-warning surcharge-badge">
                                                        <iconify-icon icon="solar:dollar-minimalistic-broken" class="me-1"></iconify-icon>
                                                        {{ number_format($area->surcharge, 0, ',', '.') }}đ
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Miễn phí</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if ($area->is_vip)
                                                        <span class="badge bg-warning-subtle text-warning" title="Khu VIP">
                                                            <iconify-icon icon="solar:crown-broken" class="me-1"></iconify-icon>
                                                            VIP
                                                        </span>
                                                    @endif
                                                    @if ($area->is_smoking)
                                                        <span class="badge bg-secondary-subtle text-secondary" title="Cho phép hút thuốc">
                                                            <iconify-icon icon="solar:smoking-broken" class="me-1"></iconify-icon>
                                                            Hút thuốc
                                                        </span>
                                                    @endif
                                                    @if (!$area->is_vip && !$area->is_smoking)
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    @if (Route::has('areas.show'))
                                                        <a href="{{ route('areas.show', $area->area_id) }}"
                                                            class="btn btn-soft-info btn-sm" title="Xem chi tiết">
                                                            <iconify-icon icon="solar:eye-broken"
                                                                class="align-middle fs-16"></iconify-icon>
                                                        </a>
                                                    @endif

                                                    @if (Route::has('areas.manageHours'))
                                                        <a href="{{ route('areas.manageHours', $area->area_id) }}"
                                                            class="btn btn-soft-secondary btn-sm"
                                                            title="Quản lý giờ hoạt động">
                                                            <iconify-icon icon="solar:clock-circle-broken"
                                                                class="align-middle fs-16"></iconify-icon>
                                                        </a>
                                                    @endif

                                                    @if (Route::has('areas.manageLayout'))
                                                        <a href="{{ route('areas.manageLayout', $area->area_id) }}"
                                                            class="btn btn-soft-success btn-sm"
                                                            title="Quản lý bố trí bàn">
                                                            <iconify-icon icon="solar:table-broken"
                                                                class="align-middle fs-16"></iconify-icon>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('areas.edit', $area->area_id) }}"
                                                        class="btn btn-soft-primary btn-sm" title="Chỉnh sửa">
                                                        <iconify-icon icon="solar:pen-2-broken"
                                                            class="align-middle fs-16"></iconify-icon>
                                                    </a>

                                                    <form action="{{ route('areas.destroy', $area->area_id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa khu vực {{ $area->name }}? Thao tác này không thể hoàn tác nếu khu vực có bàn.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-soft-danger btn-sm"
                                                            title="Xóa">
                                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                class="align-middle fs-16"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Route::has('areas.bulkDelete') ? 12 : 11 }}"
                                                class="text-center py-5">
                                                <div class="text-center">
                                                    <iconify-icon icon="solar:buildings-2-broken" class="fs-48 text-muted mb-3"></iconify-icon>
                                                    <h5 class="text-muted">Chưa có khu vực nào</h5>
                                                    <p class="text-muted mb-3">Bắt đầu bằng cách tạo khu vực đầu tiên cho nhà hàng</p>
                                                    <a href="{{ route('areas.create') }}"
                                                        class="btn btn-primary">
                                                        <iconify-icon icon="solar:add-circle-broken"
                                                            class="me-1"></iconify-icon>
                                                        Thêm khu vực đầu tiên
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($areas->count() > 0)
                        <div class="card-footer d-flex justify-content-between align-items-center border-top py-3">
                            <div class="d-flex gap-2">
                                @if (Route::has('areas.bulkDelete'))
                                    <form action="{{ route('areas.bulkDelete') }}" method="POST" id="bulkDeleteForm"
                                        class="d-inline">
                                        @csrf
                                        <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-danger" disabled>
                                            <iconify-icon icon="solar:trash-bin-trash-broken"
                                                class="me-1"></iconify-icon>
                                            Xóa đã chọn
                                        </button>
                                    </form>
                                @endif

                                @if (Route::has('areas.deleteInactiveEmpty'))
                                    <a href="{{ route('areas.deleteInactiveEmpty') }}"
                                        class="btn btn-sm btn-warning"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả khu vực không hoạt động và trống?')">
                                        <iconify-icon icon="solar:trash-bin-minimalistic-broken"
                                            class="me-1"></iconify-icon>
                                        Xóa khu vực không hoạt động
                                    </a>
                                @endif
                            </div>
                            <div>
                                {{ $areas->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        @if (session('success'))
            <div class="toast show bg-success-subtle text-success" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header bg-success-subtle text-success">
                    <strong class="me-auto">
                        <iconify-icon icon="solar:check-circle-broken" class="me-1"></iconify-icon>
                        Thành công
                    </strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast show bg-danger-subtle text-danger" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header bg-danger-subtle text-danger">
                    <strong class="me-auto">
                        <iconify-icon icon="solar:danger-triangle-broken" class="me-1"></iconify-icon>
                        Lỗi
                    </strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast show bg-warning-subtle text-warning" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="toast-header bg-warning-subtle text-warning">
                    <strong class="me-auto">
                        <iconify-icon icon="solar:info-circle-broken" class="me-1"></iconify-icon>
                        Cảnh báo
                    </strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('warning') }}
                </div>
            </div>
        @endif
    </div>
@endsection