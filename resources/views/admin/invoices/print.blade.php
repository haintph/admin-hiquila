@extends('admin.layouts.master')

@section('styles')
    <style>
        /* Font chữ */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        /* Biến CSS */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --vip-color: #9b59b6;
            --border-radius: 8px;
        }

        /* Style chung cho hoá đơn */
        .invoice-container {
            font-family: 'Roboto', sans-serif;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin-bottom: 30px;
        }

        .invoice-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            position: relative;
            overflow: hidden;
        }

        .invoice-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: 1;
        }

        .invoice-title {
            position: relative;
            z-index: 2;
            margin: 0;
            font-weight: 700;
            text-align: center;
        }

        .invoice-body {
            padding: 25px;
        }

        .invoice-info {
            margin-bottom: 25px;
        }

        .invoice-info-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid var(--light-bg);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .invoice-info-item {
            margin-bottom: 8px;
            font-size: 15px;
            display: flex;
            align-items: center;
        }

        .invoice-info-label {
            font-weight: 600;
            color: var(--secondary-color);
            min-width: 120px;
        }

        .invoice-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-left: 5px;
        }

        .invoice-status.paid {
            background-color: var(--success-color);
            color: white;
        }

        .invoice-status.pending {
            background-color: var(--warning-color);
            color: white;
        }

        .table-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 5px;
        }

        .table-badge.vip {
            background-color: var(--vip-color);
            color: white;
        }

        .table-badge.smoking {
            background-color: #e67e22;
            color: white;
        }

        .table-badge.non-smoking {
            background-color: var(--success-color);
            color: white;
        }

        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
            margin-left: 5px;
        }

        .payment-method.cash {
            background-color: #2ecc71;
            color: white;
        }

        .payment-method.card {
            background-color: #3498db;
            color: white;
        }

        .payment-method.transfer {
            background-color: #9b59b6;
            color: white;
        }

        .customer-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        .info-card.restaurant {
            border-left-color: var(--secondary-color);
        }

        .info-card.table {
            border-left-color: var(--accent-color);
        }

        .info-card.customer {
            border-left-color: var(--success-color);
        }

        .party-size {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .invoice-table-container {
            margin-bottom: 30px;
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th {
            background-color: var(--secondary-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 500;
        }

        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .invoice-table tr:last-child td {
            border-bottom: none;
        }

        .invoice-table tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-container {
            background-color: var(--light-bg);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            text-align: right;
            margin-bottom: 25px;
        }

        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
        }

        .actions-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-invoice {
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-print {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-print:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: white;
            text-decoration: none;
        }

        /* Thêm trang trí và chi tiết */
        .invoice-watermark {
            position: absolute;
            bottom: 10px;
            right: 10px;
            opacity: 0.2;
            font-size: 12px;
            font-style: italic;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #777;
            border-top: 1px dashed #ddd;
            padding-top: 20px;
        }

        .invoice-qr {
            text-align: center;
            margin: 20px 0;
        }

        .invoice-qr img {
            max-width: 100px;
            height: auto;
        }

        .invoice-qr-caption {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        /* Style cho in ấn */
        @media print {
            body * {
                visibility: hidden;
            }

            .invoice-container,
            .invoice-container * {
                visibility: visible;
            }

            .invoice-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }

            .actions-container {
                display: none;
            }

            .container-xxl {
                width: 100%;
                max-width: 100%;
                padding: 0;
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .customer-info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .invoice-info-label {
                min-width: 100px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card invoice-container">
                    <div class="invoice-header">
                        <h2 class="invoice-title">
                            <i class="fas fa-receipt me-2"></i> HÓA ĐƠN THANH TOÁN
                        </h2>
                        <div class="invoice-watermark">
                            {{ config('app.name', 'Nhà Hàng') }}
                        </div>
                    </div>

                    <div class="invoice-body">
                        <!-- Thông tin chi tiết -->
                        <div class="customer-info-grid">
                            <!-- Thông tin đơn hàng -->
                            <div class="info-card">
                                <h4 class="invoice-info-title">
                                    <i class="fas fa-file-invoice"></i> Thông Tin Đơn Hàng
                                </h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Mã Hóa Đơn:</span>
                                    <strong>#{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Ngày giờ:</span>
                                    {{ $invoice->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Trạng thái:</span>
                                    <span
                                        class="invoice-status {{ $invoice->status == 'Đã thanh toán' ? 'paid' : 'pending' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Thanh toán:</span>
                                    <span
                                        class="payment-method {{ strtolower(str_replace(' ', '', $invoice->payment_method ?? 'cash')) }}">
                                        @if (($invoice->payment_method ?? 'Tiền mặt') == 'Tiền mặt')
                                            <i class="fas fa-money-bill-wave"></i> Tiền mặt
                                        @elseif($invoice->payment_method == 'Thẻ')
                                            <i class="fas fa-credit-card"></i> Thẻ
                                        @elseif($invoice->payment_method == 'Chuyển khoản')
                                            <i class="fas fa-university"></i> Chuyển khoản
                                        @else
                                            <i class="fas fa-money-bill-wave"></i>
                                            {{ $invoice->payment_method ?? 'Tiền mặt' }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Thông tin bàn -->
                            <div class="info-card table">
                                <h4 class="invoice-info-title">
                                    <i class="fas fa-chair"></i> Thông Tin Bàn
                                </h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Số bàn:</span>
                                    <strong>{{ $invoice->table->table_number }}</strong>
                                    @if ($invoice->table->table_type == 'Bàn VIP')
                                        <span class="table-badge vip">VIP</span>
                                    @endif
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Loại bàn:</span>
                                    {{ $invoice->table->table_type }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Khu vực:</span>
                                    {{ $invoice->table->area->name ?? 'Chưa phân khu' }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Tầng:</span>
                                    <strong>Tầng {{ $invoice->table->area->floor ?? '1' }}</strong>
                                    @if ($invoice->table->area->is_vip ?? false)
                                        <span class="table-badge vip">VIP</span>
                                    @endif
                                    @if ($invoice->table->area->is_smoking ?? false)
                                        <span class="table-badge smoking">Hút thuốc</span>
                                    @else
                                        <span class="table-badge non-smoking">Không hút thuốc</span>
                                    @endif
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Sức chứa:</span>
                                    {{ $invoice->table->capacity }} người
                                </div>
                            </div>

                            <!-- Thông tin khách hàng - SỬA: Lấy từ $invoice thay vì $invoice->table -->
                            <div class="info-card customer">
                                <h4 class="invoice-info-title">
                                    <i class="fas fa-users"></i> Thông Tin Khách Hàng
                                </h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Tên khách:</span>
                                    <strong>{{ $invoice->customer_name ?? 'Khách lẻ' }}</strong>
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Số điện thoại:</span>
                                    {{ $invoice->customer_phone ?? 'Không có' }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Số người:</span>
                                    <span class="party-size">
                                        <i class="fas fa-user-friends"></i>
                                        {{ $invoice->party_size ?? $invoice->table->capacity }} người
                                    </span>
                                </div>
                                @if ($invoice->special_notes)
                                    <div class="invoice-info-item">
                                        <span class="invoice-info-label">Ghi chú:</span>
                                        {{ $invoice->special_notes }}
                                    </div>
                                @endif
                            </div>

                            <!-- Thông tin nhà hàng -->
                            <div class="info-card restaurant">
                                <h4 class="invoice-info-title">
                                    <i class="fas fa-store"></i> Thông Tin Nhà Hàng
                                </h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Tên:</span>
                                    {{ config('app.name', 'Nhà Hàng XYZ') }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Địa chỉ:</span>
                                    123 Đường ABC, Quận XYZ, Hà Nội
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Điện thoại:</span>
                                    (024) 1234 5678
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Email:</span>
                                    info@nhahangxyz.com
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách món ăn -->
                        <div class="invoice-table-container">
                            <table class="invoice-table">
                                <thead>
                                    <tr>
                                        <th width="35%">Món Ăn</th>
                                        <th width="15%">Phân loại</th>
                                        <th class="text-center" width="10%">SL</th>
                                        <th class="text-right" width="15%">Đơn Giá</th>
                                        <th class="text-right" width="20%">Thành Tiền</th>
                                        <th class="text-center" width="5%">TT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->dish->name }}
                                                @if ($item->variant)
                                                    <br><small
                                                        class="text-muted">{{ $item->variant->variant_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ $item->dish->category->name ?? 'Khác' }}</small>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($item->quantity * $item->price, 0, ',', '.') }}đ</strong>
                                            </td>
                                            <td class="text-center">
                                                @if ($item->chef_confirmed_at)
                                                    <i class="fas fa-check-circle text-success" title="Đã phục vụ"></i>
                                                @elseif($item->sent_to_kitchen_at)
                                                    <i class="fas fa-clock text-warning" title="Đang chế biến"></i>
                                                @else
                                                    <i class="fas fa-hourglass-start text-secondary"
                                                        title="Chờ xử lý"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Chi tiết thanh toán -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="invoice-qr">
                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAAB0CAYAAABUmhYnAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAECUlEQVR4nO3dQW4UMRAE0JmVuEE4AOIkcIJ8HolbcCYOwBKCtRcji253lct+JaH51yPNTLftst0/Pn96nMDTyAOsxCgUZBQKMgoFGYWCjEJBRqEgo1CQUSjIKBRkFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgoFGYWCjEJBRqEgo1CQUejJef368eufr1+evvv5Hx8fj/0U5KKFvnz+8PS/339++e3L1++e+vTT6/XPuV6vp7//7BT9yIU2/38r6szxW+GPWm41/Jkx7/F74Y9WaBv+Dnf0+GfDH6XQc+HPjL96/Fbsu3CU0Jnw28EPqVAtfAFQ6Grs7yZ0NfzV4wuRQkfg74avFvqZz3nkFKoavkL4KqGr4SuGrxC6Gr5y+NtCV8NXD39L6Gr4s+HLoZxQtfB3w1cKVQp/Rqha+OcLVQ1/R/ga4e8JvSpUNfyd8LeFXhWqHP6O8K/mTKGz4c+GrxL+lZwtVC38O8LfEXpF6Gr4s+HPhr8j/KXw74TP0H6fMd8ZyS+XbfhXn0u78BXCZ2nt3Am/XOhs+Dfiq9/CfvPfCZ/h/Mwn//zLhc6Gr/YW9tlnz+GrhX8p9Er4qm9hZ8JXf/YcvmL4l0KvhK/+FnblOKXw5XfoHM4/f3YM9bew8r+Hzt+L+t+h8zO0w1d+C9sNf+YYpfCXhLbDV3wL2w1/9ZjPzKTQGeE74d+Irxy+XOj8vI88e3bMI89X15dLFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgoFGYWCjEJBRqEgo1CQUSjIKBRkFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgqFef/x+fPTyGNYaY0/x3GzpSGkT0ahIKNQkFEoyCgUZBQKMgoFGYWCjEJB5vFDLnLO9zMOh9AKKKVBQmgVlNLBFDoDoTQ4CO20wq9AKA0MoTu04a9AKA0IoRXsQCgNBqFVqEMoFarc5VZAlaMKoYPkLjaXdvZCB8llbC6uB0AHym1sLubsEDpQrmJzGQegA+YinMunB6YBmsvtHYAOmjEQmkuZHZgOmAvttS8Ew0UAvJCZC5y9AJB3AC4GbpcDsUuDuCGDvEsY2T8v834g0X8ZALsclD/dIX8yAe8AVJ6/uxygXRrEDZnVXQbMDRmUHeIBJX+xz9kJKIUmfxVDaAcwt3ZIfrfDH3sHF5m85yXvAAAAAElFTkSuQmCC"
                                        alt="QR Code">
                                    <div class="invoice-qr-caption">Quét mã để kiểm tra hóa đơn</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="total-container">
                                    <div class="row mb-2">
                                        <div class="col-7 total-label">Tạm tính:</div>
                                        <div class="col-5 text-end">
                                            {{ number_format($invoice->total_price, 0, ',', '.') }}đ</div>
                                    </div>

                                    @if ($invoice->discount_amount ?? 0 > 0)
                                        <div class="row mb-2">
                                            <div class="col-7 total-label">Giảm giá:</div>
                                            <div class="col-5 text-end text-danger">
                                                -{{ number_format($invoice->discount_amount, 0, ',', '.') }}đ</div>
                                        </div>
                                    @endif

                                    <div class="row mb-2">
                                        <div class="col-7 total-label">VAT (8%):</div>
                                        <div class="col-5 text-end">
                                            {{ number_format(($invoice->total_price - ($invoice->discount_amount ?? 0)) * 0.08, 0, ',', '.') }}đ
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-7 total-label">Tổng tiền:</div>
                                        <div class="col-5 text-end">
                                            <span
                                                class="total-amount">{{ number_format(($invoice->total_price - ($invoice->discount_amount ?? 0)) * 1.08, 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>

                                    @if ($invoice->table->min_spend && $invoice->table->table_type == 'Bàn VIP')
                                        <div class="row mt-2">
                                            <div class="col-12 text-end">
                                                <small class="text-muted">
                                                    (Chi tiêu tối thiểu VIP:
                                                    {{ number_format($invoice->table->min_spend, 0, ',', '.') }}đ)
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="actions-container">
                            <button onclick="printInvoice()" class="btn-invoice btn-print">
                                <i class="fas fa-print"></i> In Hóa Đơn
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn-invoice btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay Lại
                            </a>
                        </div>

                        <!-- Footer -->
                        <div class="invoice-footer">
                            <p><strong>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</strong></p>
                            <p>Mọi thắc mắc xin liên hệ: (024) 1234 5678 | Email: info@nhahangxyz.com</p>
                            @if ($invoice->table->reserved_time)
                                <p><small>Thời gian đặt bàn:
                                        {{ \Carbon\Carbon::parse($invoice->table->reserved_time)->format('d/m/Y H:i') }}</small>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script xử lý in -->
    <script>
        function printInvoice() {
            window.print();
        }
    </script>
@endsection
