@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <!-- Thống kê tổng quan -->
        <div class="row mb-3">

            <div class="col-lg-4">
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
            <div class="col-lg-4">
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
            <div class="col-lg-4">
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
        </div>

        <!-- Danh sách hóa đơn -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách hóa đơn</h4>
                <a href="{{ route('staff.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>Tạo hóa đơn mới
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
                                <th>Thời gian tạo</th>
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
                                            @elseif($invoice->status == 'Hủy đơn') bg-danger @endif">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if ($invoice->status != 'Đã thanh toán')
                                                <a href="{{ route('staff.edit', $invoice->invoice_id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Order
                                                </a>

                                                @if ($invoice->status == 'Đã phục vụ' && $invoice->total_price > 0)
                                                    <a href="{{ route('staff.payment', $invoice->invoice_id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <iconify-icon icon="solar:card-bold-duotone"></iconify-icon> Thanh
                                                        toán
                                                    </a>
                                                @endif

                                                @if ($invoice->total_price == 0)
                                                    <form action="{{ route('staff.destroy', $invoice->invoice_id) }}"
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
                                            @else
                                                <a href="{{ route('staff.print', $invoice->invoice_id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <iconify-icon icon="solar:printer-bold-duotone"></iconify-icon> In
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Không có hóa đơn nào</td>
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
