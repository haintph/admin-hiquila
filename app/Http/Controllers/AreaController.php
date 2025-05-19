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

class AreaController extends Controller
{
    // Hiển thị danh sách khu vực
    public function index()
    {
        try {
            // Tải khu vực với số bàn và thông tin giờ hoạt động
            $areas = Area::withCount('tables')
                ->with(['hourSetting', 'operatingHours'])
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

    // Xử lý lưu khu vực mới
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|max:10|unique:areas',
                'name' => 'required|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
                'capacity' => 'nullable|integer',
                'floor' => 'nullable|integer',
                'is_smoking' => 'boolean',
                'is_vip' => 'boolean',
                'surcharge' => 'nullable|numeric|min:0',
                'image' => 'nullable|image|max:2048',
                'layout_data' => 'nullable|json'
            ]);

            // Bắt đầu transaction để lưu khu vực
            DB::beginTransaction();
            
            // Chuẩn bị dữ liệu cho khu vực
            $areaData = $validatedData;
            $areaData['is_smoking'] = $request->has('is_smoking') ? 1 : 0;
            $areaData['is_vip'] = $request->has('is_vip') ? 1 : 0;

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
                'code' => 'required|max:10|unique:areas,code,' . $area->area_id . ',area_id',
                'name' => 'required|max:100',
                'description' => 'nullable|string',
                'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
                'capacity' => 'nullable|integer',
                'floor' => 'nullable|integer',
                'is_smoking' => 'boolean',
                'is_vip' => 'boolean',
                'surcharge' => 'nullable|numeric|min:0',
                'image' => 'nullable|image|max:2048',
                'layout_data' => 'nullable|json'
            ]);

            // Bắt đầu transaction
            DB::beginTransaction();
            
            // Chuẩn bị dữ liệu cho khu vực
            $areaData = $validatedData;
            $areaData['is_smoking'] = $request->has('is_smoking') ? 1 : 0;
            $areaData['is_vip'] = $request->has('is_vip') ? 1 : 0;

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

            $deleted = 0;
            $cannotDelete = [];

            foreach ($inactiveAreas as $area) {
                // Kiểm tra xem khu vực có bàn nào không
                if ($area->tables()->count() > 0) {
                    $cannotDelete[] = $area->name;
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
                $message .= " Không thể xóa " . count($cannotDelete) . " khu vực có bàn: " . implode(', ', $cannotDelete);
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
            $cannotDelete = [];
            $deleted = 0;

            foreach ($areas as $area) {
                // Kiểm tra xem khu vực có bàn nào không
                if ($area->tables()->count() > 0) {
                    $cannotDelete[] = $area->name;
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
                $message .= " Không thể xóa " . count($cannotDelete) . " khu vực có bàn: " . implode(', ', $cannotDelete);
                return redirect()->route('areas.index')->with('warning', $message);
            }

            return redirect()->route('areas.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('areas.index')
                ->with('error', 'Có lỗi xảy ra khi xóa khu vực: ' . $e->getMessage());
        }
    }
}