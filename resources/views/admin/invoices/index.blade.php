@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
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
                                <div class="avatar-md bg-primary bg-opacity-10 rounded">
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
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Danh sách hóa đơn</h4>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">Tạo hóa đơn mới</a>
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
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>{{ $invoice->table->table_number ?? 'Không xác định' }}</td>
                                    <td>{{ number_format($invoice->total_price, 0, ',', '.') }} VND</td>
                                    <td>
                                        <span
                                            class="badge @if ($invoice->status == 'Đang chuẩn bị') bg-warning @elseif($invoice->status == 'Đã phục vụ') bg-primary @elseif($invoice->status == 'Đã thanh toán') bg-success @elseif($invoice->status == 'Hủy đơn') bg-danger @endif">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('invoices.edit', $invoice->invoice_id) }}"
                                            class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="{{ route('invoices.print', $invoice->invoice_id) }}"
                                            class="btn btn-info btn-sm">In</a>
                                        <form action="{{ route('invoices.destroy', $invoice->invoice_id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer border-top d-flex justify-content-end">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection
