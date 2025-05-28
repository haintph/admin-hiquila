<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    // Hiển thị danh sách bàn theo khu vực
    public function index(Request $request)
    {
        $area_id = $request->query('area_id');
        $status = $request->query('status');
        $table_type = $request->query('table_type');

        // Lấy danh sách khu vực có sắp xếp theo tầng và mã
        $areas = Area::orderBy('floor')->orderBy('code')->get();

        $tables = Table::with('area')
            ->when($area_id, function ($query) use ($area_id) {
                return $query->where('area_id', $area_id);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($table_type, function ($query) use ($table_type) {
                return $query->where('table_type', $table_type);
            })
            ->orderBy('table_number')
            ->paginate(15);

        // Lấy danh sách các loại bàn và trạng thái để hiển thị trong form lọc
        $tableTypes = [
            'Bàn đơn',
            'Bàn đôi',
            'Bàn 4',
            'Bàn 6',
            'Bàn 8',
            'Bàn dài',
            'Bàn VIP',
            'Bàn tròn'
        ];

        $statuses = [
            'Trống',
            'Đã đặt',
            'Đang phục vụ',
            'Đang dọn',
            'Bảo trì',
            'Không hoạt động'
        ];

        return view('admin.tables.index', compact('tables', 'areas', 'area_id', 'status', 'table_type', 'tableTypes', 'statuses'));
    }


    // Hiển thị form thêm bàn
    public function create(Request $request)
    {
        // Lấy danh sách khu vực có sắp xếp theo tầng và mã
        $areas = Area::orderBy('floor')->orderBy('code')->get();

        // Nếu có area_id từ query string, đặt làm mặc định
        $selectedAreaId = $request->query('area_id');

        // Danh sách các loại bàn và trạng thái cho dropdown
        $tableTypes = [
            'Bàn đơn',
            'Bàn đôi',
            'Bàn 4',
            'Bàn 6',
            'Bàn 8',
            'Bàn dài',
            'Bàn VIP',
            'Bàn tròn'
        ];

        $statuses = [
            'Trống',
            'Đã đặt',
            'Đang phục vụ',
            'Đang dọn',
            'Bảo trì',
            'Không hoạt động'
        ];

        return view('admin.tables.create', compact('areas', 'tableTypes', 'statuses', 'selectedAreaId'));
    }

    /**
     * Tự động tạo số bàn theo format khu vực
     */
    private function generateTableNumber($areaId)
    {
        if (!$areaId) {
            // Nếu không có khu vực, tạo số bàn tự do
            $lastTable = Table::whereNull('area_id')
                ->orderByRaw('CAST(SUBSTRING(table_number, 1) AS UNSIGNED) DESC')
                ->first();

            if ($lastTable && preg_match('/(\d+)/', $lastTable->table_number, $matches)) {
                return (int)$matches[1] + 1;
            }
            return '1';
        }

        $area = Area::find($areaId);
        if (!$area) {
            return '1';
        }

        // Tìm số bàn cao nhất trong khu vực này
        $lastTable = Table::where('area_id', $areaId)
            ->where('table_number', 'LIKE', $area->code . '%')
            ->orderByRaw('CAST(SUBSTRING(table_number, 2) AS UNSIGNED) DESC')
            ->first();

        if ($lastTable && preg_match('/^' . $area->code . '(\d+)$/', $lastTable->table_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return $area->code . $nextNumber;
    }

    // Xử lý lưu bàn mới
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => [
                'nullable',
                'max:10',
                Rule::unique('tables')->where(function ($query) use ($request) {
                    return $query->where('area_id', $request->area_id);
                })
            ],
            'capacity' => 'required|integer|min:1|max:20',
            'table_type' => 'required|in:Bàn đơn,Bàn đôi,Bàn 4,Bàn 6,Bàn 8,Bàn dài,Bàn VIP,Bàn tròn',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì,Không hoạt động',
            'area_id' => 'nullable|exists:areas,area_id',
            'min_spend' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'is_reservable' => 'nullable|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra logic sức chứa và loại bàn
            $capacityValidation = $this->validateTableCapacityAndType($request->capacity, $request->table_type);
            if (!$capacityValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $capacityValidation['message']);
            }

            // Tự động tạo số bàn nếu không có
            if (empty($request->table_number)) {
                $tableNumber = $this->generateTableNumber($request->area_id);
                $request->merge(['table_number' => $tableNumber]);
            }

            // Xử lý checkbox is_reservable
            $isReservable = $request->has('is_reservable') ? (int)$request->is_reservable : 1;
            $request->merge(['is_reservable' => $isReservable]);

            // Kiểm tra sức chứa của khu vực nếu có chọn khu vực
            if ($request->area_id) {
                $area = Area::findOrFail($request->area_id);

                // Kiểm tra format số bàn phù hợp với khu vực
                if (!preg_match('/^' . $area->code . '\d+$/', $request->table_number)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Số bàn phải có định dạng {$area->code}1, {$area->code}2, {$area->code}3... cho khu vực {$area->code}");
                }

                // Tính tổng sức chứa hiện tại của khu vực
                $currentCapacity = Table::where('area_id', $request->area_id)->sum('capacity');

                // Nếu thêm bàn mới vượt quá sức chứa của khu vực
                if ($area->capacity && ($currentCapacity + $request->capacity > $area->capacity)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Không thể thêm bàn vì vượt quá sức chứa của khu vực! Sức chứa hiện tại: {$currentCapacity}/{$area->capacity}. Bạn chỉ có thể thêm bàn với sức chứa tối đa " . ($area->capacity - $currentCapacity) . ' người.');
                }

                // Kiểm tra trạng thái khu vực và cập nhật trạng thái bàn nếu cần
                if ($area->hourSetting && $area->hourSetting->has_operating_hours) {
                    $isInOperatingHours = $area->isWithinOperatingHours();
                    if (!$isInOperatingHours) {
                        $request->merge(['status' => 'Không hoạt động']);
                    }
                }
            }

            Table::create($request->all());

            DB::commit();

            return redirect()->route('tables.index')
                ->with('success', 'Bàn đã được thêm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Hiển thị form chỉnh sửa bàn
    public function edit($id)
    {
        $table = Table::with('area')->findOrFail($id);
        $areas = Area::orderBy('floor')->orderBy('code')->get();

        // Danh sách các loại bàn và trạng thái cho dropdown
        $tableTypes = [
            'Bàn đơn',
            'Bàn đôi',
            'Bàn 4',
            'Bàn 6',
            'Bàn 8',
            'Bàn dài',
            'Bàn VIP',
            'Bàn tròn'
        ];

        $statuses = [
            'Trống',
            'Đã đặt',
            'Đang phục vụ',
            'Đang dọn',
            'Bảo trì',
            'Không hoạt động'
        ];

        return view('admin.tables.edit', compact('table', 'areas', 'tableTypes', 'statuses'));
    }

    // Xử lý cập nhật bàn
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'table_number' => [
                'required',
                'max:10',
                Rule::unique('tables')->where(function ($query) use ($request) {
                    return $query->where('area_id', $request->area_id);
                })->ignore($id, 'table_id')
            ],
            'capacity' => 'required|integer|min:1|max:20',
            'table_type' => 'required|in:Bàn đơn,Bàn đôi,Bàn 4,Bàn 6,Bàn 8,Bàn dài,Bàn VIP,Bàn tròn',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì,Không hoạt động',
            'area_id' => 'nullable|exists:areas,area_id',
            'min_spend' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'is_reservable' => 'nullable|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra logic sức chứa và loại bàn
            $capacityValidation = $this->validateTableCapacityAndType($request->capacity, $request->table_type);
            if (!$capacityValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $capacityValidation['message']);
            }

            // Xử lý checkbox is_reservable
            $isReservable = $request->has('is_reservable') ? (int)$request->is_reservable : 0;
            $request->merge(['is_reservable' => $isReservable]);

            // Kiểm tra sức chứa của khu vực nếu có thay đổi khu vực hoặc sức chứa
            if ($request->area_id && ($table->area_id != $request->area_id || $table->capacity != $request->capacity)) {
                $area = Area::findOrFail($request->area_id);

                // Kiểm tra format số bàn phù hợp với khu vực
                if (!preg_match('/^' . $area->code . '\d+$/', $request->table_number)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Số bàn phải có định dạng {$area->code}1, {$area->code}2, {$area->code}3... cho khu vực {$area->code}");
                }

                // Tính tổng sức chứa hiện tại của khu vực (trừ đi bàn hiện tại nếu đã thuộc khu vực này)
                $currentCapacity = Table::where('area_id', $request->area_id)
                    ->when($table->area_id == $request->area_id, function ($query) use ($id) {
                        return $query->where('table_id', '!=', $id);
                    })
                    ->sum('capacity');

                // Nếu cập nhật bàn vượt quá sức chứa của khu vực
                if ($area->capacity && ($currentCapacity + $request->capacity > $area->capacity)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Không thể cập nhật bàn vì vượt quá sức chứa của khu vực! Sức chứa hiện tại: {$currentCapacity}/{$area->capacity}. Bạn chỉ có thể cập nhật bàn với sức chứa tối đa " . ($area->capacity - $currentCapacity) . ' người.');
                }

                // Kiểm tra trạng thái khu vực và cập nhật trạng thái bàn nếu cần
                if ($area->hourSetting && $area->hourSetting->has_operating_hours) {
                    $isInOperatingHours = $area->isWithinOperatingHours();
                    if (!$isInOperatingHours && !in_array($request->status, ['Đã đặt', 'Đang phục vụ', 'Bảo trì'])) {
                        $request->merge(['status' => 'Không hoạt động']);
                    }
                }
            }

            $table->update($request->all());

            DB::commit();

            return redirect()->route('tables.index')
                ->with('success', 'Bàn đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xóa bàn (soft delete)
    public function destroy($id)
    {
        try {
            $table = Table::findOrFail($id);

            // Kiểm tra xem bàn có đang được sử dụng không
            if (in_array($table->status, ['Đã đặt', 'Đang phục vụ'])) {
                return redirect()->route('tables.index')
                    ->with('error', 'Không thể xóa bàn đang được sử dụng!');
            }

            $table->delete();

            return redirect()->route('tables.index')
                ->with('success', 'Bàn đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->route('tables.index')
                ->with('error', 'Có lỗi xảy ra khi xóa bàn: ' . $e->getMessage());
        }
    }

    // Cập nhật nhanh trạng thái bàn
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì,Không hoạt động',
        ]);

        try {
            $table = Table::findOrFail($id);

            // Kiểm tra khu vực có ngoài giờ hoạt động không
            if ($table->area_id) {
                $area = Area::with(['hourSetting', 'operatingHours'])->find($table->area_id);

                if ($area && $area->hourSetting && $area->hourSetting->has_operating_hours) {
                    $isInOperatingHours = $area->isWithinOperatingHours();

                    // Nếu ngoài giờ hoạt động và đang cố gắng thay đổi thành các trạng thái không được phép
                    if (
                        !$isInOperatingHours &&
                        !in_array($request->status, ['Đã đặt', 'Đang phục vụ', 'Bảo trì', 'Không hoạt động'])
                    ) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không thể thay đổi trạng thái này khi khu vực ngoài giờ hoạt động!'
                        ], 400);
                    }
                }
            }

            // Nếu đổi từ trạng thái khác sang "Đang phục vụ", cập nhật thời gian bắt đầu
            if ($table->status !== 'Đang phục vụ' && $request->status === 'Đang phục vụ') {
                $table->occupied_at = now();
            }

            // Nếu đổi từ "Đang phục vụ" sang "Trống", "Đang dọn" hoặc "Không hoạt động", reset thời gian
            if ($table->status === 'Đang phục vụ' && in_array($request->status, ['Trống', 'Đang dọn', 'Không hoạt động'])) {
                $table->occupied_at = null;
                $table->current_order_id = null;
            }

            $table->status = $request->status;
            $table->save();

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái bàn đã được cập nhật!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị chi tiết bàn
    public function show($id)
    {
        $table = Table::with(['area'])->findOrFail($id);
        return view('admin.tables.show', compact('table'));
    }

    // API để lấy thông tin bàn theo khu vực
    public function getTablesByArea($areaId)
    {
        $tables = Table::where('area_id', $areaId)
            ->orderBy('table_number')
            ->get(['table_id', 'table_number', 'capacity', 'status']);

        return response()->json($tables);
    }

    // API để lấy số bàn tiếp theo trong khu vực
    public function getNextTableNumber($areaId)
    {
        $area = Area::find($areaId);
        if (!$area) {
            return response()->json(['error' => 'Khu vực không tồn tại'], 404);
        }

        $nextNumber = $this->generateTableNumber($areaId);

        return response()->json([
            'next_table_number' => $nextNumber,
            'area_code' => $area->code
        ]);
    }

    // API để lấy sức chứa hiện tại của khu vực
    public function getCurrentCapacity($areaId, Request $request)
    {
        $excludeTableId = $request->query('exclude_table');

        $currentCapacity = Table::where('area_id', $areaId)
            ->when($excludeTableId, function ($query) use ($excludeTableId) {
                return $query->where('table_id', '!=', $excludeTableId);
            })
            ->sum('capacity');

        return response()->json([
            'current_capacity' => $currentCapacity
        ]);
    }

    // Bulk update trạng thái bàn
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'table_ids' => 'required|array',
            'table_ids.*' => 'exists:tables,table_id',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì,Không hoạt động',
        ]);

        try {
            DB::beginTransaction();

            $updated = 0;
            foreach ($request->table_ids as $tableId) {
                $table = Table::find($tableId);
                if ($table) {
                    $table->status = $request->status;
                    $table->save();
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->route('tables.index')
                ->with('success', "Đã cập nhật trạng thái cho {$updated} bàn!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tables.index')
                ->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }
    // Hàm kiểm tra logic sức chứa và loại bàn
    private function validateTableCapacityAndType($capacity, $tableType)
    {
        $typeCapacityRules = [
            'Bàn đơn' => [1, 2],
            'Bàn đôi' => [2, 4],
            'Bàn 4' => [3, 4],
            'Bàn 6' => [5, 6],
            'Bàn 8' => [7, 8],
            'Bàn dài' => [6, 12],
            'Bàn VIP' => [2, 10],
            'Bàn tròn' => [6, 20]
        ];

        if (!isset($typeCapacityRules[$tableType])) {
            return ['valid' => false, 'message' => 'Loại bàn không hợp lệ.'];
        }

        $allowedRange = $typeCapacityRules[$tableType];
        $minCapacity = $allowedRange[0];
        $maxCapacity = $allowedRange[1];

        if ($capacity < $minCapacity || $capacity > $maxCapacity) {
            return [
                'valid' => false,
                'message' => "Loại bàn '{$tableType}' chỉ phù hợp với sức chứa từ {$minCapacity} đến {$maxCapacity} người."
            ];
        }

        return ['valid' => true, 'message' => ''];
    }
    // API để kiểm tra tính hợp lệ của sức chứa và loại bàn
    public function validateCapacityAndType(Request $request)
    {
        $capacity = $request->input('capacity');
        $tableType = $request->input('table_type');

        if (!$capacity || !$tableType) {
            return response()->json([
                'valid' => false,
                'message' => 'Vui lòng chọn loại bàn và nhập sức chứa.'
            ]);
        }

        $validation = $this->validateTableCapacityAndType($capacity, $tableType);

        return response()->json($validation);
    }
}
