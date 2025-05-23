<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    // Dashboard khách hàng
    public function dashboard()
    {
        $user = Auth::user();
        
        // Lấy đặt bàn hiện tại của khách hàng
        $myReservations = Table::where('reserved_phone', $user->phone)
            ->orWhere('reserved_by', $user->name)
            ->whereIn('status', ['Đã đặt', 'Đến muộn'])
            ->with('area')
            ->orderBy('reserved_time', 'asc')
            ->get();

        // Thống kê nhanh
        $stats = [
            'total_tables' => Table::count(),
            'available_tables' => Table::where('status', 'Trống')->count(),
            'my_reservations' => $myReservations->count(),
            'areas_count' => Area::count()
        ];

        return view('customer.dashboard', compact('myReservations', 'stats'));
    }

    // Trang chọn bàn đơn giản
    public function selectTable()
    {
        try {
            // Lấy tất cả bàn với thông tin cơ bản
            $tables = Table::with('area')
                ->orderBy('area_id')
                ->orderBy('table_number')
                ->get()
                ->map(function($table) {
                    return [
                        'table_id' => $table->table_id,
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'table_type' => $table->table_type,
                        'status' => $table->status,
                        'area_name' => optional($table->area)->name ?? 'Không xác định',
                        'is_reservable' => $table->is_reservable,
                        'reserved_time' => $table->reserved_time ? $table->reserved_time->format('d/m/Y H:i') : null,
                        'reserved_by' => $table->reserved_by,
                        'reserved_party_size' => $table->reserved_party_size,
                        'is_available' => $table->status === 'Trống' && $table->is_reservable
                    ];
                });

            // Nhóm bàn theo khu vực
            $tablesByArea = $tables->groupBy('area_name');

            // Thống kê
            $stats = [
                'total_tables' => $tables->count(),
                'available_tables' => $tables->where('is_available', true)->count(),
                'reserved_tables' => $tables->whereIn('status', ['Đã đặt', 'Đến muộn'])->count(),
            ];

            return view('customer.select-table', compact('tablesByArea', 'stats'));
            
        } catch (\Exception $e) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Có lỗi xảy ra khi tải danh sách bàn: ' . $e->getMessage());
        }
    }

    // Trang đặt bàn cho bàn cụ thể
    public function reserveTable($tableId)
    {
        try {
            $table = Table::with('area')->findOrFail($tableId);
            
            // Kiểm tra bàn có thể đặt không
            if ($table->status !== 'Trống' || !$table->is_reservable) {
                return redirect()->route('customer.select-table')
                    ->with('error', 'Bàn này không thể đặt trước.');
            }

            $tableInfo = [
                'table_id' => $table->table_id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'table_type' => $table->table_type,
                'area_name' => optional($table->area)->name ?? 'Không xác định',
                'min_spend' => $table->min_spend,
                'notes' => $table->notes
            ];

            return view('customer.reserve-table', compact('tableInfo'));
            
        } catch (\Exception $e) {
            return redirect()->route('customer.select-table')
                ->with('error', 'Không tìm thấy bàn hoặc có lỗi xảy ra.');
        }
    }
    // Hiển thị sơ đồ tầng
    public function floorPlan($floor = 1)
    {
        $areas = Area::with(['tables' => function($query) {
            $query->select('table_id', 'table_number', 'capacity', 'status', 'area_id', 
                          'reserved_time', 'reserved_by', 'reserved_party_size', 'reservation_notes');
        }])
        ->where('floor', $floor)
        ->orderBy('code')
        ->get();

        $floors = Area::select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('customer.floor-plan', compact('areas', 'floors', 'floor'));
    }

    // API: Lấy layout tầng
    public function getFloorLayout($floor = null)
    {
        $query = Area::with(['tables' => function($query) {
            $query->select('table_id', 'table_number', 'capacity', 'status', 'area_id', 
                          'reserved_time', 'reserved_by', 'reserved_party_size');
        }]);

        if ($floor) {
            $query->where('floor', $floor);
        }

        $areas = $query->orderBy('floor')->orderBy('code')->get();

        return response()->json([
            'success' => true,
            'areas' => $areas,
            'current_time' => now()->format('Y-m-d H:i:s')
        ]);
    }

    // API: Đặt bàn
    public function makeReservation(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,table_id',
            'reserved_by' => 'required|string|max:100',
            'reserved_phone' => 'required|string|max:20',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'party_size' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);

            // Kiểm tra bàn có trống không
            if ($table->status !== 'Trống') {
                return back()->withInput()->with('error', 'Bàn này đã được đặt hoặc đang sử dụng');
            }

            // Kiểm tra sức chứa
            if ($request->party_size > $table->capacity) {
                return back()->withInput()->with('error', "Bàn này chỉ phù hợp cho tối đa {$table->capacity} người");
            }

            // Tạo datetime từ date và time
            $reservationDateTime = Carbon::createFromFormat(
                'Y-m-d H:i', 
                $request->reservation_date . ' ' . $request->reservation_time
            );

            // Kiểm tra thời gian đặt (phải từ hiện tại trở đi)
            if ($reservationDateTime->isPast()) {
                return back()->withInput()->with('error', 'Không thể đặt bàn cho thời gian đã qua');
            }

            // Cập nhật thông tin đặt bàn
            $table->update([
                'status' => 'Đã đặt',
                'reserved_by' => $request->reserved_by,
                'reserved_phone' => $request->reserved_phone,
                'reserved_time' => $reservationDateTime,
                'reserved_party_size' => $request->party_size,
                'reservation_notes' => $request->notes,
                'reserved_at' => now()
            ]);

            DB::commit();

            // Redirect về trang đặt bàn của tôi với thông báo thành công
            return redirect()->route('customer.reservations')
                ->with('success', "Đặt bàn {$table->table_number} thành công! Vui lòng đến đúng giờ.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xem đặt bàn của tôi
    public function myReservations()
    {
        $user = Auth::user();
        
        $reservations = Table::where(function($query) use ($user) {
            $query->where('reserved_phone', $user->phone)
                  ->orWhere('reserved_by', $user->name);
        })
        ->whereIn('status', ['Đã đặt', 'Đến muộn'])
        ->with('area')
        ->orderBy('reserved_time', 'asc')
        ->get();

        return view('customer.reservations', compact('reservations'));
    }

    // Hủy đặt bàn
    public function cancelReservation($tableId)
    {
        try {
            $table = Table::findOrFail($tableId);
            $user = Auth::user();

            // Kiểm tra quyền hủy (chỉ người đặt mới được hủy)
            if ($table->reserved_phone !== $user->phone && $table->reserved_by !== $user->name) {
                return back()->with('error', 'Bạn không có quyền hủy đặt bàn này');
            }

            // Kiểm tra trạng thái bàn
            if (!in_array($table->status, ['Đã đặt', 'Đến muộn'])) {
                return back()->with('error', 'Bàn này không có đặt bàn nào');
            }

            // Kiểm tra thời gian (có thể hủy trước 30 phút)
            if ($table->reserved_time && $table->reserved_time->diffInMinutes(now(), false) < 30) {
                return back()->with('error', 'Không thể hủy đặt bàn trong vòng 30 phút trước giờ đặt');
            }

            // Hủy đặt bàn
            $table->cancelReservation();

            return back()->with('success', 'Đã hủy đặt bàn thành công');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}