<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Area;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    // Danh sách tất cả đặt bàn
    public function index(Request $request)
    {
        try {
            $query = Table::with('area')
                ->whereIn('status', ['Đã đặt', 'Đến muộn'])
                ->whereNotNull('reserved_by');

            // Tìm kiếm theo tên khách hàng
            if ($request->filled('search_name')) {
                $query->where('reserved_by', 'like', '%' . $request->search_name . '%');
            }

            // Tìm kiếm theo số điện thoại
            if ($request->filled('search_phone')) {
                $query->where('reserved_phone', 'like', '%' . $request->search_phone . '%');
            }

            // Lọc theo trạng thái
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Lọc theo ngày
            if ($request->filled('date')) {
                $query->whereDate('reserved_time', $request->date);
            }

            // Lọc theo khu vực
            if ($request->filled('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Sắp xếp theo thời gian đặt
            $reservations = $query->orderBy('reserved_time', 'asc')->paginate(20);

            // Thống kê
            $stats = [
                'total_reservations' => Table::whereIn('status', ['Đã đặt', 'Đến muộn'])->count(),
                'today_reservations' => Table::whereIn('status', ['Đã đặt', 'Đến muộn'])
                    ->whereDate('reserved_time', now()->toDateString())->count(),
                'late_arrivals' => Table::where('status', 'Đến muộn')->count(),
                'upcoming_reservations' => Table::where('status', 'Đã đặt')
                    ->where('reserved_time', '>', now())->count(),
            ];

            // Danh sách khu vực cho filter
            $areas = Area::orderBy('name')->get();

            return view('admin.reservations.index', compact('reservations', 'stats', 'areas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Chi tiết đặt bàn
    public function show($tableId)
    {
        try {
            $table = Table::with('area')->findOrFail($tableId);

            if (!$table->reserved_by) {
                return back()->with('error', 'Bàn này không có đặt trước.');
            }

            return view('admin.reservations.show', compact('table'));
        } catch (\Exception $e) {
            return back()->with('error', 'Không tìm thấy đặt bàn này.');
        }
    }

    public function checkin($tableId)
    {
        DB::beginTransaction();

        try {
            $table = Table::findOrFail($tableId);

            if (!in_array($table->status, ['Đã đặt', 'Đến muộn'])) {
                return back()->with('error', 'Bàn này không thể check-in.');
            }

            // Kiểm tra xem đã có hóa đơn cho bàn này chưa
            $existingInvoice = Invoice::where('table_id', $tableId)
                ->whereIn('status', ['Đang chuẩn bị', 'Đã phục vụ'])
                ->first();

            if ($existingInvoice) {
                // Nếu đã có hóa đơn, chỉ cập nhật trạng thái bàn
                $table->update(['status' => 'Đang phục vụ']);

                DB::commit();
                return back()->with('success', "Đã check-in khách hàng vào bàn {$table->table_number}. Hóa đơn #{$existingInvoice->invoice_id} đã tồn tại.");
            }

            // Tạo hóa đơn mới từ thông tin đặt bàn
            $invoice = Invoice::create([
                'table_id' => $tableId,
                'customer_name' => $table->reserved_by,
                'customer_phone' => $table->reserved_phone,
                'party_size' => $table->reserved_party_size,
                'special_notes' => $table->reservation_notes,
                'total_price' => 0,
                'status' => 'Đang chuẩn bị',
            ]);

            // Cập nhật trạng thái bàn
            $table->update([
                'status' => 'Đang phục vụ',
                'current_order_id' => $invoice->invoice_id,
                'occupied_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', "Đã check-in khách hàng vào bàn {$table->table_number} và tạo hóa đơn #{$invoice->invoice_id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi check-in: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi check-in: ' . $e->getMessage());
        }
    }

    // Hủy đặt bàn (admin)
    public function cancel($tableId, Request $request)
    {
        try {
            $table = Table::findOrFail($tableId);

            if (!in_array($table->status, ['Đã đặt', 'Đến muộn'])) {
                return back()->with('error', 'Bàn này không thể hủy.');
            }

            $table->cancelReservation();

            return back()->with('success', "Đã hủy đặt bàn {$table->table_number}");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi hủy đặt bàn.');
        }
    }

    // Tìm kiếm nhanh - SỬA TÊN ROUTE
    public function quickSearch(Request $request)
    {
        $search = $request->get('q');

        if (empty($search)) {
            return redirect()->route('reservations.index'); // Đổi từ admin.reservations.index thành reservations.index
        }

        $reservations = Table::with('area')
            ->whereIn('status', ['Đã đặt', 'Đến muộn'])
            ->where(function ($query) use ($search) {
                $query->where('reserved_by', 'like', "%{$search}%")
                    ->orWhere('reserved_phone', 'like', "%{$search}%")
                    ->orWhere('table_number', 'like', "%{$search}%");
            })
            ->orderBy('reserved_time', 'asc')
            ->paginate(20);

        $stats = [
            'total_reservations' => $reservations->total(),
            'search_term' => $search
        ];

        $areas = Area::orderBy('name')->get();

        return view('admin.reservations.index', compact('reservations', 'stats', 'areas'));
    }
}
