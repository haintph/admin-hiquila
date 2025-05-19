<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách nguyên liệu để hiển thị trong bộ lọc
        $inventories = Inventory::all();

        // Truy vấn danh sách lịch sử kho với các bộ lọc
        $inventoryLogs = InventoryLog::with(['inventory', 'user']) // Load cả user
            ->when($request->inventory_id, function ($query, $inventory_id) {
                return $query->where('inventory_id', $inventory_id);
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query()); // Giữ lại bộ lọc khi phân trang

        return view('admin.inventory_logs.index', compact('inventoryLogs', 'inventories'));
    }


    public function create()
    {
        $inventories = Inventory::all();
        return view('admin.inventory_logs.add', compact('inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'type' => 'required|in:import,export',
            'quantity' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        // Kiểm tra xem người dùng có đăng nhập không
        if (!auth()->check()) {
            return redirect()->back()->with('error', 'Bạn cần đăng nhập để thực hiện thao tác này.');
        }

        // Tạo bản ghi trong inventory_logs
        InventoryLog::create([
            'inventory_id' => $request->inventory_id,
            'type'        => $request->type,
            'quantity'    => $request->quantity,
            'cost'        => $request->cost,
            'note'        => $request->note,
            'user_id'     => auth()->id(), // Đảm bảo user_id không bị NULL
        ]);

        return redirect()->route('inventory_logs.index')->with('success', 'Thêm giao dịch thành công.');
    }

    public function edit($id)
    {
        $inventoryLog = InventoryLog::findOrFail($id);
        $inventories = Inventory::all();
        return view('admin.inventory_logs.edit', compact('inventoryLog', 'inventories'));
    }

    /**
     * Cập nhật giao dịch kho.
     */
    public function update(Request $request, $id)
    {
        $inventoryLog = InventoryLog::findOrFail($id);
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'type' => 'required|in:import,export',
            'quantity' => 'required|numeric|min:0.01',
            'cost' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        // Hoàn tác thay đổi trước đó
        if ($inventoryLog->type === 'import') {
            $inventory->decrement('quantity', $inventoryLog->quantity);
        } else {
            $inventory->increment('quantity', $inventoryLog->quantity);
        }

        // Kiểm tra nếu xuất kho làm tồn kho dưới mức tối thiểu
        if ($request->type === 'export' && ($inventory->quantity - $request->quantity < $inventory->min_quantity)) {
            return redirect()->back()->withErrors(['quantity' => 'Không thể xuất kho vì tồn kho thấp hơn mức tối thiểu!'])->withInput();
        }

        // Cập nhật dữ liệu mới
        $inventoryLog->update([
            'inventory_id' => $request->inventory_id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'cost' => $request->cost,
            'note' => $request->note,
        ]);

        // Cập nhật lại số lượng tồn kho
        if ($request->type === 'import') {
            $inventory->increment('quantity', $request->quantity);
        } else {
            $inventory->decrement('quantity', $request->quantity);
        }

        return redirect()->route('inventory_logs.index')->with('success', 'Giao dịch kho được cập nhật thành công.');
    }

    /**
     * Xóa giao dịch kho.
     */
    public function destroy($id)
    {
        $inventoryLog = InventoryLog::findOrFail($id);
        $inventory = Inventory::findOrFail($inventoryLog->inventory_id);

        // Hoàn tác thay đổi số lượng trong kho
        if ($inventoryLog->type === 'import') {
            $inventory->decrement('quantity', $inventoryLog->quantity);
        } else {
            $inventory->increment('quantity', $inventoryLog->quantity);
        }

        $inventoryLog->delete();

        return redirect()->route('inventory_logs.index')->with('success', 'Giao dịch kho đã bị xóa.');
    }
}
