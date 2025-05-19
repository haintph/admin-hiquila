<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Area;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // Hiển thị danh sách bàn theo khu vực
    public function index(Request $request)
    {
        $area_id = $request->query('area_id');
        $status = $request->query('status');
        $table_type = $request->query('table_type');
        
        $areas = Area::all();
        
        $tables = Table::when($area_id, function ($query) use ($area_id) {
            return $query->where('area_id', $area_id);
        })
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when($table_type, function ($query) use ($table_type) {
            return $query->where('table_type', $table_type);
        })
        ->orderBy('table_number')
        ->paginate(10);

        // Lấy danh sách các loại bàn và trạng thái để hiển thị trong form lọc
        $tableTypes = [
            'Bàn đơn', 'Bàn đôi', 'Bàn 4', 'Bàn 6', 
            'Bàn 8', 'Bàn dài', 'Bàn VIP', 'Bàn tròn'
        ];
        
        $statuses = [
            'Trống', 'Đã đặt', 'Đang phục vụ', 'Đang dọn', 'Bảo trì'
        ];

        return view('admin.tables.index', compact('tables', 'areas', 'area_id', 'status', 'table_type', 'tableTypes', 'statuses'));
    }

    // Hiển thị form thêm bàn
    public function create()
    {
        $areas = Area::all();
        
        // Danh sách các loại bàn và trạng thái cho dropdown
        $tableTypes = [
            'Bàn đơn', 'Bàn đôi', 'Bàn 4', 'Bàn 6', 
            'Bàn 8', 'Bàn dài', 'Bàn VIP', 'Bàn tròn'
        ];
        
        $statuses = [
            'Trống', 'Đã đặt', 'Đang phục vụ', 'Đang dọn', 'Bảo trì'
        ];
        
        return view('admin.tables.create', compact('areas', 'tableTypes', 'statuses'));
    }

    // Xử lý lưu bàn mới
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|max:10|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
            'table_type' => 'required|in:Bàn đơn,Bàn đôi,Bàn 4,Bàn 6,Bàn 8,Bàn dài,Bàn VIP,Bàn tròn',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì',
            'area_id' => 'nullable|exists:areas,area_id',
            'min_spend' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_reservable' => 'boolean',
        ]);

        // Xử lý checkbox is_reservable
        if (!$request->has('is_reservable')) {
            $request->merge(['is_reservable' => false]);
        }

        Table::create($request->all());

        return redirect()->route('tables.index')->with('success', 'Bàn đã được thêm thành công!');
    }

    // Hiển thị form chỉnh sửa bàn
    public function edit($id)
    {
        $table = Table::findOrFail($id);
        $areas = Area::all();
        
        // Danh sách các loại bàn và trạng thái cho dropdown
        $tableTypes = [
            'Bàn đơn', 'Bàn đôi', 'Bàn 4', 'Bàn 6', 
            'Bàn 8', 'Bàn dài', 'Bàn VIP', 'Bàn tròn'
        ];
        
        $statuses = [
            'Trống', 'Đã đặt', 'Đang phục vụ', 'Đang dọn', 'Bảo trì'
        ];
        
        return view('admin.tables.edit', compact('table', 'areas', 'tableTypes', 'statuses'));
    }

    // Xử lý cập nhật bàn
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|max:10|unique:tables,table_number,' . $id . ',table_id',
            'capacity' => 'required|integer|min:1',
            'table_type' => 'required|in:Bàn đơn,Bàn đôi,Bàn 4,Bàn 6,Bàn 8,Bàn dài,Bàn VIP,Bàn tròn',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì',
            'area_id' => 'nullable|exists:areas,area_id',
            'min_spend' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_reservable' => 'boolean',
        ]);

        // Xử lý checkbox is_reservable
        if (!$request->has('is_reservable')) {
            $request->merge(['is_reservable' => false]);
        }

        $table = Table::findOrFail($id);
        $table->update($request->all());

        return redirect()->route('tables.index')->with('success', 'Bàn đã được cập nhật thành công!');
    }

    // Xóa bàn (soft delete)
    public function destroy($id)
    {
        Table::findOrFail($id)->delete();
        return redirect()->route('tables.index')->with('success', 'Bàn đã được xóa!');
    }
    
    // Cập nhật nhanh trạng thái bàn
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ,Đang dọn,Bảo trì',
        ]);
        
        $table = Table::findOrFail($id);
        
        // Nếu đổi từ trạng thái khác sang "Đang phục vụ", cập nhật thời gian bắt đầu
        if ($table->status !== 'Đang phục vụ' && $request->status === 'Đang phục vụ') {
            $table->occupied_at = now();
        }
        
        // Nếu đổi từ "Đang phục vụ" sang "Trống" hoặc "Đang dọn", reset thời gian
        if ($table->status === 'Đang phục vụ' && in_array($request->status, ['Trống', 'Đang dọn'])) {
            $table->occupied_at = null;
            $table->current_order_id = null;
        }
        
        $table->status = $request->status;
        $table->save();
        
        return response()->json(['success' => true, 'message' => 'Trạng thái bàn đã được cập nhật!']);
    }
}