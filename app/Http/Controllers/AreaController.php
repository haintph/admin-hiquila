<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AreaController extends Controller
{
    // Hiển thị danh sách khu vực
    public function index()
    {
        $areas = Area::paginate(10); // 10 khu vực mỗi trang
        return view('admin.areas.index', compact('areas'));
    }

    // Hiển thị form tạo khu vực
    public function create()
    {
        return view('admin.areas.create');
    }

    // Xử lý lưu khu vực mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
            'capacity' => 'nullable|integer',
            'floor' => 'nullable|integer',
            'is_smoking' => 'boolean',
            'is_vip' => 'boolean',
            'surcharge' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $area = new Area($request->all());

        // Xử lý ảnh nếu có
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('areas', 'public');
            $area->image = $path;
        }

        $area->save();

        return redirect()->route('areas.index')->with('success', 'Khu vực đã được thêm!');
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('admin.areas.edit', compact('area'));
    }

    // Xử lý cập nhật khu vực
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
            'capacity' => 'nullable|integer',
            'floor' => 'nullable|integer',
            'is_smoking' => 'boolean',
            'is_vip' => 'boolean',
            'surcharge' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $area = Area::findOrFail($id);
        $area->fill($request->except('image')); // Chỉ cập nhật dữ liệu khác, không ghi đè ảnh nếu không có ảnh mới

        // Xóa ảnh cũ nếu có ảnh mới
        if ($request->hasFile('image')) {
            if ($area->image && Storage::disk('public')->exists($area->image)) {
                Storage::disk('public')->delete($area->image);
            }

            // Lưu ảnh mới
            $path = $request->file('image')->store('areas', 'public');
            $area->image = $path;
        }

        $area->save();

        return redirect()->route('areas.index')->with('success', 'Khu vực đã được cập nhật!');
    }


    // Xóa khu vực và ảnh
    public function destroy($id)
    {
        $area = Area::findOrFail($id);

        // Xóa ảnh nếu có
        if ($area->image) {
            Storage::disk('public')->delete($area->image);
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Khu vực đã được xóa!');
    }
}
