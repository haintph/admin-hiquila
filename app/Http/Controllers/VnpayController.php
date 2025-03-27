<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class VnpayController extends Controller
{
    public function createPayment($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = route('vnpay.return');

        $vnp_TxnRef = $invoice->invoice_id;
        $vnp_OrderInfo = "Thanh toán hóa đơn #{$invoice->invoice_id}";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $invoice->total_price * 100;
        $vnp_Locale = "vn";
        $vnp_IpAddr = request()->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url .= "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        return redirect()->away($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode == '00') {
            $invoice = Invoice::findOrFail($request->vnp_TxnRef);
            $invoice->update(['status' => 'Đã thanh toán']);

            return redirect()->route('invoices.index', $invoice->invoice_id)->with('success', 'Thanh toán thành công!');
        }
        return redirect()->route('invoices.index')->with('error', 'Thanh toán thất bại!');
    }
}
