@extends('admin.layouts.master')

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body" id="invoice">
                    <!-- Tiêu đề hóa đơn -->
                    <div class="clearfix pb-3 bg-info-subtle p-lg-3 p-2 m-n2 rounded position-relative text-center">
                        <h2 class="text-primary"><i class="fas fa-receipt"></i> HÓA ĐƠN THANH TOÁN</h2>
                    </div>
                    
                    <div class="clearfix pb-3 mt-4">
                        <div class="float-sm-start">
                            <h4 class="card-title">Thông Tin Đơn Hàng</h4>
                            <p><strong>Bàn: </strong> {{ $invoice->table->table_number }}</p>
                            <p><strong>Ngày: </strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Trạng thái: </strong> <span class="badge bg-success">{{ $invoice->status }}</span></p>
                        </div>
                    </div>
                    
                    <!-- Danh sách món ăn -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Món Ăn</th>
                                            <th>Số Lượng</th>
                                            <th>Giá</th>
                                            <th>Thành Tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->items as $item)
                                            <tr>
                                                <td>{{ $item->dish->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tổng tiền -->
                    <h4 class="mt-4 text-danger text-center">
                        <strong>Tổng Tiền: </strong> {{ number_format($invoice->total_price, 0, ',', '.') }} đ
                    </h4>
                    
                    <!-- Nút in hóa đơn -->
                    <div class="text-center mt-3">
                        <button onclick="printInvoice()" class="btn btn-primary">
                            <i class="fas fa-print"></i> In Hóa Đơn
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh khi in -->
<style>
    @media print {
        body * { visibility: hidden; }
        #invoice, #invoice * { visibility: visible; }
        #invoice { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

<!-- Script xử lý in -->
<script>
    function printInvoice() {
        window.print();
    }
</script>
@endsection