<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    // Hiển thị danh sách nguyên liệu
    public function index()
    {
        $inventories = Inventory::orderBy('id', 'desc')->paginate(10);
        return view('admin.inventories.index', compact('inventories'));
    }

    // Hiển thị form thêm nguyên liệu
    public function create()
    {
        return view('admin.inventories.add');
    }

    // Xử lý thêm nguyên liệu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Thêm nguyên liệu thành công!');
    }

    // Hiển thị form sửa nguyên liệu
    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        return view('admin.inventories.edit', compact('inventory'));
    }

    // Xử lý cập nhật nguyên liệu
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Cập nhật nguyên liệu thành công!');
    }

    // Xóa nguyên liệu
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Xóa nguyên liệu thành công!');
    }
}
