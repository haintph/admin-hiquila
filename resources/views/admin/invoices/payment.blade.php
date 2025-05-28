@extends('admin.layouts.master')

@section('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .payment-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .payment-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 25px;
        }

        .payment-info {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: -30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .order-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .qr-container {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            height: 100%;
        }

        .qr-code {
            max-width: 90%;
            height: auto;
            border: 8px solid white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            display: block;
        }

        .payment-method {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border: 2px solid transparent;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .payment-method:hover,
        .payment-method.active {
            border-color: #38ef7d;
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .payment-logo-container {
            width: 100px;
            height: 40px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-shrink: 0;
        }

        .payment-method img {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        /* Add highlight effect on hover */
        .payment-method:hover img {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .timer-container {
            border-radius: 10px;
            padding: 15px;
            background-color: #f8f9fa;
            text-align: center;
            margin-top: 20px;
        }

        .timer {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .step-number {
            min-width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #38ef7d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .step-text {
            flex: 1;
            text-align: left;
            font-size: 0.9rem;
        }

        .bank-info {
            border-radius: 10px;
            background-color: #f8f9fa;
            padding: 15px;
            margin-top: 20px;
        }

        .bank-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #dee2e6;
        }

        .bank-info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .copy-btn {
            background-color: #e9ecef;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background-color: #dee2e6;
        }

        .payment-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .payment-actions .btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        .collapse-header {
            cursor: pointer;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .collapse-header:hover {
            background-color: #e9ecef;
        }

        .order-item {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .payment-verified {
            background-color: #d4edda;
            color: #155724;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-verified i {
            font-size: 2rem;
            margin-right: 15px;
        }

        /* Responsive improvements */
        @media (max-width: 767.98px) {
            .qr-container {
                margin-top: 20px;
            }

            .payment-header {
                padding: 15px;
            }

            .payment-header h4 {
                font-size: 1.25rem;
            }

            .step-text {
                font-size: 0.85rem;
            }

            .qr-code {
                max-width: 80%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Nút quay lại -->
                <div class="mb-4">
                    <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại đặt món
                    </a>
                </div>

                <div class="card payment-card">
                    <!-- Header -->
                    <div class="payment-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Thanh toán hóa đơn</h4>
                                <p class="mb-0 opacity-75">Mã hóa đơn: #{{ $invoice->invoice_id }}</p>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0">{{ number_format($invoice->total_price, 0, ',', '.') }} VND</h4>
                                <p class="mb-0 opacity-75">
                                    {{ \Carbon\Carbon::parse($invoice->created_at)->format('H:i - d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pb-4">
                        <div class="row">
                            <!-- Thông tin thanh toán -->
                            <div class="col-md-6">
                                <div class="payment-info mb-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin bàn
                                        </h5>
                                        <span
                                            class="badge bg-{{ $invoice->status == 'Đã thanh toán' ? 'success' : 'warning' }}">
                                            {{ $invoice->status }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <p class="mb-2"><strong>Số bàn:</strong> <span
                                                class="badge bg-primary">{{ $invoice->table->table_number }}</span></p>
                                        <p class="mb-2"><strong>Khu vực:</strong>
                                            {{ $invoice->table->area->name ?? 'Chưa phân khu' }}</p>
                                        <p class="mb-0"><strong>Thời gian vào:</strong>
                                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('H:i - d/m/Y') }}</p>
                                    </div>

                                    <!-- Tóm tắt đơn hàng -->
                                    <div class="collapse-header" data-bs-toggle="collapse" data-bs-target="#orderSummary">
                                        <i class="fas fa-receipt me-2"></i>Chi tiết đơn hàng <i
                                            class="fas fa-chevron-down float-end"></i>
                                    </div>

                                    <div class="collapse show" id="orderSummary">
                                        <div class="order-summary mt-3">
                                            @foreach ($invoice->items as $item)
                                                <div class="order-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <p class="mb-0 fw-medium">{{ $item->dish->name }} <span
                                                                    class="badge bg-light text-dark">x{{ $item->quantity }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0">
                                                                {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                VND</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                                <h5>Tổng cộng:</h5>
                                                <h5 class="text-danger">
                                                    {{ number_format($invoice->total_price, 0, ',', '.') }} VND</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phương thức thanh toán -->
                                <div class="payment-methods mb-4">
                                    <h5 class="mb-3"><i class="fas fa-wallet me-2 text-primary"></i>Chọn phương thức thanh
                                        toán</h5>

                                    <!-- VNPAY QR - Giữ nguyên -->
                                    <a href="{{ route('vnpay.payment', $invoice->invoice_id) }}"
                                        class="text-decoration-none">
                                        <div class="payment-method active" data-method="vnpay">
                                            <div class="d-flex align-items-center">
                                                <img width="100"
                                                    src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-VNPAY-QR.png"
                                                    alt="VNPAY">
                                                <div>
                                                    <h6 class="mb-0">VNPAY QR</h6>
                                                    <small class="text-muted">Thanh toán bằng mã QR qua VNPAY</small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- PayPal - Giữ nguyên -->
                                    <a href="{{ route('paypal.payment', $invoice->invoice_id) }}"
                                        class="text-decoration-none">
                                        <div class="payment-method" data-method="paypal">
                                            <div class="d-flex align-items-center">
                                                <img width="100"
                                                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/1200px-PayPal.svg.png"
                                                    alt="PayPal">
                                                <div>
                                                    <h6 class="mb-0">PayPal</h6>
                                                    <small class="text-muted">Thanh toán qua PayPal</small>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- Tiền mặt - CHỈ ĐỔI THÀNH FORM POST -->
                                    <form action="{{ route('invoices.checkout', $invoice->invoice_id) }}" method="POST"
                                        style="display: inline-block; width: 100%;">
                                        @csrf
                                        <button type="submit"
                                            style="background: none; border: none; padding: 0; width: 100%; text-align: left; cursor: pointer;">
                                            <div class="payment-method" data-method="cash">
                                                <div class="d-flex align-items-center">
                                                    <img width="100"
                                                        src="https://cdn-icons-png.flaticon.com/512/2331/2331941.png"
                                                        alt="Cash">
                                                    <div>
                                                        <h6 class="mb-0">Tiền mặt</h6>
                                                        <small class="text-muted">Thanh toán trực tiếp bằng tiền mặt</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                </div>

                                <!-- In hóa đơn -->
                                <div class="d-grid">
                                    <a href="{{ route('invoices.print', $invoice->invoice_id) }}"
                                        class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-print me-2"></i>In hóa đơn
                                    </a>
                                </div>
                            </div>

                            <!-- Phần QR -->
                            <div class="col-md-6">
                                <div class="qr-container">
                                    <h5 class="mb-3"><i class="fas fa-qrcode me-2 text-primary"></i>Quét mã để thanh toán
                                    </h5>

                                    <!-- QR Code -->
                                    <div class="text-center mb-3">
                                        <img src="https://img.vietqr.io/image/vietcombank-1234567890-compact.png?amount={{ $invoice->total_price }}&addInfo=THANHTOAN{{ $invoice->invoice_id }}"
                                            class="qr-code" alt="QR Code">
                                    </div>

                                    <!-- Các bước thanh toán -->
                                    <div class="text-start">
                                        <h6 class="mb-3">Hướng dẫn thanh toán:</h6>

                                        <div class="step">
                                            <div class="step-number">1</div>
                                            <div class="step-text">Mở ứng dụng ngân hàng hoặc ví điện tử có liên kết VNPAY
                                            </div>
                                        </div>

                                        <div class="step">
                                            <div class="step-number">2</div>
                                            <div class="step-text">Quét mã QR bằng chức năng QR Pay trong ứng dụng</div>
                                        </div>

                                        <div class="step">
                                            <div class="step-number">3</div>
                                            <div class="step-text">Kiểm tra thông tin và xác nhận thanh toán</div>
                                        </div>

                                        <div class="step">
                                            <div class="step-number">4</div>
                                            <div class="step-text">Hoàn tất giao dịch và chờ xác nhận từ nhà hàng</div>
                                        </div>
                                    </div>

                                    <!-- Đếm ngược -->
                                    <div class="timer-container">
                                        <p class="mb-2">Mã QR có hiệu lực trong:</p>
                                        <div class="timer" id="countdown">15:00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Hàm đếm ngược thời gian
        function startCountdown() {
            let minutes = 15;
            let seconds = 0;

            const countdownElement = document.getElementById('countdown');

            const countdownInterval = setInterval(function() {
                if (seconds === 0) {
                    if (minutes === 0) {
                        clearInterval(countdownInterval);
                        countdownElement.textContent = "Hết hạn";
                        return;
                    }
                    minutes--;
                    seconds = 59;
                } else {
                    seconds--;
                }

                // Hiển thị thời gian dạng MM:SS
                countdownElement.textContent =
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (seconds < 10 ? "0" + seconds : seconds);
            }, 1000);
        }

        $(document).ready(function() {
            // Khởi động đếm ngược
            startCountdown();

            // Hiển thị thông báo thành công hoặc lỗi
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            // Kiểm tra trạng thái thanh toán mỗi 5 giây
            let checkPaymentStatus = function() {
                $.ajax({
                    url: "{{ route('invoices.checkPayment', $invoice->invoice_id) }}",
                    type: "GET",
                    success: function(data) {
                        if (data.paid) {
                            // Nếu đã thanh toán, hiển thị thông báo và chuyển hướng
                            toastr.success("Thanh toán thành công!");

                            setTimeout(function() {
                                window.location.href = "{{ route('invoices.index') }}";
                            }, 3000);
                        }
                    }
                });
            };

            // Kiểm tra trạng thái ban đầu
            checkPaymentStatus();

            // Thiết lập kiểm tra định kỳ
            setInterval(checkPaymentStatus, 5000);
        });
    </script>
@endsection
