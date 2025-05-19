<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Table;
use App\Models\Dish;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DishVariant;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Hiển thị danh sách hóa đơn.
     */
    public function index()
    {
        $invoices = Invoice::with('table')->paginate(10);

        // Tính toán số lượng hóa đơn theo trạng thái
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::where('status', 'Đang chuẩn bị')->count();
        $paidInvoices = Invoice::where('status', 'Đã thanh toán')->count();

        return view('admin.invoices.index', compact('invoices', 'totalInvoices', 'pendingInvoices', 'paidInvoices'));
    }

    /**
     * Hiển thị form tạo hóa đơn mới.
     */
    public function create()
    {
        $areas = Area::with(['tables' => function ($query) {
            $query->where('status', 'Trống');
        }])->get();

        $dishes = Dish::all();

        return view('admin.invoices.create', compact('areas', 'dishes'));
    }

    /**
     * Lấy danh sách bàn theo khu vực.
     */
    public function getTables($area_id)
    {
        $tables = Table::where('area_id', $area_id)
            ->where('status', 'Trống')
            ->get();
        return response()->json($tables);
    }

    /**
     * Lưu hóa đơn mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,table_id',
        ]);

        // Kiểm tra xem bàn có trống không trước khi tạo hóa đơn
        $table = Table::find($request->table_id);
        if (!$table || $table->status !== 'Trống') {
            return redirect()->back()->with('error', 'Bàn đã được đặt hoặc không tồn tại!');
        }

        // Tạo hóa đơn mới
        $invoice = Invoice::create([
            'table_id' => $request->table_id,
            'total_price' => 0,
            'status' => 'Đang chuẩn bị',
        ]);

        // Cập nhật trạng thái bàn thành "Đang sử dụng"
        Table::where('table_id', $request->table_id)->update(['status' => 'Đang phục vụ']);

        return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã được tạo!');
    }
    /**
     * Đồng bộ số lượng trong giỏ hàng với tồn kho thực tế
     */
    public function syncCart($invoice_id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($invoice_id);
            $updated = false;

            foreach ($invoice->items as $item) {
                // Lấy tồn kho thực tế
                $stock = 0;
                if ($item->variant_id) {
                    $variant = DishVariant::find($item->variant_id);
                    $stock = $variant ? $variant->stock : 0;
                } else {
                    $dish = Dish::find($item->dish_id);
                    $stock = $dish ? $dish->stock : 0;
                }

                // Lấy số lượng đã đặt từ các đơn khác
                $otherOrdered = InvoiceDetail::where('dish_id', $item->dish_id)
                    ->where('detail_id', '!=', $item->detail_id);

                if ($item->variant_id) {
                    $otherOrdered->where('variant_id', $item->variant_id);
                } else {
                    $otherOrdered->whereNull('variant_id');
                }

                $otherQty = $otherOrdered->sum('quantity');
                $available = max(0, $stock - $otherQty);

                // Nếu số lượng hiện tại vượt quá available, cập nhật lại
                if ($item->quantity > $available) {
                    $item->quantity = $available;
                    $item->save();
                    $updated = true;
                }

                // Xóa các mục có số lượng 0
                if ($item->quantity <= 0) {
                    $item->delete();
                    $updated = true;
                }
            }

            // Cập nhật tổng tiền
            if ($updated) {
                $totalPrice = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->sum(DB::raw('quantity * price'));
                $invoice->update(['total_price' => $totalPrice]);
            }

            DB::commit();
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("[Lỗi Đồng bộ] " . $e->getMessage());
            return false;
        }
    }
    /**
     * Hiển thị trang đặt món.
     */
    public function edit(Request $request, $id)
    {
        // Kiểm tra và đồng bộ giỏ hàng
        $synced = $this->syncCart($id);

        if ($synced) {
            \Illuminate\Support\Facades\Log::info("Đã đồng bộ giỏ hàng #$id");
        }
        // Lấy dữ liệu hóa đơn và món ăn
        $invoice = Invoice::with(['items.dish', 'items.variant', 'table'])->findOrFail($id);
        $categories = Category::where('is_active', 1)->get();
        $subcategories = collect();

        // Tính toán số lượng khả dụng cho từng item trong giỏ hàng
        foreach ($invoice->items as $item) {
            // Lấy thông tin món ăn hoặc biến thể
            if ($item->variant_id) {
                $variant = $item->variant;
                $maxStock = $variant ? $variant->stock : 0;
            } else {
                $dish = $item->dish;
                $maxStock = $dish ? $dish->stock : 0;
            }

            // Tính tổng số lượng đã đặt (trừ chính nó)
            $othersOrdered = InvoiceDetail::where('dish_id', $item->dish_id);

            if ($item->variant_id) {
                $othersOrdered->where('variant_id', $item->variant_id);
            } else {
                $othersOrdered->whereNull('variant_id');
            }

            // Loại trừ chính item này
            $othersOrdered->where('detail_id', '!=', $item->detail_id);

            $othersQty = $othersOrdered->sum('quantity');

            // Số lượng tối đa còn có thể đặt
            $item->availableToOrder = max(0, $maxStock - $othersQty);

            // Số lượng hiện tại không thể vượt quá tổng tồn kho
            $maxAllowed = $othersQty + $item->quantity;
            if ($maxAllowed > $maxStock) {
                \Illuminate\Support\Facades\Log::warning("[Cảnh báo] Item ID: {$item->detail_id} vượt quá tồn kho ($maxAllowed > $maxStock)");
            }
        }

        // Xử lý chi tiết món ăn nếu được chọn
        if ($request->has('dish')) {
            $selected_dish = Dish::with('variants')->findOrFail($request->dish);

            // Tính số lượng có thể đặt thêm cho món chính
            $dishOrdered = InvoiceDetail::where('dish_id', $selected_dish->id)
                ->whereNull('variant_id')
                ->sum('quantity');

            $selected_dish->available_stock = max(0, $selected_dish->stock - $dishOrdered);

            // Tính cho các biến thể
            foreach ($selected_dish->variants as $variant) {
                $variantOrdered = InvoiceDetail::where('dish_id', $selected_dish->id)
                    ->where('variant_id', $variant->id)
                    ->sum('quantity');

                $variant->available_stock = max(0, $variant->stock - $variantOrdered);
            }

            return view('admin.invoices.order', compact('invoice', 'selected_dish', 'categories'));
        }

        // Xử lý danh sách món ăn
        $dishesQuery = Dish::where('is_available', 1);

        // Lọc theo danh mục
        if ($request->has('category')) {
            $category_id = $request->category;

            // Lấy danh mục con
            $subcategories = SubCategory::where('parent_id', $category_id)->get();

            if ($request->has('subcategory')) {
                $dishesQuery->where('sub_category_id', $request->subcategory);
            } elseif ($subcategories->count() > 0) {
                $dishesQuery->whereIn('sub_category_id', $subcategories->pluck('id'));
            } else {
                $dishesQuery->whereHas('subCategory', function ($q) use ($category_id) {
                    $q->where('parent_id', $category_id);
                });
            }
        }

        // Tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $dishesQuery->where('name', 'like', "%{$request->search}%");
        }

        // Lấy danh sách món
        $dishes = $dishesQuery->with(['variants', 'subCategory'])->get();

        // Tính số lượng khả dụng cho mỗi món
        foreach ($dishes as $dish) {
            // Số lượng đã đặt
            $dishOrdered = InvoiceDetail::where('dish_id', $dish->id)
                ->whereNull('variant_id')
                ->sum('quantity');

            $dish->available_stock = max(0, $dish->stock - $dishOrdered);

            // Tính cho biến thể
            foreach ($dish->variants as $variant) {
                $variantOrdered = InvoiceDetail::where('dish_id', $dish->id)
                    ->where('variant_id', $variant->id)
                    ->sum('quantity');

                $variant->available_stock = max(0, $variant->stock - $variantOrdered);
            }
        }

        return view('admin.invoices.order', compact('invoice', 'categories', 'dishes', 'subcategories'));
    }


    /**
     * Thêm/Cập nhật/Xóa món ăn vào hóa đơn.
     */
    public function addDish(Request $request, $invoice_id)
    {
        // Bắt đầu transaction và lock để tránh race condition
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($invoice_id);
            $dish_id = $request->dish_id;
            $quantity = (int)$request->quantity;
            $variant_id = $request->variant_id;
            $is_add_more = $request->has('is_add_more') && $request->is_add_more == 1;

            // Thêm log cho request
            \Illuminate\Support\Facades\Log::info("Request: dish_id=$dish_id, quantity=$quantity, variant_id=" . ($variant_id ?? 'null') . ", action=" . $request->input('action') . ", original_quantity=" . $request->input('original_quantity'));

            // Nếu quantity = 0, xóa món khỏi hóa đơn
            if ($quantity == 0) {
                $query = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->where('dish_id', $dish_id);

                // Nếu có variant_id, phải khớp cả variant_id
                if ($variant_id) {
                    $query->where('variant_id', $variant_id);
                } else {
                    $query->whereNull('variant_id');
                }

                $item = $query->first();

                if ($item) {
                    // Xóa món khỏi hóa đơn
                    $item->delete();
                    \Illuminate\Support\Facades\Log::info("Đã xóa mục: detail_id=" . $item->detail_id);
                }
            } else {
                // Lấy thông tin món ăn
                $dish = Dish::findOrFail($dish_id);

                // Xác định stock cần kiểm tra (món ăn hoặc biến thể)
                $checkStock = $dish->stock;
                $variant = null;

                if ($variant_id) {
                    $variant = DishVariant::findOrFail($variant_id);
                    $checkStock = $variant->stock;
                }

                // Lấy mục hiện có trong giỏ (nếu có) với FOR UPDATE để lock
                $existingItem = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->where('dish_id', $dish_id);

                if ($variant_id) {
                    $existingItem->where('variant_id', $variant_id);
                } else {
                    $existingItem->whereNull('variant_id');
                }

                // Lock để tránh race condition
                $existingItem = $existingItem->lockForUpdate()->first();
                $currentQuantity = $existingItem ? $existingItem->quantity : 0;

                // Tính số lượng đã đặt của cùng món này (trừ mục hiện tại)
                $otherOrderedQuery = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->where('dish_id', $dish_id);

                if ($variant_id) {
                    $otherOrderedQuery->where('variant_id', $variant_id);
                } else {
                    $otherOrderedQuery->whereNull('variant_id');
                }

                if ($existingItem) {
                    $otherOrderedQuery->where('detail_id', '!=', $existingItem->detail_id);
                }

                $otherOrdered = $otherOrderedQuery->sum('quantity');

                // Số lượng còn lại có thể đặt
                $availableStock = max(0, $checkStock - $otherOrdered);

                \Illuminate\Support\Facades\Log::info("Stock: dish_id=$dish_id, stock=$checkStock, other_ordered=$otherOrdered, available=$availableStock, current=$currentQuantity");

                // Xác định số lượng mới dựa vào action
                $action = $request->input('action');
                $originalQuantity = (int)$request->input('original_quantity', 0);

                if ($action === 'increase' && $originalQuantity > 0) {
                    // Chỉ tăng 1 đơn vị nếu còn stock
                    if ($availableStock > 0) {
                        $newQuantity = $originalQuantity + 1;
                        \Illuminate\Support\Facades\Log::info("Tăng số lượng từ $originalQuantity lên $newQuantity");
                    } else {
                        $newQuantity = $originalQuantity;
                        \Illuminate\Support\Facades\Log::warning("Không thể tăng: Hết hàng");
                    }
                } else if ($action === 'decrease' && $originalQuantity > 0) {
                    // Chỉ giảm 1 đơn vị, không giảm xuống dưới 1
                    $newQuantity = max(1, $originalQuantity - 1);
                    \Illuminate\Support\Facades\Log::info("Giảm số lượng từ $originalQuantity xuống $newQuantity");
                } else if ($is_add_more) {
                    // Thêm mới vào giỏ hàng, cộng dồn số lượng
                    $newQuantity = $currentQuantity + $quantity;

                    // Kiểm tra không vượt quá stock
                    if ($newQuantity > $currentQuantity + $availableStock) {
                        $newQuantity = $currentQuantity + $availableStock;
                        \Illuminate\Support\Facades\Log::warning("Chỉ có thể thêm tối đa: $availableStock");
                    }

                    \Illuminate\Support\Facades\Log::info("Thêm mới: $currentQuantity + $quantity = $newQuantity");
                } else {
                    // Cập nhật số lượng trực tiếp, đảm bảo không vượt quá stock
                    $maxPossible = $currentQuantity + $availableStock;
                    if ($quantity > $maxPossible) {
                        $newQuantity = $maxPossible;
                        \Illuminate\Support\Facades\Log::warning("Số lượng vượt quá giới hạn: $quantity > $maxPossible");
                    } else {
                        $newQuantity = $quantity;
                    }

                    \Illuminate\Support\Facades\Log::info("Cập nhật trực tiếp: $currentQuantity -> $newQuantity");
                }

                // Tính toán giá cuối cùng (sử dụng giá biến thể nếu có)
                $price = $dish->price;
                if ($variant) {
                    $price = $variant->price;
                }

                if ($existingItem) {
                    // Cập nhật số lượng món
                    $existingItem->quantity = $newQuantity;
                    $existingItem->save();
                    \Illuminate\Support\Facades\Log::info("Đã cập nhật mục: detail_id={$existingItem->detail_id}, quantity=$newQuantity");
                } else {
                    // Thêm món mới vào hóa đơn
                    $newItem = InvoiceDetail::create([
                        'invoice_id' => $invoice_id,
                        'dish_id' => $dish_id,
                        'variant_id' => $variant_id,
                        'quantity' => $newQuantity,
                        'price' => $price,
                    ]);
                    \Illuminate\Support\Facades\Log::info("Đã thêm mục mới: detail_id={$newItem->detail_id}, quantity=$newQuantity");
                }
            }

            // Cập nhật tổng tiền hóa đơn
            $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
                ->sum(DB::raw('quantity * price'));
            $invoice->update(['total_price' => $total_price]);

            // Commit transaction
            DB::commit();

            // Thông báo tùy thuộc vào hành động
            $action = $request->input('action');
            if ($action === 'increase' && $quantity > 0) {
                return redirect()->back()->with('success', 'Đã tăng số lượng!');
            } else if ($action === 'decrease' && $quantity > 0) {
                return redirect()->back()->with('success', 'Đã giảm số lượng!');
            } else if ($quantity === 0) {
                return redirect()->back()->with('success', 'Đã xóa món khỏi giỏ!');
            } else {
                return redirect()->back()->with('success', 'Cập nhật giỏ hàng thành công!');
            }
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Lỗi cập nhật giỏ hàng: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Thêm món ăn kèm biến thể vào hóa đơn.
     */
    public function addDishWithVariant(Request $request, $invoice_id)
    {
        // Chuyển hướng đến phương thức addDish, giữ nguyên các tham số
        return $this->addDish($request, $invoice_id);
    }

    /**
     * Hiển thị trang thanh toán.
     */
    public function payment($id)
    {
        $invoice = Invoice::with(['items.dish', 'items.variant', 'table'])->findOrFail($id);
        return view('admin.invoices.payment', compact('invoice'));
    }

    /**
     * Xử lý thanh toán tiền mặt.
     */
    public function checkout($id)
    {
        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($id);

            // Cập nhật số lượng tồn kho khi thanh toán
            foreach ($invoice->items as $item) {
                if ($item->variant_id) {
                    // Giảm stock của biến thể
                    $variant = DishVariant::findOrFail($item->variant_id);
                    $variant->stock = max(0, $variant->stock - $item->quantity);
                    $variant->save();
                } else {
                    // Giảm stock của món ăn
                    $dish = Dish::findOrFail($item->dish_id);
                    $dish->stock = max(0, $dish->stock - $item->quantity);
                    $dish->save();
                }
            }

            // Cập nhật trạng thái hóa đơn và bàn
            $invoice->update(['status' => 'Đã thanh toán']);
            Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

            DB::commit();

            return redirect()->route('invoices.index')->with('success', 'Thanh toán thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thanh toán: ' . $e->getMessage());
        }
    }



    /**
     * In hóa đơn.
     */
    public function print($id)
    {
        $invoice = Invoice::with('items.dish', 'items.variant', 'table')->findOrFail($id);
        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * Xóa hóa đơn.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Cập nhật trạng thái bàn về "Trống" nếu hóa đơn đang phục vụ
        Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

        // Xóa hóa đơn
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã bị xóa!');
    }

    /**
     * Xác nhận thanh toán.
     */
    public function confirmPayment($id)
    {
        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($id);

            // Cập nhật số lượng tồn kho khi thanh toán
            foreach ($invoice->items as $item) {
                if ($item->variant_id) {
                    // Giảm stock của biến thể
                    $variant = DishVariant::findOrFail($item->variant_id);
                    $variant->stock = max(0, $variant->stock - $item->quantity);
                    $variant->save();
                } else {
                    // Giảm stock của món ăn
                    $dish = Dish::findOrFail($item->dish_id);
                    $dish->stock = max(0, $dish->stock - $item->quantity);
                    $dish->save();
                }
            }

            // Cập nhật trạng thái hóa đơn
            $invoice->update(['status' => 'Đã thanh toán']);

            // Cập nhật trạng thái bàn ăn
            Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

            DB::commit();

            return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã được xác nhận thanh toán!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xác nhận thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán.
     */
    public function checkPayment($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice && $invoice->status == 'Đã thanh toán') {
            return response()->json(['paid' => true]);
        }
        return response()->json(['paid' => false]);
    }
    /**
     * Tăng số lượng món ăn trong giỏ hàng
     */
    public function increaseItem(Request $request, $invoice_id)
    {
        DB::beginTransaction();

        try {
            $detail_id = $request->input('detail_id');
            $item = InvoiceDetail::findOrFail($detail_id);

            // Đảm bảo item thuộc về hóa đơn hiện tại
            if ($item->invoice_id != $invoice_id) {
                return redirect()->back()->with('error', 'Dữ liệu không hợp lệ');
            }

            $dish_id = $item->dish_id;
            $variant_id = $item->variant_id;
            $currentQty = $item->quantity;

            // Lấy số lượng tồn kho
            if ($variant_id) {
                $variant = DishVariant::findOrFail($variant_id);
                $totalStock = $variant->stock;
            } else {
                $dish = Dish::findOrFail($dish_id);
                $totalStock = $dish->stock;
            }

            // Tính tổng số lượng đã đặt của cùng món này trong tất cả đơn hàng
            $totalOrdered = InvoiceDetail::where('dish_id', $dish_id);

            if ($variant_id) {
                $totalOrdered->where('variant_id', $variant_id);
            } else {
                $totalOrdered->whereNull('variant_id');
            }

            // Loại trừ item hiện tại
            $totalOrdered->where('detail_id', '!=', $detail_id);

            $orderedQty = $totalOrdered->sum('quantity');

            // Số lượng còn lại có thể đặt
            $remainingStock = max(0, $totalStock - $orderedQty);

            \Illuminate\Support\Facades\Log::info("[Tăng] ID: $detail_id, Món: $dish_id, Hiện tại: $currentQty, Tổng tồn: $totalStock, Đã đặt: $orderedQty, Còn lại: $remainingStock");

            // Kiểm tra xem còn có thể tăng không
            if ($remainingStock > 0) {
                // Cập nhật số lượng mới
                $item->quantity = $currentQty + 1;
                $item->save();

                // Cập nhật tổng tiền
                $invoice = Invoice::findOrFail($invoice_id);
                $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->sum(DB::raw('quantity * price'));
                $invoice->update(['total_price' => $total_price]);

                DB::commit();
                return redirect()->back()->with('success', 'Đã tăng số lượng');
            } else {
                DB::commit();
                return redirect()->back()->with('warning', 'Đã đạt số lượng tồn kho tối đa');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("[Lỗi Tăng] " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    /**
     * Giảm số lượng món ăn trong giỏ hàng
     */
    public function decreaseItem(Request $request, $invoice_id)
    {
        DB::beginTransaction();

        try {
            $detail_id = $request->input('detail_id');
            $item = InvoiceDetail::findOrFail($detail_id);

            // Đảm bảo item thuộc về hóa đơn hiện tại
            if ($item->invoice_id != $invoice_id) {
                return redirect()->back()->with('error', 'Dữ liệu không hợp lệ');
            }

            $currentQty = $item->quantity;

            // Log để debug
            \Illuminate\Support\Facades\Log::info("[Giảm] ID: $detail_id, Hiện tại: $currentQty");

            if ($currentQty > 1) {
                $item->quantity = $currentQty - 1;
                $item->save();

                // Kiểm tra lại sau khi lưu
                $item = $item->fresh();
                \Illuminate\Support\Facades\Log::info("[Giảm] Sau khi lưu: " . $item->quantity);

                // Cập nhật tổng tiền
                $invoice = Invoice::findOrFail($invoice_id);
                $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->sum(DB::raw('quantity * price'));
                $invoice->update(['total_price' => $total_price]);

                DB::commit();
                return redirect()->back()->with('success', 'Đã giảm số lượng');
            } else {
                DB::commit();
                return redirect()->back()->with('info', 'Số lượng tối thiểu là 1');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("[Lỗi Giảm] " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa món ăn khỏi giỏ hàng
     */
    /**
     * Xóa món ăn khỏi giỏ hàng
     */
    public function removeItem($invoice_id, $item_id)
    {
        DB::beginTransaction();

        try {
            $item = InvoiceDetail::findOrFail($item_id);

            // Đảm bảo item thuộc về hóa đơn hiện tại
            if ($item->invoice_id != $invoice_id) {
                return redirect()->back()->with('error', 'Dữ liệu không hợp lệ');
            }

            // Log trước khi xóa
            \Illuminate\Support\Facades\Log::info("[Xóa] ID: $item_id, Món: {$item->dish_id}");

            // Xóa item
            $item->delete();

            // Cập nhật tổng tiền
            $invoice = Invoice::findOrFail($invoice_id);
            $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
                ->sum(DB::raw('quantity * price'));
            $invoice->update(['total_price' => $total_price]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã xóa món khỏi giỏ hàng');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("[Lỗi Xóa] " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
