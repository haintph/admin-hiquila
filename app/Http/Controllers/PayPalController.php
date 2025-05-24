<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Invoice;

class PayPalController extends Controller
{
    public function createPayment($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $invoice->total_price
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('paypal.cancel')
            ]
        ]);

        if (isset($response['id'])) {
            // ✅ Lưu PayPal Order ID vào database
            $invoice->paypal_order_id = $response['id'];
            $invoice->save();

            $approvalUrl = $response['links'][1]['href'];
            return view('admin.invoices.qr', compact('invoice', 'approvalUrl'));
        } else {
            return redirect()->back()->with('error', 'Lỗi tạo đơn hàng PayPal!');
        }
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] == "COMPLETED") {
            // ✅ Lấy `paypal_order_id` từ response
            $paypalOrderId = $response['id'];

            // ✅ Tìm hóa đơn đúng theo `paypal_order_id`
            $invoice = Invoice::where('paypal_order_id', $paypalOrderId)->first();

            if (!$invoice) {
                return redirect()->route('invoices.index')->with('error', 'Không tìm thấy hóa đơn!');
            }

            // ✅ Cập nhật trạng thái hóa đơn
            $invoice->update(['status' => 'Đã thanh toán']);

            // ✅ Cập nhật trạng thái bàn về "Trống"
            $table = $invoice->table;
            if ($table) {
                $table->update(['status' => 'Trống']);
            }

            return redirect()->route('invoices.index')->with('success', 'Thanh toán thành công, bàn đã được giải phóng!');
        } else {
            return redirect()->route('invoices.index')->with('error', 'Lỗi khi xác nhận thanh toán!');
        }
    }

    public function cancelPayment()
    {
        return redirect()->route('invoices.index')->with('error', 'Bạn đã hủy thanh toán PayPal.');
    }
}
