<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    // Dashboard khách hàng - ĐÃ SỬA LOGIC
    public function dashboard()
    {
        $user = Auth::user();
        
        // Lấy đặt bàn hiện tại - SỬA LOGIC ĐỂ TƯƠNG THÍCH HỚN
        $myReservations = Table::where(function($query) use ($user) {
            // Kiểm tra theo phone HOẶC name (không phải cả hai)
            $query->where('reserved_phone', $user->phone)
                  ->orWhere('reserved_by', $user->name);
        })
        ->whereIn('status', ['Đã đặt', 'Đến muộn'])
        // BỎ ĐIỀU KIỆN THỜI GIAN TƯƠNG LAI - cho phép xem cả đặt bàn hôm nay
        ->whereNotNull('reserved_phone')
        ->whereNotNull('reserved_by')
        ->with('area')
        ->orderBy('reserved_time', 'asc')
        ->get();

        // Thống kê nhanh
        $stats = [
            'total_tables' => Table::count(),
            'available_tables' => Table::where('status', 'Trống')->where('is_reservable', true)->count(),
            'my_reservations' => $myReservations->count(),
            'areas_count' => Area::where('status', 'Hoạt động')->count()
        ];

        return view('customer.dashboard', compact('myReservations', 'stats'));
    }

    // Trang chọn bàn đơn giản
    public function selectTable()
    {
        try {
            // Lấy tất cả bàn với thông tin cơ bản - chỉ lấy bàn đang hoạt động
            $tables = Table::with(['area' => function($query) {
                $query->where('status', 'Hoạt động');
            }])
            ->whereHas('area', function($query) {
                $query->where('status', 'Hoạt động');
            })
            ->where('is_reservable', true)
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
                          'reserved_time', 'reserved_by', 'reserved_party_size', 'reservation_notes')
                  ->where('is_reservable', true);
        }])
        ->where('floor', $floor)
        ->where('status', 'Hoạt động')
        ->orderBy('code')
        ->get();

        $floors = Area::select('floor')
            ->where('status', 'Hoạt động')
            ->distinct()
            ->orderBy('floor')
            ->pluck('floor');

        return view('customer.floor-plan', compact('areas', 'floors', 'floor'));
    }

    // API: Đặt bàn - SỬA ĐỂ TƯƠNG THÍCH VỚI FORM
    public function makeReservation(Request $request)
    {
        // DEBUG: Log dữ liệu đầu vào
        Log::info('Reservation request data:', $request->all());

        $request->validate([
            'table_id' => 'required|exists:tables,table_id',
            'reserved_by' => 'required|string|max:100',
            'reserved_phone' => 'required|string|max:20',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required', // Bỏ format validation tạm thời
            'party_size' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $table = Table::findOrFail($request->table_id);
            $user = Auth::user();

            // Kiểm tra bàn có trống không
            if ($table->status !== 'Trống') {
                DB::rollBack();
                return back()->withInput()->with('error', 'Bàn này đã được đặt hoặc đang sử dụng');
            }

            // Kiểm tra sức chứa
            if ($request->party_size > $table->capacity) {
                DB::rollBack();
                return back()->withInput()->with('error', "Bàn này chỉ phù hợp cho tối đa {$table->capacity} người");
            }

            // Tạo datetime - SỬA CÁCH XỬ LÝ THỜI GIAN
            try {
                // Nếu reservation_time có format H:i
                if (preg_match('/^\d{2}:\d{2}$/', $request->reservation_time)) {
                    $reservationDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i', 
                        $request->reservation_date . ' ' . $request->reservation_time
                    );
                } else {
                    // Fallback nếu format khác
                    $reservationDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
                }
            } catch (\Exception $e) {
                Log::error('DateTime parsing error: ' . $e->getMessage());
                DB::rollBack();
                return back()->withInput()->with('error', 'Định dạng thời gian không hợp lệ');
            }

            // Kiểm tra thời gian đặt (phải từ hiện tại trở đi)
            if ($reservationDateTime->isPast()) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Không thể đặt bàn cho thời gian đã qua');
            }

            // Cập nhật thông tin đặt bàn - ĐẢM BẢO DỮ LIỆU ĐÚNG
            $updateData = [
                'status' => 'Đã đặt',
                'reserved_by' => trim($request->reserved_by),
                'reserved_phone' => trim($request->reserved_phone),
                'reserved_time' => $reservationDateTime,
                'reserved_party_size' => $request->party_size,
                'reservation_notes' => $request->notes ? trim($request->notes) : null,
                'reserved_at' => now()
            ];

            // DEBUG: Log dữ liệu cập nhật
            Log::info('Updating table with data:', $updateData);

            $table->update($updateData);

            // Verify update
            $table->refresh();
            Log::info('Table after update:', $table->toArray());

            DB::commit();

            return redirect()->route('customer.reservations')
                ->with('success', "Đặt bàn {$table->table_number} thành công! Thời gian: {$reservationDateTime->format('d/m/Y H:i')}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi đặt bàn: ' . $e->getMessage());
        }
    }

    // Xem đặt bàn của tôi - SỬA LOGIC
    public function myReservations()
    {
        $user = Auth::user();
        
        // SỬA LOGIC: sử dụng OR thay vì AND, bỏ điều kiện thời gian tương lai
        $reservations = Table::where(function($query) use ($user) {
            $query->where('reserved_phone', $user->phone)
                  ->orWhere('reserved_by', $user->name);
        })
        ->whereIn('status', ['Đã đặt', 'Đến muộn'])
        ->whereNotNull('reserved_phone')
        ->whereNotNull('reserved_by')
        ->with('area')
        ->orderBy('reserved_time', 'asc')
        ->get();

        // DEBUG: Log kết quả
        Log::info('My reservations query result:', [
            'user_phone' => $user->phone,
            'user_name' => $user->name,
            'reservations_count' => $reservations->count(),
            'reservations' => $reservations->toArray()
        ]);

        return view('customer.reservations', compact('reservations'));
    }

    // Hủy đặt bàn - GIỮ NGUYÊN NHƯNG CẢI THIỆN LOG
    public function cancelReservation($tableId)
    {
        try {
            $table = Table::findOrFail($tableId);
            $user = Auth::user();

            Log::info('Cancel reservation attempt:', [
                'table_id' => $tableId,
                'user_phone' => $user->phone,
                'user_name' => $user->name,
                'table_reserved_phone' => $table->reserved_phone,
                'table_reserved_by' => $table->reserved_by
            ]);

            // Kiểm tra quyền hủy
            if ($table->reserved_phone !== $user->phone && $table->reserved_by !== $user->name) {
                return back()->with('error', 'Bạn không có quyền hủy đặt bàn này');
            }

            // Kiểm tra trạng thái bàn
            if (!in_array($table->status, ['Đã đặt', 'Đến muộn'])) {
                return back()->with('error', 'Bàn này không có đặt bàn nào');
            }

            // Kiểm tra thời gian (có thể hủy trước 1 giờ)
            if ($table->reserved_time && $table->reserved_time->diffInMinutes(now(), false) < 60) {
                return back()->with('error', 'Không thể hủy đặt bàn trong vòng 1 giờ trước giờ đặt');
            }

            // Hủy đặt bàn
            $table->update([
                'status' => 'Trống',
                'reserved_by' => null,
                'reserved_phone' => null,
                'reserved_time' => null,
                'reserved_party_size' => null,
                'reservation_notes' => null,
                'reserved_at' => null
            ]);

            Log::info('Reservation cancelled successfully for table: ' . $tableId);

            return back()->with('success', 'Đã hủy đặt bàn thành công');

        } catch (\Exception $e) {
            Log::error('Cancel reservation error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi hủy đặt bàn');
        }
    }

    // METHOD DEBUG - THÊM ĐỂ KIỂM TRA DỮ LIỆU
    public function debugReservations()
    {
        $user = Auth::user();
        
        $allReservations = Table::whereNotNull('reserved_phone')
            ->orWhereNotNull('reserved_by')
            ->get(['table_id', 'table_number', 'status', 'reserved_by', 'reserved_phone', 'reserved_time']);
            
        $myReservations = Table::where(function($query) use ($user) {
            $query->where('reserved_phone', $user->phone)
                  ->orWhere('reserved_by', $user->name);
        })
        ->whereIn('status', ['Đã đặt', 'Đến muộn'])
        ->get();

        dd([
            'user_info' => [
                'phone' => $user->phone,
                'name' => $user->name,
            ],
            'all_reservations' => $allReservations->toArray(),
            'my_reservations' => $myReservations->toArray(),
            'my_reservations_count' => $myReservations->count()
        ]);
    }
}