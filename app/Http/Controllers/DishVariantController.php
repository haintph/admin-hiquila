<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dish;
use App\Models\DishVariant;

class DishVariantController extends Controller
{
    public function list()
    {
        $dishes = Dish::with('variants')
            ->orderByDesc('updated_at') // Sắp xếp theo updated_at của món ăn
            ->paginate(8);

        return view('admin.variants.list', compact('dishes'));
    }

    /**
     * Thêm một biến thể mới.
     */
    public function create($dish_id)
    {
        $dish = Dish::findOrFail($dish_id);
        return view('admin.variants.create', compact('dish'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dish_id' => 'required|exists:dishes,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'is_available' => 'required|boolean',
        ]);

        DishVariant::create([
            'dish_id' => $request->dish_id,
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'stock' => $request->stock,
            'is_available' => $request->is_available,
        ]);

        return redirect()->route('variant_list')->with('success', 'Thêm biến thể thành công!');
    }

    /**
     * Cập nhật biến thể.
     */
    public function update(Request $request, $id)
    {
        $variant = DishVariant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'stock' => 'required|integer|min:0',
            'is_available' => 'required|boolean',
        ]);

        $variant->update([
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'stock' => $request->stock,
            'is_available' => $request->is_available,
        ]);

        // Cập nhật updated_at của món ăn để thay đổi vị trí trong danh sách
        $variant->dish->touch();

        return redirect()->route('variant_list')->with('success', 'Cập nhật biến thể thành công!');
    }

    /**
     * Xóa biến thể.
     */
    public function destroy($id)
    {
        $variant = DishVariant::findOrFail($id);
        $variant->delete();

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $variant = DishVariant::with('dish')->findOrFail($id);
        $dish = $variant->dish; // Lấy thông tin món ăn
        return view('admin.variants.edit', compact('variant', 'dish'));
    }
}