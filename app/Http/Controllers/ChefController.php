<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChefController extends Controller
{
    /**
     * Hiển thị trang dashboard cho đầu bếp
     */
    public function index()
    {
        // Lấy các món ăn đã gửi đến bếp nhưng chưa được chef xác nhận
        $pendingItems = InvoiceDetail::with(['dish', 'variant', 'invoice.table'])
            ->whereNotNull('sent_to_kitchen_at')          // Đã gửi bếp
            ->whereNull('chef_confirmed_at')              // Chưa xác nhận bởi chef
            ->orderBy('sent_to_kitchen_at', 'asc')        // Sắp xếp theo thời gian gửi bếp
            ->get();

        // Nhóm các món theo hóa đơn và thời gian gửi bếp để hiển thị theo batch
        $groupedOrders = $pendingItems->groupBy(function($item) {
            // Nhóm theo invoice_id và sent_to_kitchen_at (để phân biệt các lần gửi khác nhau)
            return $item->invoice_id . '_' . $item->sent_to_kitchen_at;
        });

        // Chuyển đổi thành format dễ sử dụng trong view
        $pendingOrders = $groupedOrders->map(function($items, $key) {
            $firstItem = $items->first();
            return (object) [
                'invoice_id' => $firstItem->invoice_id,
                'invoice' => $firstItem->invoice,
                'table' => $firstItem->invoice->table,
                'sent_to_kitchen_at' => $firstItem->sent_to_kitchen_at,
                'items' => $items,
                'batch_key' => $key, // Để identify batch khi xác nhận
                'total_items' => $items->count()
            ];
        });
        
        return view('chef.dashboard', compact('pendingOrders'));
    }

    /**
     * Xác nhận đã chế biến xong một batch món ăn
     */
    public function confirmOrder(Request $request, $invoiceId)
    {
        try {
            DB::beginTransaction();

            // Lấy sent_to_kitchen_at từ request (để xác định batch nào)
            $sentTime = $request->input('sent_time');
            
            if ($sentTime) {
                // Xác nhận theo batch (cùng thời gian gửi bếp)
                $updatedCount = InvoiceDetail::where('invoice_id', $invoiceId)
                    ->where('sent_to_kitchen_at', $sentTime)
                    ->whereNull('chef_confirmed_at')
                    ->update([
                        'chef_confirmed_at' => now()
                    ]);
                    
                Log::info("Chef confirmed batch for invoice $invoiceId at $sentTime. Updated $updatedCount items.");
            } else {
                // Fallback: Xác nhận tất cả món chưa confirm của hóa đơn này
                $updatedCount = InvoiceDetail::where('invoice_id', $invoiceId)
                    ->whereNotNull('sent_to_kitchen_at')
                    ->whereNull('chef_confirmed_at')
                    ->update([
                        'chef_confirmed_at' => now()
                    ]);
                    
                Log::info("Chef confirmed all pending items for invoice $invoiceId. Updated $updatedCount items.");
            }

            // Kiểm tra xem tất cả món trong hóa đơn đã được confirm chưa
            $invoice = Invoice::findOrFail($invoiceId);
            $totalSentItems = $invoice->items()->whereNotNull('sent_to_kitchen_at')->count();
            $confirmedItems = $invoice->items()->whereNotNull('chef_confirmed_at')->count();
            
            // Nếu tất cả món đã gửi bếp đều được confirm, cập nhật trạng thái hóa đơn
            if ($totalSentItems > 0 && $totalSentItems == $confirmedItems) {
                $invoice->update(['status' => 'Đã phục vụ']);
                Log::info("Invoice $invoiceId status updated to 'Đã phục vụ' - all sent items confirmed.");
            }

            DB::commit();
            
            return redirect()->route('chef.dashboard')
                ->with('success', "Đã xác nhận hoàn thành $updatedCount món ăn!");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Lỗi khi xác nhận đơn hàng: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận một món cụ thể (option cho tương lai)
     */
    public function confirmItem(Request $request, $itemId)
    {
        try {
            DB::beginTransaction();

            $item = InvoiceDetail::findOrFail($itemId);
            
            // Xác nhận món này
            $item->update(['chef_confirmed_at' => now()]);
            
            Log::info("Chef confirmed item $itemId");

            // Kiểm tra xem tất cả món trong hóa đơn đã được confirm chưa
            $invoice = $item->invoice;
            $totalSentItems = $invoice->items()->whereNotNull('sent_to_kitchen_at')->count();
            $confirmedItems = $invoice->items()->whereNotNull('chef_confirmed_at')->count();
            
            if ($totalSentItems > 0 && $totalSentItems == $confirmedItems) {
                $invoice->update(['status' => 'Đã phục vụ']);
                Log::info("Invoice {$invoice->invoice_id} status updated to 'Đã phục vụ' - all sent items confirmed.");
            }

            DB::commit();
            
            return redirect()->route('chef.dashboard')
                ->with('success', 'Đã xác nhận hoàn thành món ăn!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming item: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Lỗi khi xác nhận món ăn: ' . $e->getMessage());
        }
    }
}