<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishVariant;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

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
            // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::beginTransaction();
            
            try {
                $invoice = Invoice::with('items')->findOrFail($request->vnp_TxnRef);
                
                // Cập nhật số lượng tồn kho khi thanh toán
                foreach ($invoice->items as $item) {
                    // Tất cả món ăn (kể cả biến thể) đều sử dụng stock của dish gốc
                    $dish = Dish::findOrFail($item->dish_id);
                    $dish->stock = max(0, $dish->stock - $item->quantity);
                    $dish->save();
                }
                
                // Cập nhật hóa đơn với đầy đủ thông tin thanh toán
                $invoice->update([
                    'status' => 'Đã thanh toán',
                    'payment_method' => 'vnpay',
                    'paid_at' => now()
                ]);
                
                // Cập nhật trạng thái bàn thành "Trống"
                Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);
                
                DB::commit();
                
                return redirect()->route('invoices.index')->with('success', 'Thanh toán VNPAY thành công!');
            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error('Lỗi khi cập nhật sau thanh toán VNPAY: ' . $e->getMessage());
                return redirect()->route('invoices.index')->with('error', 'Thanh toán thành công, nhưng có lỗi khi cập nhật: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('invoices.index')->with('error', 'Thanh toán VNPAY thất bại!');
    }
}