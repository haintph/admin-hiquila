<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaHourSetting;
use App\Models\AreaOperatingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaOperatingHourController extends Controller
{
    /**
     * Hiển thị form quản lý giờ hoạt động của khu vực
     */
    public function manageHours(Area $area)
    {
        // Tải quan hệ
        $area->load(['hourSetting', 'operatingHours' => function($query) {
            $query->orderBy('display_order')->orderBy('start_time');
        }]);
        
        return view('admin.areas.manage-hours', compact('area'));
    }
    
    /**
     * Thêm khung giờ hoạt động mới (action riêng biệt)
     */
    public function addTimeSlot(Area $area)
    {
        try {
            // Kiểm tra cài đặt giờ
            if (!$area->hourSetting) {
                // Tạo mới cài đặt giờ nếu chưa có
                AreaHourSetting::create([
                    'area_id' => $area->area_id,
                    'has_operating_hours' => true,
                    'non_operating_status' => 'Đóng cửa'
                ]);
            } else if (!$area->hourSetting->has_operating_hours) {
                // Cập nhật nếu đã có nhưng chưa bật
                $area->hourSetting->update(['has_operating_hours' => true]);
            }
            
            // Đếm số khung giờ hiện tại để tạo thứ tự mới
            $count = AreaOperatingHour::where('area_id', $area->area_id)->count();
            
            // Thêm khung giờ mới
            AreaOperatingHour::create([
                'area_id' => $area->area_id,
                'start_time' => '08:00',
                'end_time' => '22:00',
                'is_active' => true,
                'display_order' => $count
            ]);
            
            return redirect()->route('areas.manageHours', $area->area_id)
                ->with('success', 'Đã thêm khung giờ mới.');
        } catch (\Exception $e) {
            Log::error('Error adding time slot: ' . $e->getMessage());
            
            return redirect()->route('areas.manageHours', $area->area_id)
                ->with('error', 'Lỗi khi thêm khung giờ: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa một khung giờ hoạt động (action riêng biệt)
     */
    public function removeTimeSlot(Area $area, $timeSlotId)
    {
        try {
            // Kiểm tra nếu đây là khung giờ cuối cùng
            $count = AreaOperatingHour::where('area_id', $area->area_id)->count();
            
            if ($count <= 1) {
                return redirect()->route('areas.manageHours', $area->area_id)
                    ->with('error', 'Không thể xóa khung giờ cuối cùng.');
            }
            
            // Xóa khung giờ
            AreaOperatingHour::where('id', $timeSlotId)
                ->where('area_id', $area->area_id)
                ->delete();
            
            // Cập nhật lại thứ tự hiển thị
            $timeSlots = AreaOperatingHour::where('area_id', $area->area_id)
                ->orderBy('start_time')
                ->get();
            
            foreach ($timeSlots as $index => $slot) {
                $slot->update(['display_order' => $index]);
            }
            
            return redirect()->route('areas.manageHours', $area->area_id)
                ->with('success', 'Đã xóa khung giờ.');
        } catch (\Exception $e) {
            Log::error('Error removing time slot: ' . $e->getMessage());
            
            return redirect()->route('areas.manageHours', $area->area_id)
                ->with('error', 'Lỗi khi xóa khung giờ: ' . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật giờ hoạt động của khu vực
     */
    public function updateHours(Request $request, Area $area)
    {
        try {
            DB::beginTransaction();
            
            // Cập nhật hoặc tạo mới cài đặt giờ hoạt động
            $hasOperatingHours = $request->has('has_operating_hours');
            $nonOperatingStatus = $request->input('non_operating_status', 'Đóng cửa');
            
            $hourSetting = AreaHourSetting::updateOrCreate(
                ['area_id' => $area->area_id],
                [
                    'has_operating_hours' => $hasOperatingHours,
                    'non_operating_status' => $nonOperatingStatus
                ]
            );
            
            // Cập nhật các khung giờ hoạt động
            if ($hasOperatingHours && $request->has('operating_hours')) {
                $operatingHours = $request->input('operating_hours', []);
                
                // Xóa tất cả khung giờ hiện tại
                AreaOperatingHour::where('area_id', $area->area_id)->delete();
                
                // Thêm lại các khung giờ đã chỉnh sửa
                foreach ($operatingHours as $index => $timeSlot) {
                    // Bỏ qua các khung giờ không hợp lệ
                    if (empty($timeSlot['start_time']) || empty($timeSlot['end_time'])) {
                        continue;
                    }
                    
                    AreaOperatingHour::create([
                        'area_id' => $area->area_id,
                        'start_time' => $timeSlot['start_time'],
                        'end_time' => $timeSlot['end_time'],
                        'is_active' => true,
                        'display_order' => $index
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('areas.index')
                ->with('success', 'Đã cập nhật giờ hoạt động thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating operating hours: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Lỗi khi cập nhật giờ hoạt động: ' . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật trạng thái khu vực dựa trên giờ hoạt động
     */
    public function updateAreaStatuses()
    {
        try {
            // Lấy tất cả khu vực có giờ hoạt động
            $areas = Area::with(['hourSetting', 'operatingHours'])
                ->whereHas('hourSetting', function($query) {
                    $query->where('has_operating_hours', true);
                })
                ->get();
            
            $updatedCount = 0;
            $activeAreas = 0;
            $inactiveAreas = 0;
            
            foreach ($areas as $area) {
                // Kiểm tra xem có đang trong giờ hoạt động không
                $isInOperatingHours = $area->isWithinOperatingHours();
                
                // Cập nhật trạng thái nếu cần
                if (!$isInOperatingHours && $area->status != $area->hourSetting->non_operating_status) {
                    // Cập nhật trạng thái ngoài giờ hoạt động
                    $area->setAttribute('status', $area->hourSetting->non_operating_status);
                    $area->save();
                    $updatedCount++;
                    $inactiveAreas++;
                } else if ($isInOperatingHours && $area->status == $area->hourSetting->non_operating_status) {
                    // Nếu đang trong giờ hoạt động nhưng trạng thái vẫn là ngoài giờ, cập nhật về Hoạt động
                    $area->setAttribute('status', 'Hoạt động');
                    $area->save();
                    $updatedCount++;
                    $activeAreas++;
                } else {
                    // Đếm các khu vực không cần cập nhật
                    if ($isInOperatingHours) {
                        $activeAreas++;
                    } else {
                        $inactiveAreas++;
                    }
                }
            }
            
            $total = $activeAreas + $inactiveAreas;
            
            if ($total > 0) {
                return redirect()->route('areas.index')
                    ->with('success', "Đã cập nhật trạng thái của {$updatedCount} khu vực. Hiện có {$activeAreas} khu vực đang hoạt động, {$inactiveAreas} khu vực ngoài giờ hoạt động.");
            } else {
                return redirect()->route('areas.index')
                    ->with('info', 'Không có khu vực nào có khung giờ hoạt động riêng.');
            }
        } catch (\Exception $e) {
            Log::error('Error updating area statuses: ' . $e->getMessage());
            
            return redirect()->route('areas.index')
                ->with('error', 'Lỗi khi cập nhật trạng thái khu vực: ' . $e->getMessage());
        }
    }
}