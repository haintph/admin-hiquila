<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaHourSetting;
use App\Models\AreaOperatingHour;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    // Hằng số giới hạn tầng
    const MAX_FLOORS = 3;
    
    // Hiển thị danh sách khu vực
    public function index()
    {
        try {
            // Tải khu vực với số bàn và thông tin giờ hoạt động
            $areas = Area::withCount('tables')
                ->with(['hourSetting', 'operatingHours'])
                ->orderBy('floor')
                ->orderBy('code')
                ->paginate(10);
                
            $currentTime = Carbon::now()->format('H:i');

            return view('admin.areas.index', compact('areas', 'currentTime'));
        } catch (QueryException $e) {
            // Xử lý lỗi database
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                return redirect()->back()
                    ->with('db_error', 'Cấu trúc database chưa được cập nhật đầy đủ.')
                    ->with('db_error_details', $e->getMessage());
            } else {
                return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra khi truy vấn dữ liệu.');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Hiển thị form tạo khu vực
    public function create()
    {
        return view('admin.areas.create');
    }

    /**
     * Kiểm tra thứ tự tạo khu vực theo tầng
     * Phải tạo khu A trước khi tạo khu B, C...
     * Mã khu vực chỉ là chữ cái đơn: A, B, C...
     */
    private function validateAreaOrder($code, $floor, $excludeId = null)
    {
        // Kiểm tra format code - chỉ cho phép chữ cái đơn
        if (!preg_match('/^[A-Z]$/', $code)) {
            return 'Mã khu vực phải là một chữ cái: A, B, C, D...';
        }

        $areaLetter = $code;
        
        // Kiểm tra thứ tự alphabet trên từng tầng
        $existingAreas = Area::where('floor', $floor);
        
        if ($excludeId) {
            $existingAreas = $existingAreas->where('area_id', '!=', $excludeId);
        }
        
        $existingCodes = $existingAreas->pluck('code')->toArray();
        
        // Nếu đang tạo khu A, luôn cho phép
        if ($areaLetter === 'A') {
            return true;
        }
        
        // Kiểm tra các khu vực trước đó đã tồn tại chưa
        $alphabet = range('A', 'Z');
        $currentIndex = array_search($areaLetter, $alphabet);
        
        for ($i = 0; $i < $currentIndex; $i++) {
            if (!in_array($alphabet[$i], $existingCodes)) {
                return "Phải tạo khu vực {$alphabet[$i]} trước khi tạo khu vực {$areaLetter} trên tầng {$floor}";
            }
        }
        
        return true;
    }

    // Xử lý lưu khu vực mới
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code' => [
                    'required',
                    'max:10',
                    Rule::unique('areas')->where(function ($query) use ($request) {
                        return $query->where('floor', $request->floor);
                    })
                ],
                'name' => 'required|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
                'capacity' => 'nullable|integer|min:0|max:20',
                'floor' => 'required|integer|min:1|max:' . self::MAX_FLOORS,
                'is_smoking' => 'required|in:0,1',
                'is_vip' => 'required|in:0,1',
                'surcharge' => 'nullable|numeric|min:0',
                'image' => 'nullable|image|max:2048',
                'layout_data' => 'nullable|json'
            ]);

            // Kiểm tra thứ tự tạo khu vực
            $orderCheck = $this->validateAreaOrder($validatedData['code'], $validatedData['floor']);
            if ($orderCheck !== true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $orderCheck);
            }

            // Bắt đầu transaction để lưu khu vực
            DB::beginTransaction();
            
            // Chuẩn bị dữ liệu cho khu vực
            $areaData = $validatedData;
            // Chuyển đổi string thành integer
            $areaData['is_smoking'] = (int) $validatedData['is_smoking'];
            $areaData['is_vip'] = (int) $validatedData['is_vip'];

            // Tạo khu vực mới
            $area = new Area($areaData);

            // Xử lý ảnh nếu có
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('areas', 'public');
                $area->image = $path;
            }

            $area->save();
            
            DB::commit();

            return redirect()->route('areas.index')
                ->with('success', 'Khu vực đã được thêm thành công!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        try {
            $area = Area::with(['tables', 'hourSetting', 'operatingHours'])->findOrFail($id);
            return view('admin.areas.edit', compact('area'));
        } catch (Exception $e) {
            return redirect()->route('areas.index')
                ->with('error', 'Không tìm thấy khu vực yêu cầu.');
        }
    }
   
    // Xử lý cập nhật khu vực
    public function update(Request $request, $id)
    {
        try {
            $area = Area::findOrFail($id);

            $validatedData = $request->validate([
                'code' => [
                    'required',
                    'max:10',
                    Rule::unique('areas')->where(function ($query) use ($request, $area) {
                        return $query->where('floor', $request->floor);
                    })->ignore($area->area_id, 'area_id')
                ],
                'name' => 'required|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
                'capacity' => 'nullable|integer|min:0|max:20',
                'floor' => 'required|integer|min:1|max:' . self::MAX_FLOORS,
                'is_smoking' => 'required|in:0,1',
                'is_vip' => 'required|in:0,1',
                'surcharge' => 'nullable|numeric|min:0',
                'image' => 'nullable|image|max:2048',
                'layout_data' => 'nullable|json'
            ]);

            // Kiểm tra thứ tự khu vực nếu code hoặc floor thay đổi
            if ($validatedData['code'] !== $area->code || $validatedData['floor'] !== $area->floor) {
                $orderCheck = $this->validateAreaOrder($validatedData['code'], $validatedData['floor'], $area->area_id);
                if ($orderCheck !== true) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $orderCheck);
                }
            }

            // Bắt đầu transaction
            DB::beginTransaction();
            
            // Chuẩn bị dữ liệu cho khu vực
            $areaData = $validatedData;
            // Chuyển đổi string thành integer
            $areaData['is_smoking'] = (int) $validatedData['is_smoking'];
            $areaData['is_vip'] = (int) $validatedData['is_vip'];

            // Cập nhật thông tin cơ bản của khu vực
            $area->fill(array_diff_key($areaData, ['image' => '']));

            // Xử lý ảnh nếu có
            if ($request->hasFile('image')) {
                if ($area->image && Storage::disk('public')->exists($area->image)) {
                    Storage::disk('public')->delete($area->image);
                }
                $path = $request->file('image')->store('areas', 'public');
                $area->image = $path;
            }

            $area->save();
            
            DB::commit();

            return redirect()->route('areas.index')
                ->with('success', 'Khu vực đã được cập nhật thành công!');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Hiển thị chi tiết khu vực với danh sách bàn
    public function show($id)
    {
        try {
            $area = Area::with(['tables', 'hourSetting', 'operatingHours'])->findOrFail($id);
            return view('admin.areas.show', compact('area'));
        } catch (Exception $e) {
            return redirect()->route('areas.index')
                ->with('error', 'Không tìm thấy khu vực yêu cầu.');
        }
    }

    // Xóa khu vực và ảnh
    public function destroy($id)
    {
        try {
            $area = Area::findOrFail($id);

            // Kiểm tra xem có bàn nào trong khu vực này không
            if ($area->tables()->count() > 0) {
                return redirect()->route('areas.index')
                    ->with('error', 'Không thể xóa khu vực này vì có bàn đang được gán vào khu vực.');
            }

            // Kiểm tra thứ tự xóa - không thể xóa khu vực nếu còn khu vực sau nó
            if (!$this->canDeleteArea($area)) {
                return redirect()->route('areas.index')
                    ->with('error', 'Không thể xóa khu vực này vì còn khu vực khác phụ thuộc vào nó. Vui lòng xóa theo thứ tự ngược lại (từ cuối về đầu).');
            }

            // Bắt đầu transaction
            DB::beginTransaction();
            
            // Xóa các khung giờ hoạt động trước
            AreaOperatingHour::where('area_id', $area->area_id)->delete();
            
            // Xóa cài đặt giờ hoạt động
            AreaHourSetting::where('area_id', $area->area_id)->delete();
            
            // Xóa ảnh nếu có
            if ($area->image && Storage::disk('public')->exists($area->image)) {
                Storage::disk('public')->delete($area->image);
            }

            $area->delete();
            
            DB::commit();

            return redirect()->route('areas.index')
                ->with('success', 'Khu vực đã được xóa thành công!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('areas.index')
                ->with('error', 'Có lỗi xảy ra khi xóa khu vực: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra có thể xóa khu vực không
     * Không thể xóa nếu còn khu vực phía sau trong alphabet
     */
    private function canDeleteArea($area)
    {
        // Kiểm tra format code - chỉ chữ cái đơn
        if (!preg_match('/^[A-Z]$/', $area->code)) {
            return true; // Nếu không đúng format thì cho phép xóa
        }

        $areaLetter = $area->code;
        $floor = $area->floor;

        // Lấy tất cả khu vực cùng tầng
        $areasOnSameFloor = Area::where('floor', $floor)
            ->where('area_id', '!=', $area->area_id)
            ->get();

        // Kiểm tra có khu vực nào có chữ cái lớn hơn không
        $alphabet = range('A', 'Z');
        $currentIndex = array_search($areaLetter, $alphabet);

        foreach ($areasOnSameFloor as $otherArea) {
            if (preg_match('/^[A-Z]$/', $otherArea->code)) {
                $otherLetter = $otherArea->code;
                $otherIndex = array_search($otherLetter, $alphabet);
                
                if ($otherIndex > $currentIndex) {
                    return false; // Có khu vực phía sau, không thể xóa
                }
            }
        }

        return true;
    }

    // Quản lý bố trí bàn trong khu vực
    public function manageLayout($id)
    {
        try {
            $area = Area::with('tables')->findOrFail($id);
            $availableTables = Table::whereNull('area_id')->orWhere('area_id', $area->area_id)->get();

            return view('admin.areas.layout', compact('area', 'availableTables'));
        } catch (Exception $e) {
            return redirect()->route('areas.index')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Cập nhật bố trí bàn
    public function updateLayout(Request $request, $id)
    {
        try {
            $request->validate([
                'layout_data' => 'required|json',
                'tables' => 'nullable|array',
                'tables.*' => 'exists:tables,table_id'
            ]);

            $area = Area::findOrFail($id);
            $area->layout_data = $request->layout_data;
            $area->save();

            // Cập nhật bàn cho khu vực
            if ($request->has('tables')) {
                // Đầu tiên reset các bàn hiện tại không thuộc khu vực này nữa
                Table::where('area_id', $area->area_id)
                    ->whereNotIn('table_id', $request->tables)
                    ->update(['area_id' => null]);

                // Cập nhật các bàn thuộc khu vực này
                Table::whereIn('table_id', $request->tables)
                    ->update(['area_id' => $area->area_id]);
            }

            return redirect()->route('areas.show', $area->area_id)
                ->with('success', 'Bố trí khu vực đã được cập nhật thành công!');
        } catch (QueryException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xóa các khu vực không hoạt động và trống
    public function deleteInactiveEmpty()
    {
        try {
            // Lấy danh sách khu vực không hoạt động hoặc ngoài giờ
            $inactiveAreas = Area::all()->filter(function ($area) {
                return $area->current_status != 'Hoạt động';
            });

            // Sắp xếp theo thứ tự ngược (từ cuối về đầu) để xóa đúng thứ tự
            $inactiveAreas = $inactiveAreas->sortByDesc(function ($area) {
                if (preg_match('/^([A-Z])/', $area->code, $matches)) {
                    return $area->floor * 100 + ord($matches[1]);
                }
                return 0;
            });

            $deleted = 0;
            $cannotDelete = [];

            foreach ($inactiveAreas as $area) {
                // Kiểm tra xem khu vực có bàn nào không
                if ($area->tables()->count() > 0) {
                    $cannotDelete[] = $area->name;
                    continue;
                }

                // Kiểm tra có thể xóa không (theo thứ tự)
                if (!$this->canDeleteArea($area)) {
                    $cannotDelete[] = $area->name . ' (cần xóa theo thứ tự)';
                    continue;
                }

                // Bắt đầu transaction
                DB::beginTransaction();
                
                // Xóa các khung giờ hoạt động trước
                AreaOperatingHour::where('area_id', $area->area_id)->delete();
                
                // Xóa cài đặt giờ hoạt động
                AreaHourSetting::where('area_id', $area->area_id)->delete();
                
                // Xóa ảnh nếu có
                if ($area->image && Storage::disk('public')->exists($area->image)) {
                    Storage::disk('public')->delete($area->image);
                }

                $area->delete();
                
                DB::commit();
                $deleted++;
            }

            $message = "Đã xóa $deleted khu vực không hoạt động và trống.";
            if (count($cannotDelete) > 0) {
                $message .= " Không thể xóa " . count($cannotDelete) . " khu vực: " . implode(', ', $cannotDelete);
                return redirect()->route('areas.index')->with('warning', $message);
            }

            return redirect()->route('areas.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('areas.index')
                ->with('error', 'Có lỗi xảy ra khi xóa khu vực: ' . $e->getMessage());
        }
    }

    // Xóa nhiều khu vực cùng lúc
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'area_ids' => 'required|array',
                'area_ids.*' => 'exists:areas,area_id'
            ]);

            $areas = Area::whereIn('area_id', $request->area_ids)->get();
            
            // Sắp xếp theo thứ tự ngược để xóa đúng thứ tự
            $areas = $areas->sortByDesc(function ($area) {
                if (preg_match('/^([A-Z])/', $area->code, $matches)) {
                    return $area->floor * 100 + ord($matches[1]);
                }
                return 0;
            });
            
            $cannotDelete = [];
            $deleted = 0;

            foreach ($areas as $area) {
                // Kiểm tra xem khu vực có bàn nào không
                if ($area->tables()->count() > 0) {
                    $cannotDelete[] = $area->name . ' (có bàn)';
                    continue;
                }

                // Kiểm tra có thể xóa không (theo thứ tự)
                if (!$this->canDeleteArea($area)) {
                    $cannotDelete[] = $area->name . ' (cần xóa theo thứ tự)';
                    continue;
                }

                // Bắt đầu transaction
                DB::beginTransaction();
                
                // Xóa các khung giờ hoạt động trước
                AreaOperatingHour::where('area_id', $area->area_id)->delete();
                
                // Xóa cài đặt giờ hoạt động
                AreaHourSetting::where('area_id', $area->area_id)->delete();
                
                // Xóa ảnh nếu có
                if ($area->image && Storage::disk('public')->exists($area->image)) {
                    Storage::disk('public')->delete($area->image);
                }

                $area->delete();
                
                DB::commit();
                $deleted++;
            }

            $message = "Đã xóa $deleted khu vực.";
            if (count($cannotDelete) > 0) {
                $message .= " Không thể xóa " . count($cannotDelete) . " khu vực: " . implode(', ', $cannotDelete);
                return redirect()->route('areas.index')->with('warning', $message);
            }

            return redirect()->route('areas.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('areas.index')
                ->with('error', 'Có lỗi xảy ra khi xóa khu vực: ' . $e->getMessage());
        }
    }

    /**
     * API để lấy số bàn tiếp theo có thể tạo trong khu vực
     * Đảm bảo thứ tự A1 → A2 → A3, B1 → B2 → B3
     */
    public function getNextAvailableTable($areaId)
    {
        $area = Area::find($areaId);
        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Khu vực không tồn tại'
            ], 404);
        }

        // Lấy tất cả bàn trong khu vực, sắp xếp theo số
        $existingTables = Table::where('area_id', $areaId)
            ->where('table_number', 'LIKE', $area->code . '%')
            ->get()
            ->map(function($table) use ($area) {
                if (preg_match('/^' . $area->code . '(\d+)$/', $table->table_number, $matches)) {
                    return (int)$matches[1];
                }
                return null;
            })
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        // Tìm số tiếp theo theo thứ tự (phải tạo 1 trước 2, 2 trước 3...)
        $nextNumber = 1;
        foreach ($existingTables as $number) {
            if ($number == $nextNumber) {
                $nextNumber++;
            } else {
                break; // Có gap, dừng lại
            }
        }

        return response()->json([
            'success' => true,
            'next_table' => $area->code . $nextNumber,
            'existing_tables' => array_map(function($num) use ($area) {
                return $area->code . $num;
            }, $existingTables),
            'message' => count($existingTables) > 0 
                ? "Khu vực {$area->code} có " . count($existingTables) . " bàn. Tiếp theo: {$area->code}{$nextNumber}"
                : "Khu vực {$area->code} chưa có bàn nào. Bắt đầu với: {$area->code}1"
        ]);
    }

    /**
     * API để validate số bàn có đúng thứ tự không
     */
    public function validateTableNumber(Request $request, $areaId)
    {
        $area = Area::find($areaId);
        if (!$area) {
            return response()->json([
                'valid' => false,
                'message' => 'Khu vực không tồn tại'
            ]);
        }

        $tableNumber = $request->table_number;
        $excludeTableId = $request->table_id; // Khi edit

        // Kiểm tra format
        if (!preg_match('/^' . $area->code . '(\d+)$/', $tableNumber, $matches)) {
            return response()->json([
                'valid' => false,
                'message' => "Số bàn phải có định dạng {$area->code}1, {$area->code}2, {$area->code}3..."
            ]);
        }

        $requestedNumber = (int)$matches[1];

        // Kiểm tra trùng lặp
        $existingTable = Table::where('area_id', $areaId)
            ->where('table_number', $tableNumber)
            ->when($excludeTableId, function($query) use ($excludeTableId) {
                return $query->where('table_id', '!=', $excludeTableId);
            })
            ->first();

        if ($existingTable) {
            return response()->json([
                'valid' => false,
                'message' => "Số bàn {$tableNumber} đã tồn tại trong khu vực"
            ]);
        }

        // Lấy danh sách bàn hiện có
        $existingNumbers = Table::where('area_id', $areaId)
            ->where('table_number', 'LIKE', $area->code . '%')
            ->when($excludeTableId, function($query) use ($excludeTableId) {
                return $query->where('table_id', '!=', $excludeTableId);
            })
            ->get()
            ->map(function($table) use ($area) {
                if (preg_match('/^' . $area->code . '(\d+)$/', $table->table_number, $matches)) {
                    return (int)$matches[1];
                }
                return null;
            })
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        // Kiểm tra thứ tự: chỉ cho phép tạo số tiếp theo
        if (empty($existingNumbers)) {
            // Nếu chưa có bàn nào, chỉ cho phép tạo số 1
            if ($requestedNumber == 1) {
                return response()->json([
                    'valid' => true,
                    'message' => "OK: Bàn đầu tiên trong khu vực {$area->code}"
                ]);
            } else {
                return response()->json([
                    'valid' => false,
                    'message' => "Phải tạo {$area->code}1 trước khi tạo {$tableNumber}"
                ]);
            }
        }

        // Tìm số tiếp theo hợp lệ
        $expectedNext = 1;
        foreach ($existingNumbers as $number) {
            if ($number == $expectedNext) {
                $expectedNext++;
            } else {
                break;
            }
        }

        if ($requestedNumber == $expectedNext) {
            return response()->json([
                'valid' => true,
                'message' => "OK: Số bàn hợp lệ theo thứ tự"
            ]);
        } else if ($requestedNumber < $expectedNext) {
            return response()->json([
                'valid' => false,
                'message' => "Số bàn {$area->code}{$requestedNumber} đã được sử dụng hoặc bỏ qua"
            ]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => "Phải tạo {$area->code}{$expectedNext} trước khi tạo {$tableNumber}"
            ]);
        }
    }
}