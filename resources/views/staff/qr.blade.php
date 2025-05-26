@extends('admin.layouts.master')

@section('content')
    <div class="text-center">
        <h4>Quét mã QR để thanh toán qua PayPal</h4>
        <img src="https://quickchart.io/qr?text={{ urlencode($approvalUrl) }}&size=250" alt="QR Code PayPal">
        <br>
        <a href="{{ $approvalUrl }}" class="btn btn-primary mt-3">Hoặc nhấn vào đây để thanh toán</a>
    <d/iv>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function checkPaymentStatus() {
            $.ajax({
                url: "{{ route('invoices.checkPayment', ['invoice_id' => $invoice->invoice_id]) }}",
                method: "GET",
                success: function(response) {
                    if (response.paid) {
                        window.location.href = "{{ route('invoices.index') }}"; // Chuyển về danh sách hóa đơn
                    }
                }
            });
        }

        setInterval(checkPaymentStatus, 5000); // Kiểm tra mỗi 5 giây
    </script>
@endsection
