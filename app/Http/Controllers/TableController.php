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
        $areas = Area::all();
        $tables = Table::when($area_id, function ($query) use ($area_id) {
            return $query->where('area_id', $area_id);
        })->paginate(10);

        return view('admin.tables.index', compact('tables', 'areas', 'area_id'));
    }

    // Hiển thị form thêm bàn
    public function create()
    {
        $areas = Area::all();
        return view('admin.tables.create', compact('areas'));
    }

    // Xử lý lưu bàn mới
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|max:10|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ',
            'area_id' => 'nullable|exists:areas,area_id',
        ]);

        Table::create($request->all());

        return redirect()->route('tables.index')->with('success', 'Bàn đã được thêm!');
    }

    // Hiển thị form chỉnh sửa bàn
    public function edit($id)
    {
        $table = Table::findOrFail($id);
        $areas = Area::all();
        return view('admin.tables.edit', compact('table', 'areas'));
    }

    // Xử lý cập nhật bàn
    public function update(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|max:10|unique:tables,table_number,' . $id . ',table_id',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Trống,Đã đặt,Đang phục vụ',
            'area_id' => 'nullable|exists:areas,area_id',
        ]);

        $table = Table::findOrFail($id);
        $table->update($request->all());

        return redirect()->route('tables.index')->with('success', 'Bàn đã được cập nhật!');
    }

    // Xóa bàn
    public function destroy($id)
    {
        Table::findOrFail($id)->delete();
        return redirect()->route('tables.index')->with('success', 'Bàn đã được xóa!');
    }
}
