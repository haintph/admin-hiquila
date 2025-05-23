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
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
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
        margin-bottom: 30px;
    }
    
    .invoice-info-title {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid var(--light-bg);
        padding-bottom: 10px;
    }
    
    .invoice-info-item {
        margin-bottom: 8px;
        font-size: 15px;
    }
    
    .invoice-info-label {
        font-weight: 600;
        color: var(--secondary-color);
    }
    
    .invoice-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .invoice-status.paid {
        background-color: #27ae60;
        color: white;
    }
    
    .invoice-status.pending {
        background-color: #f39c12;
        color: white;
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
        font-size: 18px;
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
        
        .invoice-container, .invoice-container * {
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
</style>
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="invoice-info">
                                <h4 class="invoice-info-title">Thông Tin Đơn Hàng</h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Mã Hóa Đơn:</span> 
                                    <strong>#{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Bàn:</span> 
                                    <strong>{{ $invoice->table->table_number }}</strong>
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Ngày:</span> 
                                    {{ $invoice->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Trạng thái:</span> 
                                    <span class="invoice-status {{ $invoice->status == 'Đã thanh toán' ? 'paid' : 'pending' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="invoice-info">
                                <h4 class="invoice-info-title">Thông Tin Nhà Hàng</h4>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Tên:</span> 
                                    {{ config('app.name', 'Nhà Hàng XYZ') }}
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Địa Chỉ:</span> 
                                    123 Đường ABC, Quận XYZ, Hà Nội
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Điện Thoại:</span> 
                                    (024) 1234 5678
                                </div>
                                <div class="invoice-info-item">
                                    <span class="invoice-info-label">Email:</span> 
                                    info@nhahangxyz.com
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Danh sách món ăn -->
                    <div class="invoice-table-container">
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th width="40%">Món Ăn</th>
                                    <th class="text-center" width="15%">Số Lượng</th>
                                    <th class="text-right" width="20%">Đơn Giá</th>
                                    <th class="text-right" width="25%">Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->dish->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                        <td class="text-right">{{ number_format($item->quantity * $item->price, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Chi tiết thanh toán -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="invoice-qr">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHQAAAB0CAYAAABUmhYnAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAECUlEQVR4nO3dQW4UMRAE0JmVuEE4AOIkcIJ8HolbcCYOwBKCtRcji253lct+JaH51yPNTLftst0/Pn96nMDTyAOsxCgUZBQKMgoFGYWCjEJBRqEgo1CQUSjIKBRkFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgoFGYWCjEJBRqEgo1CQUejJef368eufr1+evvv5Hx8fj/0U5KKFvnz+8PS/339++e3L1++e+vTT6/XPuV6vp7//7BT9yIU2/38r6szxW+GPWm41/Jkx7/F74Y9WaBv+Dnf0+GfDH6XQc+HPjL96/Fbsu3CU0Jnw28EPqVAtfAFQ6Grs7yZ0NfzV4wuRQkfg74avFvqZz3nkFKoavkL4KqGr4SuGrxC6Gr5y+NtCV8NXD39L6Gr4s+HLoZxQtfB3w1cKVQp/Rqha+OcLVQ1/R/ga4e8JvSpUNfyd8LeFXhWqHP6O8K/mTKGz4c+GrxL+lZwtVC38O8LfEXpF6Gr4s+HPhr8j/KXw74TP0H6fMd8ZyS+XbfhXn0u78BXCZ2nt3Am/XOhs+Dfiq9/CfvPfCZ/h/Mwn//zLhc6Gr/YW9tlnz+GrhX8p9Er4qm9hZ8JXf/YcvmL4l0KvhK/+FnblOKXw5XfoHM4/f3YM9bew8r+Hzt+L+t+h8zO0w1d+C9sNf+YYpfCXhLbDV3wL2w1/9ZjPzKTQGeE74d+Irxy+XOj8vI88e3bMI89X15dLFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgoFGYWCjEJBRqEgo1CQUSjIKBRkFAoyCgUZhYKMQkFGoSCjUJBRKMgoFGQUCjIKBRmFgoxCQUahIKNQkFEoyCgUZBQKMgqFef/x+fPTyGNYaY0/x3GzpSGkT0ahIKNQkFEoyCgUZBQKMgoFGYWCjEJB5vFDLnLO9zMOh9AKKKVBQmgVlNLBFDoDoTQ4CO20wq9AKA0MoTu04a9AKA0KoTu14a9AKA0IoRXsQCgNBqFVqEMoFarc5VZAlaMKoYPkLjaXdvZCB8llbC6uB0AHym1sLubsEDpQrmJzGQegA+YinMunB6YBmsvtHYAOmjEQmkuZHZgOmAvttS8Ew0UAvJCZC5y9AJB3AC4GbpcDsUuDuCGDvEsY2T8v834g0X8ZALsclD/dIX8yAe8AVJ6/uxygXRrEDZnVXQbMDRmUHeIBJX+xz9kJKIUmfxVDaAcwt3ZIfrfDH3sHF5m85yXvAAAAAElFTkSuQmCC" alt="QR Code">
                                <div class="invoice-qr-caption">Quét mã để kiểm tra hóa đơn</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="total-container">
                                <div class="row mb-2">
                                    <div class="col-7 total-label">Tạm tính:</div>
                                    <div class="col-5 text-end">{{ number_format($invoice->total_price, 0, ',', '.') }}đ</div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-7 total-label">Giảm giá:</div>
                                    <div class="col-5 text-end">{{ number_format(0, 0, ',', '.') }}đ</div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-7 total-label">VAT (8%):</div>
                                    <div class="col-5 text-end">{{ number_format($invoice->total_price * 0.08, 0, ',', '.') }}đ</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-7 total-label">Tổng tiền:</div>
                                    <div class="col-5 text-end">
                                        <span class="total-amount">{{ number_format($invoice->total_price * 1.08, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Nút in hóa đơn -->
                    <div class="actions-container">
                        <button onclick="printInvoice()" class="btn-invoice btn-print">
                            <i class="fas fa-print"></i> In Hóa Đơn
                        </button>
                        <a href="{{ route('staff.index') }}" class="btn-invoice btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                    </div>
                    
                    <!-- Footer -->
                    <div class="invoice-footer">
                        <p>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
                        <p>Mọi thắc mắc xin liên hệ: (024) 1234 5678</p>
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