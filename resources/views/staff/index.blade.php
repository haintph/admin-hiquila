@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <!-- Thống kê tổng quan -->
        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-2 d-flex align-items-center gap-2">Tổng hóa đơn</h4>
                                <p class="text-muted fw-medium fs-22 mb-0">{{ $totalInvoices }}</p>
                            </div>
                            <div>
                                <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                    <iconify-icon icon="solar:bill-list-bold-duotone"
                                        class="fs-32 text-primary avatar-title"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-2">Chờ thanh toán</h4>
                                <p class="text-muted fw-medium fs-22 mb-0">{{ $pendingInvoices }}</p>
                            </div>
                            <div>
                                <div class="avatar-md bg-warning bg-opacity-10 rounded">
                                    <iconify-icon icon="solar:bill-cross-bold-duotone"
                                        class="fs-32 text-warning avatar-title"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-2">Đã thanh toán</h4>
                                <p class="text-muted fw-medium fs-22 mb-0">{{ $paidInvoices }}</p>
                            </div>
                            <div>
                                <div class="avatar-md bg-success bg-opacity-10 rounded">
                                    <iconify-icon icon="solar:bill-check-bold-duotone"
                                        class="fs-32 text-success avatar-title"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-2">Hoàn thành</h4>
                                <p class="text-muted fw-medium fs-22 mb-0">{{ $completedInvoices ?? 0 }}</p>
                            </div>
                            <div>
                                <div class="avatar-md bg-info bg-opacity-10 rounded">
                                    <iconify-icon icon="solar:check-circle-bold-duotone"
                                        class="fs-32 text-info avatar-title"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách hóa đơn -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách hóa đơn</h4>
                <a href="{{ route('staff.invoices.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>Đặt bàn
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>ID</th>
                                <th>Bàn</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Phương thức TT</th>
                                <th>Thời gian</th>
                                <th style="width: 200px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>
                                        <span class="badge bg-info">Bàn
                                            {{ $invoice->table->table_number ?? 'Không xác định' }}</span>
                                    </td>
                                    <td>{{ number_format($invoice->total_price, 0, ',', '.') }} VND</td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($invoice->status == 'Đang chuẩn bị') bg-warning 
                                            @elseif($invoice->status == 'Đã phục vụ') bg-primary 
                                            @elseif($invoice->status == 'Đã thanh toán') bg-success 
                                            @elseif($invoice->status == 'Hoàn thành') bg-info
                                            @elseif($invoice->status == 'Hủy đơn') bg-danger 
                                            @else bg-secondary @endif">
                                            @if ($invoice->status == 'Hoàn thành')
                                                <iconify-icon icon="solar:check-circle-bold-duotone"
                                                    class="me-1"></iconify-icon>
                                            @elseif($invoice->status == 'Đã thanh toán')
                                                <iconify-icon icon="solar:card-bold-duotone" class="me-1"></iconify-icon>
                                            @elseif($invoice->status == 'Đã phục vụ')
                                                <iconify-icon icon="solar:chef-hat-bold-duotone"
                                                    class="me-1"></iconify-icon>
                                            @elseif($invoice->status == 'Đang chuẩn bị')
                                                <iconify-icon icon="solar:clock-bold-duotone" class="me-1"></iconify-icon>
                                            @elseif($invoice->status == 'Hủy đơn')
                                                <iconify-icon icon="solar:close-circle-bold-duotone"
                                                    class="me-1"></iconify-icon>
                                            @endif
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($invoice->payment_method)
                                            @if ($invoice->payment_method == 'cash')
                                                <span class="badge bg-success">
                                                    <iconify-icon icon="solar:wallet-money-bold-duotone"
                                                        class="me-1"></iconify-icon>
                                                    Tiền mặt
                                                </span>
                                            @elseif($invoice->payment_method == 'transfer')
                                                <span class="badge bg-info">
                                                    <iconify-icon icon="solar:card-transfer-bold-duotone"
                                                        class="me-1"></iconify-icon>
                                                    Chuyển khoản
                                                </span>
                                            @elseif($invoice->payment_method == 'qr')
                                                <span class="badge bg-warning">
                                                    <iconify-icon icon="solar:qr-code-bold-duotone"
                                                        class="me-1"></iconify-icon>
                                                    QR Code
                                                </span>
                                            @elseif($invoice->payment_method == 'vnpay')
                                                <span class="badge bg-primary">
                                                    <iconify-icon icon="solar:card-bold-duotone"
                                                        class="me-1"></iconify-icon>
                                                    VNPAY
                                                </span>
                                            @elseif($invoice->payment_method == 'paypal')
                                                <span class="badge bg-dark">
                                                    <iconify-icon icon="solar:card-bold-duotone"
                                                        class="me-1"></iconify-icon>
                                                    PayPal
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ $invoice->payment_method }}</span>
                                            @endif
                                        @else
                                            @if ($invoice->status == 'Đã thanh toán')
                                                <span class="text-muted small">Chưa xác định</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Tạo:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if ($invoice->paid_at)
                                            <small class="text-success">
                                                <strong>TT:</strong>
                                                {{ \Carbon\Carbon::parse($invoice->paid_at)->format('H:i d/m/Y') }}
                                            </small>
                                        @endif
                                    </td>
                                    <!-- Thay thế phần hành động trong view invoices/index.blade.php -->

                                    <td>
                                        <div class="d-flex gap-1">
                                            @if ($invoice->status != 'Đã thanh toán' && $invoice->status != 'Hoàn thành')
                                                <!-- Hóa đơn chưa thanh toán -->
                                                <a href="{{ route('staff.invoices.edit', $invoice->invoice_id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Order
                                                </a>

                                                @if ($invoice->status == 'Đã phục vụ' && $invoice->total_price > 0)
                                                    @if (auth()->user()->role === 'owner')
                                                        <a href="{{ route('staff.invoices.payment', $invoice->invoice_id) }}"
                                                            class="btn btn-success btn-sm">
                                                            <iconify-icon icon="solar:card-bold-duotone"></iconify-icon>
                                                            Thanh toán
                                                        </a>
                                                    @elseif (auth()->user()->role === 'staff')
                                                        <a href="{{ route('staff.invoices.payment', $invoice->invoice_id) }}"
                                                            class="btn btn-success btn-sm">
                                                            <iconify-icon icon="solar:card-bold-duotone"></iconify-icon>
                                                            Thanh toán
                                                        </a>
                                                    @endif
                                                @endif

                                                @if ($invoice->total_price == 0)
                                                    <form action="{{ route('staff.invoices.destroy', $invoice->invoice_id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')">
                                                            <iconify-icon
                                                                icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                                                            Xóa
                                                        </button>
                                                    </form>
                                                @endif
                                            @elseif($invoice->status == 'Đã thanh toán')
                                                <!-- Hóa đơn đã thanh toán - có thể in và hoàn tất -->
                                                <a href="{{ route('staff.invoices.print', $invoice->invoice_id) }}"
                                                    class="btn btn-primary btn-sm" target="_blank">
                                                    <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Chi tiết
                                                </a>

                                                <form action="{{ route('staff.invoices.finish', $invoice->invoice_id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Xác nhận hoàn tất và dọn bàn?')">
                                                        <iconify-icon
                                                            icon="solar:check-circle-bold-duotone"></iconify-icon>
                                                        Hoàn tất
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Hóa đơn đã hoàn thành -->
                                                <a href="{{ route('staff.invoices.print', $invoice->invoice_id) }}"
                                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> Chi tiết
                                                </a>
                                                <span class="badge bg-success">
                                                    <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon> Đã
                                                    hoàn thành
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Không có hóa đơn nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-top d-flex justify-content-between align-items-center">
                <div>Hiển thị {{ $invoices->firstItem() ?? 0 }} đến {{ $invoices->lastItem() ?? 0 }} của
                    {{ $invoices->total() ?? 0 }} hóa đơn</div>
                {{ $invoices->links() }}
            </div>
        </div>

        <!-- Thống kê nhanh theo phương thức thanh toán -->
        @if ($invoices->where('status', 'Đã thanh toán')->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <iconify-icon icon="solar:chart-bold-duotone" class="me-2"></iconify-icon>
                                Thống kê phương thức thanh toán (hôm nay)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $todayPaid = $invoices
                                        ->where('status', 'Đã thanh toán')
                                        ->where('created_at', '>=', now()->startOfDay());
                                    $paymentMethods = $todayPaid->groupBy('payment_method');
                                @endphp

                                @foreach ($paymentMethods as $method => $invoicesGroup)
                                    @php
                                        $methodInfo = [
                                            'cash' => [
                                                'name' => 'Tiền mặt',
                                                'color' => 'success',
                                                'icon' => 'solar:wallet-money-bold-duotone',
                                            ],
                                            'transfer' => [
                                                'name' => 'Chuyển khoản',
                                                'color' => 'info',
                                                'icon' => 'solar:card-transfer-bold-duotone',
                                            ],
                                            'qr' => [
                                                'name' => 'QR Code',
                                                'color' => 'warning',
                                                'icon' => 'solar:qr-code-bold-duotone',
                                            ],
                                            'vnpay' => [
                                                'name' => 'VNPAY',
                                                'color' => 'primary',
                                                'icon' => 'solar:card-bold-duotone',
                                            ],
                                            'paypal' => [
                                                'name' => 'PayPal',
                                                'color' => 'dark',
                                                'icon' => 'solar:card-bold-duotone',
                                            ],
                                        ];
                                        $info = $methodInfo[$method] ?? [
                                            'name' => $method,
                                            'color' => 'secondary',
                                            'icon' => 'solar:question-circle-bold-duotone',
                                        ];
                                        $total = $invoicesGroup->sum('total_price');
                                        $count = $invoicesGroup->count();
                                    @endphp

                                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                                        <div class="card bg-{{ $info['color'] }} bg-opacity-10 border-0">
                                            <div class="card-body text-center py-3">
                                                <iconify-icon icon="{{ $info['icon'] }}"
                                                    class="fs-1 text-{{ $info['color'] }} mb-2"></iconify-icon>
                                                <h6 class="mb-1">{{ $info['name'] }}</h6>
                                                <p class="mb-0 small">{{ $count }} đơn</p>
                                                <p class="mb-0 fw-bold text-{{ $info['color'] }}">
                                                    {{ number_format($total, 0, ',', '.') }}đ</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Script xử lý QR thanh toán -->
    @if (config('app.qr_payment_enabled', true))
        @push('scripts')
            <script>
                // Chức năng kiểm tra thanh toán QR (giữ nguyên nếu đã có)
                function checkPaymentStatus(invoiceId) {
                    $.ajax({
                        url: `{{ url('admin/invoices') }}/${invoiceId}/check-payment`,
                        type: 'GET',
                        success: function(response) {
                            if (response.paid) {
                                alert('Thanh toán thành công!');
                                window.location.href = `{{ url('admin/invoices') }}`;
                            } else {
                                setTimeout(function() {
                                    checkPaymentStatus(invoiceId);
                                }, 5000); // Kiểm tra lại sau 5 giây
                            }
                        }
                    });
                }
            </script>
        @endpush
    @endif
@endsection
