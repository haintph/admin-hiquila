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
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Hiển thị danh sách hóa đơn.
     */
    public function index()
    {
        // Lấy hóa đơn mới nhất (sắp xếp theo created_at giảm dần)
        $invoices = Invoice::with('table')
            ->orderBy('created_at', 'desc') // Sắp xếp theo ngày tạo mới nhất
            ->paginate(10);

        // Tính toán số lượng hóa đơn theo trạng thái
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::whereIn('status', ['Đang chuẩn bị', 'Đã phục vụ'])->count();
        $paidInvoices = Invoice::where('status', 'Đã thanh toán')->count();
        $completedInvoices = Invoice::where('status', 'Hoàn thành')->count();

        return view('admin.invoices.index', compact(
            'invoices',
            'totalInvoices',
            'pendingInvoices',
            'paidInvoices',
            'completedInvoices'
        ));
    }

    /**
     * Hiển thị form tạo hóa đơn mới.
     */
    public function create(Request $request)
    {
        // Lấy tất cả các tầng từ bảng areas
        $floors = Area::select('floor')
            ->whereNotNull('floor')
            ->distinct()
            ->orderBy('floor')
            ->get()
            ->pluck('floor');

        // Lấy tầng được chọn, mặc định là tầng đầu tiên
        $selectedFloor = $request->query('floor', $floors->first());

        // Lấy khu vực theo tầng
        $areas = Area::when($selectedFloor !== null, function ($query) use ($selectedFloor) {
            return $query->where('floor', $selectedFloor);
        })
            ->orderBy('name')
            ->get();

        // Lấy khu vực được chọn
        $area_id = $request->query('area_id');

        // Nếu không có khu vực nào được chọn, chọn khu vực đầu tiên
        if (!$area_id && $areas->count() > 0) {
            $area_id = $areas->first()->area_id;
        }

        // Lấy danh sách bàn trống thuộc khu vực
        $tables = [];
        if ($area_id) {
            $tables = Table::where('area_id', $area_id)
                ->where('status', 'Trống')
                ->orderBy('table_number')
                ->get();
        }

        // Thông tin bảng màu hiển thị các loại bàn
        $tableTypes = [
            'Bàn đơn' => ['color' => '#FF9999', 'icon' => 'fa-circle', 'shape' => 'circle', 'size' => 'small'],
            'Bàn đôi' => ['color' => '#99CCFF', 'icon' => 'fa-circle', 'shape' => 'circle', 'size' => 'small'],
            'Bàn 4' => ['color' => '#99FF99', 'icon' => 'fa-square', 'shape' => 'square', 'size' => 'medium'],
            'Bàn 6' => ['color' => '#FFCC99', 'icon' => 'fa-square', 'shape' => 'square', 'size' => 'medium'],
            'Bàn 8' => ['color' => '#CC99FF', 'icon' => 'fa-square', 'shape' => 'square', 'size' => 'large'],
            'Bàn dài' => ['color' => '#FFFF99', 'icon' => 'fa-rectangle-wide', 'shape' => 'rectangle', 'size' => 'large'],
            'Bàn VIP' => ['color' => '#FFD700', 'icon' => 'fa-star', 'shape' => 'square', 'size' => 'large'],
            'Bàn tròn' => ['color' => '#C0C0C0', 'icon' => 'fa-circle', 'shape' => 'circle', 'size' => 'large'],
        ];

        return view('admin.invoices.create', compact('floors', 'selectedFloor', 'areas', 'tables', 'area_id', 'tableTypes'));
    }

    /**
     * Lấy danh sách bàn theo khu vực (method này không còn cần thiết với approach mới).
     * Giữ lại để tương thích với code cũ.
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
            'party_size' => 'required|integer|min:1|max:20',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20|regex:/^[0-9]{10,11}$/',
            'special_notes' => 'nullable|string|max:1000',
        ], [
            'table_id.required' => 'Vui lòng chọn bàn',
            'party_size.required' => 'Vui lòng nhập số lượng khách',
            'party_size.min' => 'Số lượng khách tối thiểu là 1 người',
            'party_size.max' => 'Số lượng khách tối đa là 20 người',
            'customer_phone.regex' => 'Số điện thoại phải có 10-11 chữ số',
        ]);

        DB::beginTransaction();

        try {
            // Kiểm tra bàn
            $table = Table::find($request->table_id);
            if (!$table || !in_array($table->status, ['Trống', 'Đã đặt'])) {
                return redirect()->back()->withInput()
                    ->with('error', 'Bàn không khả dụng!');
            }

            // Kiểm tra sức chứa
            if ($request->party_size > $table->capacity) {
                return redirect()->back()->withInput()
                    ->with('error', "Bàn chỉ có sức chứa {$table->capacity} người!");
            }

            // Lấy thông tin khách hàng (ưu tiên từ reservation nếu có)
            $customerName = $request->customer_name;
            $customerPhone = $request->customer_phone;
            $partySize = $request->party_size;
            $specialNotes = $request->special_notes;

            // Nếu bàn có reservation, lấy thông tin từ đó
            if ($table->status === 'Đã đặt') {
                $customerName = $table->reserved_by ?? $request->customer_name;
                $customerPhone = $table->reserved_phone ?? $request->customer_phone;
                $partySize = $table->reserved_party_size ?? $request->party_size;
                $specialNotes = $table->reservation_notes ?? $request->special_notes;
            }

            // Tạo hóa đơn với thông tin khách hàng
            $invoice = Invoice::create([
                'table_id' => $request->table_id,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'party_size' => $partySize,
                'special_notes' => $specialNotes,
                'total_price' => 0,
                'status' => 'Đang chuẩn bị',
            ]);

            // Cập nhật bàn - GIỮ NGUYÊN thông tin reservation
            $table->update([
                'status' => 'Đang phục vụ',
                'current_order_id' => $invoice->invoice_id,
                'occupied_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('invoices.edit', $invoice->invoice_id)
                ->with('success', "Hóa đơn cho bàn {$table->table_number} đã được tạo!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo hóa đơn: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra!');
        }
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
                // Tất cả món ăn (kể cả biến thể) đều sử dụng stock của dish gốc
                $dish = Dish::find($item->dish_id);
                $stock = $dish ? $dish->stock : 0;

                // Tính tổng số lượng đã đặt của món này (bao gồm cả biến thể)
                $otherOrdered = InvoiceDetail::where('dish_id', $item->dish_id)
                    ->where('detail_id', '!=', $item->detail_id)
                    ->sum('quantity');

                $available = max(0, $stock - $otherOrdered);

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

        // Phân chia items thành đã gửi bếp và chưa gửi bếp
        $sentItems = collect();
        $pendingItems = collect();

        foreach ($invoice->items as $item) {
            // Tất cả món ăn (kể cả biến thể) đều sử dụng stock của dish gốc
            $dish = $item->dish;
            $maxStock = $dish ? $dish->stock : 0;

            // Tính tổng số lượng đã đặt của món này (bao gồm cả biến thể, trừ chính nó)
            $othersOrdered = InvoiceDetail::where('dish_id', $item->dish_id)
                ->where('detail_id', '!=', $item->detail_id)
                ->sum('quantity');

            // Số lượng tối đa còn có thể đặt
            $item->availableToOrder = max(0, $maxStock - $othersOrdered);

            // Phân loại item dựa trên sent_to_kitchen_at
            if ($item->sent_to_kitchen_at) {
                $sentItems->push($item);
            } else {
                $pendingItems->push($item);
            }
        }

        // Gán lại vào invoice để sử dụng trong view
        $invoice->sentItems = $sentItems;
        $invoice->pendingItems = $pendingItems;

        // Xử lý chi tiết món ăn nếu được chọn
        if ($request->has('dish')) {
            $selected_dish = Dish::with('variants')->findOrFail($request->dish);

            // Tính số lượng đã đặt của món này (bao gồm cả biến thể)
            $totalOrdered = InvoiceDetail::where('dish_id', $selected_dish->id)
                ->sum('quantity');

            $selected_dish->available_stock = max(0, $selected_dish->stock - $totalOrdered);
            $selected_dish->total_stock = $selected_dish->stock;
            $selected_dish->total_available_stock = $selected_dish->available_stock;

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
            // Tính tổng số lượng đã đặt của món này (bao gồm cả biến thể)
            $totalOrdered = InvoiceDetail::where('dish_id', $dish->id)
                ->sum('quantity');

            // Số lượng còn có thể đặt
            $dish->available_stock = max(0, $dish->stock - $totalOrdered);
            $dish->total_stock = $dish->stock;
            $dish->total_available_stock = $dish->available_stock;
        }

        return view('admin.invoices.order', compact('invoice', 'categories', 'dishes', 'subcategories'));
    }


    /**
     * Thêm/Cập nhật/Xóa món ăn vào hóa đơn.
     * Cải thiện để hỗ trợ order thêm món sau khi đã gửi bếp
     */
    public function addDish(Request $request, $invoice_id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($invoice_id);

            $dish_id = $request->dish_id;
            $quantity = (int)$request->quantity;
            $variant_id = $request->variant_id;
            $is_add_more = $request->has('is_add_more') && $request->is_add_more == 1;

            // Log request
            Log::info("Add Dish Request", [
                'dish_id' => $dish_id,
                'quantity' => $quantity,
                'variant_id' => $variant_id,
                'is_add_more' => $is_add_more,
                'action' => $request->input('action')
            ]);

            // Nếu quantity = 0, xóa món khỏi hóa đơn
            if ($quantity == 0) {
                $query = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->where('dish_id', $dish_id)
                    ->whereNull('sent_to_kitchen_at'); // Chỉ xóa món chưa gửi bếp

                if ($variant_id) {
                    $query->where('variant_id', $variant_id);
                } else {
                    $query->whereNull('variant_id');
                }

                $item = $query->first();
                if ($item) {
                    $item->delete();
                    Log::info("Deleted pending item: {$item->detail_id}");
                }
            } else {
                // Lấy thông tin món ăn
                $dish = Dish::findOrFail($dish_id);
                $variant = $variant_id ? DishVariant::findOrFail($variant_id) : null;

                // Tính stock khả dụng
                $totalStock = $dish->stock;
                $otherOrdered = InvoiceDetail::where('dish_id', $dish_id)->sum('quantity');
                $availableStock = max(0, $totalStock - $otherOrdered);

                // Tìm món trong pending items (chưa gửi bếp)
                $existingPendingItem = InvoiceDetail::where('invoice_id', $invoice_id)
                    ->where('dish_id', $dish_id)
                    ->whereNull('sent_to_kitchen_at'); // Chỉ tìm món chưa gửi bếp

                if ($variant_id) {
                    $existingPendingItem->where('variant_id', $variant_id);
                } else {
                    $existingPendingItem->whereNull('variant_id');
                }

                $existingPendingItem = $existingPendingItem->lockForUpdate()->first();
                $currentQuantity = $existingPendingItem ? $existingPendingItem->quantity : 0;

                // Xác định số lượng mới
                $action = $request->input('action');
                $originalQuantity = (int)$request->input('original_quantity', 0);

                if ($action === 'increase' && $originalQuantity > 0) {
                    if ($availableStock > 0) {
                        $newQuantity = $originalQuantity + 1;
                    } else {
                        throw new \Exception('Không đủ hàng để tăng số lượng');
                    }
                } else if ($action === 'decrease' && $originalQuantity > 0) {
                    $newQuantity = max(1, $originalQuantity - 1);
                } else if ($is_add_more) {
                    // Luôn tạo mới hoặc cộng dồn vào pending items
                    $newQuantity = $currentQuantity + $quantity;

                    if ($newQuantity > $currentQuantity + $availableStock) {
                        $newQuantity = $currentQuantity + $availableStock;
                        if ($newQuantity <= $currentQuantity) {
                            throw new \Exception('Không đủ hàng để thêm món');
                        }
                    }
                } else {
                    $maxPossible = $currentQuantity + $availableStock;
                    $newQuantity = min($quantity, $maxPossible);
                }

                // Tính giá (ưu tiên giá variant)
                $price = $variant ? $variant->price : $dish->price;

                if ($existingPendingItem) {
                    // Cập nhật món pending hiện có
                    $existingPendingItem->quantity = $newQuantity;
                    $existingPendingItem->save();
                    Log::info("Updated pending item: {$existingPendingItem->detail_id}, quantity: $newQuantity");
                } else {
                    // Tạo món mới trong pending
                    $newItem = InvoiceDetail::create([
                        'invoice_id' => $invoice_id,
                        'dish_id' => $dish_id,
                        'variant_id' => $variant_id,
                        'quantity' => $newQuantity,
                        'price' => $price,
                        'sent_to_kitchen_at' => null, // Luôn tạo mới với trạng thái pending
                    ]);
                    Log::info("Created new pending item: {$newItem->detail_id}, quantity: $newQuantity");
                }
            }

            // Cập nhật tổng tiền
            $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
                ->sum(DB::raw('quantity * price'));
            $invoice->update(['total_price' => $total_price]);

            DB::commit();

            // Thông báo thành công
            $action = $request->input('action');
            if ($action === 'increase') {
                return redirect()->back()->with('success', 'Đã tăng số lượng món!');
            } else if ($action === 'decrease') {
                return redirect()->back()->with('success', 'Đã giảm số lượng món!');
            } else if ($quantity === 0) {
                return redirect()->back()->with('success', 'Đã xóa món khỏi giỏ!');
            } else {
                return redirect()->back()->with('success', 'Đã thêm món vào giỏ hàng!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Dish Error: ' . $e->getMessage());
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

        // Kiểm tra điều kiện thanh toán
        if (!in_array($invoice->status, ['Đã phục vụ', 'Đã thanh toán'])) {
            return redirect()->route('invoices.edit', $id)
                ->with('error', 'Hóa đơn cần được gửi bếp trước khi thanh toán!');
        }

        if ($invoice->total_price <= 0) {
            return redirect()->route('invoices.edit', $id)
                ->with('error', 'Hóa đơn chưa có món nào để thanh toán!');
        }

        // Nếu đã thanh toán → hiển thị trang success với nút In/Hoàn tất
        if ($invoice->status === 'Đã thanh toán') {
            return view('admin.invoices.index', compact('invoice'));
        }

        // Nếu chưa thanh toán → hiển thị form thanh toán
        return view('admin.invoices.payment', compact('invoice'));
    }

    /**
     * Xử lý thanh toán tiền mặt.
     */
    public function checkout($id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($id);

            // Cập nhật stock
            foreach ($invoice->items as $item) {
                $dish = Dish::findOrFail($item->dish_id);
                $dish->stock = max(0, $dish->stock - $item->quantity);
                $dish->save();
            }

            // Thanh toán
            $invoice->update([
                'status' => 'Đã thanh toán',
                'payment_method' => 'cash',
                'paid_at' => now()
            ]);

            // CẬP NHẬT BÀN - GIỮ NGUYÊN thông tin để có thể in
            Table::where('table_id', $invoice->table_id)->update([
                'status' => 'Đã thanh toán',
            ]);

            DB::commit();

            return redirect()->route('invoices.index', $invoice->invoice_id)
                ->with('success', 'Thanh toán tiền mặt thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thanh toán!');
        }
    }

    /**
     * In hóa đơn.
     */
    public function print($id)
    {
        $invoice = Invoice::with(['items.dish', 'items.variant', 'table'])->findOrFail($id);

        if (!in_array($invoice->status, ['Đã thanh toán', 'Hoàn thành'])) {
            return redirect()->back()->with('error', 'Chỉ có thể in hóa đơn đã thanh toán!');
        }

        return view('admin.invoices.print', compact('invoice'));
    }


    /**
     * Xóa hóa đơn.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($id);

            // Reset bàn về trạng thái trống
            Table::where('table_id', $invoice->table_id)->update([
                'status' => 'Trống',
                'current_order_id' => null,
                'occupied_at' => null,
                'reserved_by' => null,
                'reserved_phone' => null,
                'reserved_time' => null,
                'reserved_party_size' => null,
                'reservation_notes' => null,
                'reserved_at' => null,
            ]);

            $invoice->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Đã xóa hóa đơn và reset bàn!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }

    /**
     * Xác nhận thanh toán.
     */
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qr,vnpay,paypal'
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($id);

            // Cập nhật stock
            foreach ($invoice->items as $item) {
                $dish = Dish::findOrFail($item->dish_id);
                $dish->stock = max(0, $dish->stock - $item->quantity);
                $dish->save();
            }

            // Thanh toán
            $invoice->update([
                'status' => 'Đã thanh toán',
                'payment_method' => $request->payment_method,
                'paid_at' => now()
            ]);

            // CẬP NHẬT BÀN - GIỮ NGUYÊN thông tin
            Table::where('table_id', $invoice->table_id)->update([
                'status' => 'Đã thanh toán',
            ]);

            DB::commit();

            $methodNames = [
                'cash' => 'tiền mặt',
                'transfer' => 'chuyển khoản',
                'qr' => 'QR Code',
                'vnpay' => 'VNPAY',
                'paypal' => 'PayPal'
            ];

            $methodName = $methodNames[$request->payment_method];

            return redirect()->route('invoices.payment', $invoice->invoice_id)
                ->with('success', "Thanh toán bằng {$methodName} thành công!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
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

            // Kiểm tra nếu item đã gửi bếp thì không cho phép sửa
            if ($item->sent_to_kitchen_at) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không thể thay đổi món đã gửi bếp!');
            }

            $dish_id = $item->dish_id;
            $currentQty = $item->quantity;

            // Lấy stock của dish gốc
            $dish = Dish::findOrFail($dish_id);
            $totalStock = $dish->stock;

            // Tính tổng số lượng đã đặt của món này (bao gồm cả biến thể, trừ item hiện tại)
            $totalOrdered = InvoiceDetail::where('dish_id', $dish_id)
                ->where('detail_id', '!=', $detail_id)
                ->sum('quantity');

            // Số lượng còn lại có thể đặt
            $remainingStock = max(0, $totalStock - $totalOrdered);

            \Illuminate\Support\Facades\Log::info("[Tăng] ID: $detail_id, Món: $dish_id, Hiện tại: $currentQty, Tổng tồn: $totalStock, Đã đặt: $totalOrdered, Còn lại: $remainingStock");

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

            // Kiểm tra nếu item đã gửi bếp thì không cho phép sửa
            if ($item->sent_to_kitchen_at) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không thể thay đổi món đã gửi bếp!');
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
    public function removeItem($invoice_id, $item_id)
    {
        DB::beginTransaction();

        try {
            $item = InvoiceDetail::findOrFail($item_id);

            // Đảm bảo item thuộc về hóa đơn hiện tại
            if ($item->invoice_id != $invoice_id) {
                return redirect()->back()->with('error', 'Dữ liệu không hợp lệ');
            }

            // Kiểm tra nếu item đã gửi bếp thì không cho phép xóa
            if ($item->sent_to_kitchen_at) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không thể xóa món đã gửi bếp!');
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


    /**
     * Gửi đơn hàng đến đầu bếp.
     */
    public function sendToKitchen($invoice_id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->findOrFail($invoice_id);

            // Lấy các món chưa gửi bếp
            $pendingItems = $invoice->items->whereNull('sent_to_kitchen_at');

            // Kiểm tra xem có món nào chưa gửi bếp không
            if ($pendingItems->isEmpty()) {
                return redirect()->back()->with('error', 'Không có món mới nào để gửi đến bếp.');
            }

            $now = now();

            // Cập nhật sent_to_kitchen_at cho chỉ những món chưa gửi bếp
            foreach ($pendingItems as $item) {
                $item->update(['sent_to_kitchen_at' => $now]);
            }

            // Cập nhật sent_to_kitchen_at của invoice chỉ khi có món mới được gửi
            // Nếu đây là lần đầu gửi bếp hoặc có món mới
            if (!$invoice->sent_to_kitchen_at) {
                $invoice->update(['sent_to_kitchen_at' => $now]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Đã gửi ' . $pendingItems->count() . ' món mới đến bếp thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Lỗi khi gửi đơn hàng đến bếp: " . $e->getMessage());

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi đơn hàng: ' . $e->getMessage());
        }
    }
    public function finishAndCleanTable($id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($id);

            if ($invoice->status !== 'Đã thanh toán') {
                return redirect()->back()->with('error', 'Hóa đơn chưa được thanh toán!');
            }

            // Cập nhật hóa đơn thành "Hoàn thành"
            $invoice->update(['status' => 'Hoàn thành']);

            // RESET BÀN về trạng thái trống
            Table::where('table_id', $invoice->table_id)->update([
                'status' => 'Trống',
                'current_order_id' => null,
                'occupied_at' => null,

                // XÓA thông tin đặt bàn
                'reserved_by' => null,
                'reserved_phone' => null,
                'reserved_time' => null,
                'reserved_party_size' => null,
                'reservation_notes' => null,
                'reserved_at' => null,
            ]);

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Đã hoàn tất hóa đơn và dọn bàn thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi hoàn tất hóa đơn: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra!');
        }
    }
    public function quickClean($id)
    {
        return $this->finishAndCleanTable($id);
    }
}
