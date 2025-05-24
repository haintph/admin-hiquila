@extends('admin.layouts.master')

@section('styles')
    <style>
        .dish-card {
            transition: all 0.3s;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }

        .dish-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .dish-image {
            height: 140px;
            object-fit: cover;
            width: 100%;
        }

        .dish-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .table-info {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .order-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .category-badge {
            transition: all 0.2s;
        }

        .dish-search {
            border-radius: 30px;
            padding-left: 20px;
        }

        .cart-item {
            border-left: 3px solid transparent;
        }

        .cart-item:hover {
            border-left-color: #4b6cb7;
            background-color: rgba(75, 108, 183, 0.05);
        }

        .cart-quantity {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .empty-cart {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #aaa;
        }

        .category-nav {
            overflow-x: auto;
            white-space: nowrap;
            flex-wrap: nowrap;
            padding: 10px 0;
        }

        .order-complete-btn {
            border-radius: 30px;
            padding: 12px 25px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .quantity-control {
            display: flex;
            align-items: center;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
        }

        .subcategory-item {
            min-width: 120px;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.2s;
        }

        .subcategory-item.active,
        .subcategory-item:hover {
            background-color: #4b6cb7;
            color: white;
        }

        .subcategory-item img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 5px;
        }

        .variant-option {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .variant-option:hover,
        .variant-option.selected {
            background-color: #e9ecef;
        }

        .variant-option.selected {
            border-left: 3px solid #4b6cb7;
        }

        .variant-name {
            font-weight: 500;
        }

        .variant-price {
            color: #dc3545;
            font-weight: 500;
        }

        .dish-detail-card {
            border-radius: 15px;
            overflow: hidden;
        }

        .dish-detail-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .back-to-menu {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Back button to invoices.index -->
        <div class="mb-3">
            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>
        <div class="row">
            <!-- Phần Menu Món ăn hoặc Chi tiết món ăn -->
            <div class="col-lg-8">
                @if (isset($selected_dish))
                    <!-- Chi tiết món ăn và biến thể -->
                    <div class="card shadow-sm mb-4 dish-detail-card">
                        <div class="position-relative">
                            <a href="{{ route('staff.edit', $invoice->invoice_id) }}" class="back-to-menu">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <img width="100"
                                src="{{ $selected_dish->image ? asset('storage/' . $selected_dish->image) : 'https://via.placeholder.com/800x400?text=' . urlencode($selected_dish->name) }}"
                                class="dish-detail-image" alt="{{ $selected_dish->name }}">
                        </div>
                        <div class="card-body">
                            <h4 class="mb-2">{{ $selected_dish->name }}</h4>
                            @if ($selected_dish->description)
                                <p class="text-muted mb-3">{{ $selected_dish->description }}</p>
                            @endif

                            @php
                                // Tính tổng tồn kho bao gồm món ăn gốc và tất cả biến thể
                                $totalStock = $selected_dish->stock;
                                foreach ($selected_dish->variants as $variant) {
                                    $totalStock += $variant->stock;
                                }
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span
                                    class="text-danger fs-5 fw-bold">{{ number_format($selected_dish->price, 0, ',', '.') }}
                                    VND</span>
                                <span
                                    class="badge bg-{{ $selected_dish->total_available_stock > 0 ? 'success' : 'danger' }}">
                                    Còn {{ $selected_dish->total_available_stock }}
                                </span>
                            </div>

                            @if ($selected_dish->variants->count() > 0)
                                <h5 class="mt-4 mb-3">Tùy chọn thêm</h5>
                                <form action="{{ route('staff.addDishWithVariant', $invoice->invoice_id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $selected_dish->id }}">
                                    <input type="hidden" name="is_add_more" value="1">

                                    <div class="mb-4">
                                        <!-- Option gốc luôn hiển thị nếu còn available_stock -->
                                        @if (isset($selected_dish->available_stock) && $selected_dish->available_stock > 0)
                                            <div class="form-check variant-option">
                                                <input class="form-check-input" type="radio" name="variant_id"
                                                    id="variant-original" value="" checked>
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="variant-original">
                                                    <span class="variant-name">{{ $selected_dish->name }} (Gốc)</span>
                                                    <span
                                                        class="variant-price">{{ number_format($selected_dish->price, 0, ',', '.') }}
                                                        VND</span>
                                                </label>
                                                <small class="text-muted d-block ms-4">
                                                    Có thể đặt thêm: {{ $selected_dish->available_stock }}
                                                </small>
                                            </div>
                                        @endif

                                        <!-- Chỉ hiển thị các biến thể còn stock -->
                                        @foreach ($selected_dish->variants as $variant)
                                            @if (isset($variant->available_stock) && $variant->available_stock > 0)
                                                <div class="form-check variant-option">
                                                    <input class="form-check-input" type="radio" name="variant_id"
                                                        id="variant{{ $variant->id }}" value="{{ $variant->id }}"
                                                        {{ !isset($selected_dish->available_stock) || $selected_dish->available_stock <= 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label d-flex justify-content-between w-100"
                                                        for="variant{{ $variant->id }}">
                                                        <span class="variant-name">{{ $variant->name }}</span>
                                                        <span
                                                            class="variant-price">{{ number_format($variant->price, 0, ',', '.') }}
                                                            VND</span>
                                                    </label>
                                                    <small class="text-muted d-block ms-4">
                                                        Có thể đặt thêm: {{ $variant->available_stock }}
                                                    </small>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- Chỉ hiển thị nút thêm vào giỏ nếu có ít nhất một tùy chọn khả dụng -->
                                    @if (
                                        (isset($selected_dish->available_stock) && $selected_dish->available_stock > 0) ||
                                            $selected_dish->variants->where('available_stock', '>', 0)->count() > 0)
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="quantity-control">
                                                <span class="me-2">Số lượng:</span>
                                                <input type="number" name="quantity" class="form-control quantity-input"
                                                    value="1" min="1"
                                                    max="{{ isset($selected_dish->available_stock)
                                                        ? $selected_dish->available_stock
                                                        : ($selected_dish->variants->where('available_stock', '>', 0)->first()
                                                            ? $selected_dish->variants->where('available_stock', '>', 0)->first()->available_stock
                                                            : 1) }}">
                                            </div>

                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                            </button>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            Món ăn này hiện đã hết. Vui lòng chọn món khác.
                                        </div>
                                    @endif
                                </form>
                            @else
                                <form action="{{ route('staff.addDish', $invoice->invoice_id) }}" method="POST"
                                    class="mt-4">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $selected_dish->id }}">
                                    <input type="hidden" name="is_add_more" value="1">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="quantity-control">
                                            <span class="me-2">Số lượng:</span>
                                            <input type="number" name="quantity" class="form-control quantity-input"
                                                value="1" min="1"
                                                max="{{ isset($selected_dish->available_stock) ? $selected_dish->available_stock : $selected_dish->stock }}">
                                        </div>

                                        <button type="submit" class="btn btn-primary"
                                            {{ $selected_dish->available_stock <= 0 || !$selected_dish->is_available ? 'disabled' : '' }}>
                                            <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Danh sách món ăn -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0"><i class="fas fa-utensils text-primary me-2"></i>Đặt món</h4>

                                <div class="d-flex align-items-center">
                                    <!-- Tìm kiếm món ăn sử dụng form -->
                                    <form action="{{ route('staff.edit', $invoice->invoice_id) }}" method="GET"
                                        class="me-2">
                                        <div class="position-relative">
                                            <input type="text" name="search" class="form-control dish-search"
                                                placeholder="Tìm kiếm món ăn..." value="{{ request('search') }}">
                                            <button type="submit" class="btn position-absolute"
                                                style="right: 0; top: 0;">
                                                <i class="fas fa-search" style="color: #aaa;"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Danh mục món ăn -->
                            <div class="category-nav d-flex mb-4">
                                <a href="{{ route('staff.edit', $invoice->invoice_id) }}" class="text-decoration-none">
                                    <span
                                        class="category-badge badge {{ !request('category') ? 'bg-primary' : 'bg-light text-dark' }} me-2 px-3 py-2">
                                        Tất cả
                                    </span>
                                </a>
                                @if (isset($categories) && count($categories) > 0)
                                    @foreach ($categories as $category)
                                        <a href="{{ route('staff.edit', ['id' => $invoice->invoice_id, 'category' => $category->id]) }}"
                                            class="text-decoration-none">
                                            <span
                                                class="category-badge badge {{ request('category') == $category->id ? 'bg-primary' : 'bg-light text-dark' }} me-2 px-3 py-2">
                                                {{ $category->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Danh mục con -->
                            @if (request('category') && isset($subcategories) && count($subcategories) > 0)
                                <div class="d-flex flex-wrap mb-4 subcategory-container">
                                    <a href="{{ route('staff.edit', ['id' => $invoice->invoice_id, 'category' => request('category')]) }}"
                                        class="text-decoration-none me-2 mb-2">
                                        <div
                                            class="subcategory-item {{ !request('subcategory') ? 'active' : '' }} px-3 py-2 rounded text-center">
                                            <i class="fas fa-border-all"></i>
                                            <div>Tất cả</div>
                                        </div>
                                    </a>
                                    @foreach ($subcategories as $subcategory)
                                        <a href="{{ route('staff.edit', ['id' => $invoice->invoice_id, 'category' => request('category'), 'subcategory' => $subcategory->id]) }}"
                                            class="text-decoration-none me-2 mb-2">
                                            <div class="subcategory-item {{ request('subcategory') == $subcategory->id ? 'active' : '' }} px-3 py-2 rounded text-center"
                                                style="min-width: 120px;">
                                                @if ($subcategory->img_subcate)
                                                    <img src="{{ asset('storage/' . $subcategory->img_subcate) }}"
                                                        alt="{{ $subcategory->name_sub }}" class="mb-2"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                @else
                                                    <i class="fas fa-utensils mb-2"></i>
                                                @endif
                                                <div>{{ $subcategory->name_sub }}</div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Danh sách món ăn -->
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">
                                @foreach ($dishes as $dish)
                                    @php
                                        // Tính tổng tồn kho bao gồm món ăn gốc và tất cả biến thể
                                        $totalStock = $dish->stock;
                                        foreach ($dish->variants as $variant) {
                                            $totalStock += $variant->stock;
                                        }
                                    @endphp
                                    <div class="col">
                                        <div class="card dish-card h-100">
                                            @if ($dish->stock <= 5 && $dish->stock > 0)
                                                <span class="dish-badge badge bg-warning">Sắp hết</span>
                                            @elseif($dish->stock == 0)
                                                <span class="dish-badge badge bg-danger">Hết hàng</span>
                                            @endif

                                            <img src="{{ $dish->image ? asset('storage/' . $dish->image) : 'https://via.placeholder.com/300x200?text=' . urlencode($dish->name) }}"
                                                class="dish-image" alt="{{ $dish->name }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $dish->name }}</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="text-danger fw-bold">{{ number_format($dish->price, 0, ',', '.') }}
                                                        VND</span>
                                                    <span
                                                        class="badge bg-{{ $dish->total_available_stock > 0 ? 'success' : 'danger' }}">
                                                        Còn {{ $dish->total_available_stock }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top-0">
                                                @if ($dish->available_stock > 0 && $dish->is_available)
                                                    @if ($dish->variants->count() > 0)
                                                        <a href="{{ route('staff.edit', ['id' => $invoice->invoice_id, 'dish' => $dish->id]) }}"
                                                            class="btn btn-primary w-100">
                                                            <i class="fas fa-list-ul me-1"></i> Chọn tùy chọn
                                                        </a>
                                                    @else
                                                        <form action="{{ route('staff.addDish', $invoice->invoice_id) }}"
                                                            method="POST"
                                                            class="d-flex justify-content-between align-items-center">
                                                            @csrf
                                                            <input type="hidden" name="dish_id"
                                                                value="{{ $dish->id }}">
                                                            <input type="hidden" name="is_add_more" value="1">
                                                            <div class="quantity-control">
                                                                <input type="number" name="quantity"
                                                                    class="quantity-input" value="1" min="1"
                                                                    max="{{ $dish->available_stock }}">
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-cart-plus me-1"></i> Thêm
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <button class="btn btn-sm btn-secondary w-100" disabled>
                                                        <i class="fas fa-ban me-1"></i> Không khả dụng
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Phần Giỏ hàng & Thông tin bàn -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 20px;">
                    <!-- Thông tin bàn -->
                    <div class="card table-info shadow-sm mb-4">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0"><i class="fas fa-chair me-2"></i>Bàn
                                        {{ $invoice->table->table_number }}</h5>
                                    <small class="text-white-50">Mã hóa đơn: #{{ $invoice->invoice_id }}</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0">Giờ vào:
                                        {{ \Carbon\Carbon::parse($invoice->created_at)->format('H:i') }}</h5>
                                    <small
                                        class="text-white-50">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Giỏ hàng -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary me-2"></i>Danh sách đã đặt</h5>
                        </div>
                        <div class="card-body p-0">
                            <!-- Danh sách món đã đặt -->
                            <div class="order-list">
                                @if (count($invoice->items) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">Món ăn</th>
                                                    <th class="py-2 text-center" width="80">SL</th>
                                                    <th class="py-2 text-end">Thành tiền</th>
                                                    <th class="py-2" width="40"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($invoice->items as $item)
                                                    @php
                                                        // Lấy số lượng tồn kho hiện tại của món ăn hoặc biến thể
                                                        $maxStock = $item->variant_id
                                                            ? ($item->variant
                                                                ? $item->variant->stock
                                                                : 0)
                                                            : ($item->dish
                                                                ? $item->dish->stock
                                                                : 0);

                                                        // Tính số lượng đã đặt của món này (trừ chính nó)
                                                        $otherQuantity = 0;
                                                        $query = \App\Models\InvoiceDetail::where(
                                                            'invoice_id',
                                                            $invoice->invoice_id,
                                                        )->where('dish_id', $item->dish_id);

                                                        if ($item->variant_id) {
                                                            $query->where('variant_id', $item->variant_id);
                                                        } else {
                                                            $query->whereNull('variant_id');
                                                        }

                                                        // Sử dụng detail_id thay vì id
                                                        $query->where('detail_id', '!=', $item->detail_id);
                                                        $otherQuantity = $query->sum('quantity');

                                                        // Số lượng tối đa có thể đặt = số lượng tồn kho - số lượng đã đặt bởi các dòng khác
                                                        $availableToOrder = max(0, $maxStock - $otherQuantity);

                                                        // Số lượng hiện tại không thể vượt quá availableToOrder
                                                        $currentQty = min($item->quantity, $maxStock);

                                                        // Số lượng tối đa có thể cập nhật = số lượng hiện tại + availableToOrder
                                                        $maxPossible = $item->quantity + $availableToOrder;
                                                    @endphp
                                                    <tr class="cart-item">
                                                        <td class="align-middle">
                                                            <p class="mb-0 fw-medium">{{ $item->dish->name }}</p>
                                                            @if ($item->variant)
                                                                <small
                                                                    class="text-muted d-block">{{ $item->variant->name }}</small>
                                                            @endif
                                                            <small
                                                                class="text-muted">{{ number_format($item->price, 0, ',', '.') }}
                                                                VND</small>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if (!$invoice->sent_to_kitchen_at)
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center">
                                                                    <form
                                                                        action="{{ route('staff.decreaseItem', $invoice->invoice_id) }}"
                                                                        method="POST" class="me-1">
                                                                        @csrf
                                                                        <input type="hidden" name="detail_id"
                                                                            value="{{ $item->detail_id }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                                                            style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0;">
                                                                            <i class="fas fa-minus"
                                                                                style="font-size: 10px;"></i>
                                                                        </button>
                                                                    </form>

                                                                    <span class="mx-2 text-center"
                                                                        style="min-width: 30px;">{{ $item->quantity }}</span>

                                                                    <form
                                                                        action="{{ route('staff.increaseItem', $invoice->invoice_id) }}"
                                                                        method="POST" class="ms-1">
                                                                        @csrf
                                                                        <input type="hidden" name="detail_id"
                                                                            value="{{ $item->detail_id }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            {{ $item->availableToOrder <= 0 ? 'disabled' : '' }}
                                                                            style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0;">
                                                                            <i class="fas fa-plus"
                                                                                style="font-size: 10px;"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <span class="text-center">{{ $item->quantity }}</span>
                                                                </div>
                                                            @endif
                                                        </td>

                                                        <td class="align-middle text-end fw-bold">
                                                            {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                            VND
                                                        </td>

                                                        <td class="align-middle px-2">
                                                            @if (!$invoice->sent_to_kitchen_at)
                                                                <form
                                                                    action="{{ route('staff.removeItem', ['invoice_id' => $invoice->invoice_id, 'item_id' => $item->detail_id]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm text-danger border-0"
                                                                        onclick="return confirm('Bạn có chắc muốn xóa món này?')">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-cart text-center p-4">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                        <p>Chưa có món ăn nào trong đơn hàng</p>
                                        <p class="small text-muted">Vui lòng chọn món từ menu bên trái</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Tổng tiền & thanh toán -->
                            <div class="card-footer bg-white border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-medium">Tổng tiền:</span>
                                    <span
                                        class="text-danger fw-bold fs-5">{{ number_format($invoice->total_price, 0, ',', '.') }}
                                        VND</span>
                                </div>

                                @if ($invoice->total_price > 0 && !$invoice->sent_to_kitchen_at)
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('staff.sendToKitchen', $invoice->invoice_id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-100 order-complete-btn"
                                                onclick="return confirm('Xác nhận gửi đơn hàng này đến đầu bếp?')">
                                                <i class="fas fa-utensils me-2"></i>Gửi đến bếp
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($invoice->sent_to_kitchen_at)
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Đơn hàng đã được gửi đến bếp lúc
                                        {{ \Carbon\Carbon::parse($invoice->sent_to_kitchen_at)->format('H:i d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
