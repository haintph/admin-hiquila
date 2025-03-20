<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('admin.', compact('areas'));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
            'capacity' => 'nullable|integer',
            'floor' => 'nullable|integer',
            'surcharge' => 'nullable|numeric',
            'image' => 'nullable|string',
        ]);

        Area::create($request->all());
        return redirect()->route('areas.index')->with('success', 'Khu vực đã được thêm.');
    }

    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:Hoạt động,Bảo trì,Đóng cửa',
            'capacity' => 'nullable|integer',
            'floor' => 'nullable|integer',
            'surcharge' => 'nullable|numeric',
            'image' => 'nullable|string',
        ]);

        $area->update($request->all());
        return redirect()->route('areas.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('areas.index')->with('success', 'Xóa thành công.');
    }
}
