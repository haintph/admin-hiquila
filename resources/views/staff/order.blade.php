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

        .kitchen-section-header {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #28a745;
        }

        .pending-section-header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            border-left: 4px solid #ffc107;
        }

        .cart-item.bg-light:hover {
            background-color: #f8f9fa !important;
            border-left-color: #28a745;
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
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        <div class="row">
            <!-- Phần Menu Món ăn hoặc Chi tiết món ăn -->
            <div class="col-lg-8">
                @if (isset($selected_dish))
                    <!-- Chi tiết món ăn và biến thể -->
                    <div class="card shadow-sm mb-4 dish-detail-card">
                        <div class="position-relative">
                            <a href="{{ route('staff.invoices.edit', $invoice->invoice_id) }}" class="back-to-menu">
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

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span
                                    class="text-danger fs-5 fw-bold">{{ number_format($selected_dish->price, 0, ',', '.') }}
                                    VND</span>
                                <span class="badge bg-{{ $selected_dish->available_stock > 0 ? 'success' : 'danger' }}">
                                    Còn {{ $selected_dish->available_stock }}
                                </span>
                            </div>

                            @if ($selected_dish->variants->count() > 0)
                                <h5 class="mt-4 mb-3">Tùy chọn thêm</h5>
                                <form action="{{ route('staff.invoices.addDishWithVariant', $invoice->invoice_id) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $selected_dish->id }}">
                                    <input type="hidden" name="is_add_more" value="1">

                                    <div class="mb-4">
                                        <!-- Option gốc luôn hiển thị nếu còn available_stock -->
                                        @if ($selected_dish->available_stock > 0)
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

                                        <!-- Tất cả biến thể đều hiển thị vì dùng chung kho với dish gốc -->
                                        @foreach ($selected_dish->variants as $variant)
                                            <div class="form-check variant-option">
                                                <input class="form-check-input" type="radio" name="variant_id"
                                                    id="variant{{ $variant->id }}" value="{{ $variant->id }}"
                                                    {{ $selected_dish->available_stock <= 0 && $loop->first ? 'checked' : '' }}>
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="variant{{ $variant->id }}">
                                                    <span class="variant-name">{{ $variant->name }}</span>
                                                    <span
                                                        class="variant-price">{{ number_format($variant->price, 0, ',', '.') }}
                                                        VND</span>
                                                </label>
                                                <small class="text-muted d-block ms-4">
                                                    Có thể đặt thêm: {{ $selected_dish->available_stock }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Hiển thị nút thêm vào giỏ nếu còn stock -->
                                    @if ($selected_dish->available_stock > 0)
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="quantity-control">
                                                <span class="me-2">Số lượng:</span>
                                                <input type="number" name="quantity" class="form-control quantity-input"
                                                    value="1" min="1"
                                                    max="{{ $selected_dish->available_stock }}">
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
                                <form action="{{ route('staff.invoices.addDish', $invoice->invoice_id) }}" method="POST"
                                    class="mt-4">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $selected_dish->id }}">
                                    <input type="hidden" name="is_add_more" value="1">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="quantity-control">
                                            <span class="me-2">Số lượng:</span>
                                            <input type="number" name="quantity" class="form-control quantity-input"
                                                value="1" min="1" max="{{ $selected_dish->available_stock }}">
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
                                    <form action="{{ route('staff.invoices.edit', $invoice->invoice_id) }}" method="GET"
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
                                <a href="{{ route('staff.invoices.edit', $invoice->invoice_id) }}"
                                    class="text-decoration-none">
                                    <span
                                        class="category-badge badge {{ !request('category') ? 'bg-primary' : 'bg-light text-dark' }} me-2 px-3 py-2">
                                        Tất cả
                                    </span>
                                </a>
                                @if (isset($categories) && count($categories) > 0)
                                    @foreach ($categories as $category)
                                        <a href="{{ route('staff.invoices.edit', ['id' => $invoice->invoice_id, 'category' => $category->id]) }}"
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
                                    <a href="{{ route('staff.invoices.edit', ['id' => $invoice->invoice_id, 'category' => request('category')]) }}"
                                        class="text-decoration-none me-2 mb-2">
                                        <div
                                            class="subcategory-item {{ !request('subcategory') ? 'active' : '' }} px-3 py-2 rounded text-center">
                                            <i class="fas fa-border-all"></i>
                                            <div>Tất cả</div>
                                        </div>
                                    </a>
                                    @foreach ($subcategories as $subcategory)
                                        <a href="{{ route('staff.invoices.edit', ['id' => $invoice->invoice_id, 'category' => request('category'), 'subcategory' => $subcategory->id]) }}"
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
                                    <div class="col">
                                        <div class="card dish-card h-100">
                                            @if ($dish->stock <= 5 && $dish->stock > 0)
                                                <span class="dish-badge badge bg-warning">Sắp hết</span>
                                            @elseif($dish->stock == 0)
                                                <span class="dish-badge badge bg-danger">Hết hàng</span>
                                            @endif

                                            <img height="170px"
                                                src="{{ $dish->image ? asset('storage/' . $dish->image) : 'https://via.placeholder.com/300x200?text=' . urlencode($dish->name) }}"
                                                class="dish-image" alt="{{ $dish->name }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $dish->name }}</h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="text-danger fw-bold">{{ number_format($dish->price, 0, ',', '.') }}
                                                        VND</span>
                                                    <span
                                                        class="badge bg-{{ $dish->available_stock > 0 ? 'success' : 'danger' }}">
                                                        Còn {{ $dish->available_stock }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top-0">
                                                @if ($dish->available_stock > 0 && $dish->is_available)
                                                    @if ($dish->variants->count() > 0)
                                                        <a href="{{ route('staff.invoices.edit', ['id' => $invoice->invoice_id, 'dish' => $dish->id]) }}"
                                                            class="btn btn-primary w-100">
                                                            <i class="fas fa-list-ul me-1"></i> Chọn tùy chọn
                                                        </a>
                                                    @else
                                                        <form
                                                            action="{{ route('staff.invoices.addDish', $invoice->invoice_id) }}"
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

                    <!-- Giỏ hàng cải thiện -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                                    Danh sách đã đặt
                                </h5>
                                @if ($invoice->items->count() > 0)
                                    <span class="badge bg-info">
                                        Tổng: {{ $invoice->items->count() }} món
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="order-list">
                                @if (count($invoice->items) > 0)

                                    <!-- Phần món đã gửi đến bếp -->
                                    @if ($invoice->sentItems && $invoice->sentItems->count() > 0)
                                        <div class="bg-light border-bottom">
                                            <div class="px-3 py-2 d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Đã gửi bếp
                                                </h6>
                                                <span class="badge bg-success">
                                                    {{ $invoice->sentItems->count() }} món
                                                </span>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    @foreach ($invoice->sentItems as $item)
                                                        <tr class="border-bottom bg-light">
                                                            <td class="align-middle px-3 py-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="fas fa-lock text-success"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <p class="mb-0 fw-medium">{{ $item->dish->name }}
                                                                        </p>
                                                                        @if ($item->variant)
                                                                            <small
                                                                                class="text-muted d-block">{{ $item->variant->name }}</small>
                                                                        @endif
                                                                        <small class="text-muted">
                                                                            {{ number_format($item->price, 0, ',', '.') }}
                                                                            VND x {{ $item->quantity }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle text-end px-3 py-2">
                                                                <div class="fw-bold">
                                                                    {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                    VND
                                                                </div>
                                                                <small class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($item->sent_to_kitchen_at)->format('H:i d/m') }}
                                                                </small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <!-- Phần món mới order (pending) -->
                                    @if ($invoice->pendingItems && $invoice->pendingItems->count() > 0)
                                        @if ($invoice->sentItems && $invoice->sentItems->count() > 0)
                                            <div class="bg-primary text-white">
                                                <div class="px-3 py-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Chờ gửi bếp
                                                    </h6>
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $invoice->pendingItems->count() }} món
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                @if (!($invoice->sentItems && $invoice->sentItems->count() > 0))
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="py-2 px-3">Món ăn</th>
                                                            <th class="py-2 text-center" width="100">Số lượng</th>
                                                            <th class="py-2 text-end px-3">Thành tiền</th>
                                                            <th class="py-2" width="50"></th>
                                                        </tr>
                                                    </thead>
                                                @endif
                                                <tbody>
                                                    @foreach ($invoice->pendingItems as $item)
                                                        <tr class="border-bottom">
                                                            <td class="align-middle px-3 py-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <form
                                                                            action="{{ route('staff.invoices.removeItem', ['invoice_id' => $invoice->invoice_id, 'item_id' => $item->detail_id]) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm text-danger border-0 p-1"
                                                                                onclick="return confirm('Bạn có chắc muốn xóa món này?')"
                                                                                style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                                                <i class="fas fa-times"
                                                                                    style="font-size: 10px;"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <p class="mb-0 fw-medium">{{ $item->dish->name }}
                                                                        </p>
                                                                        @if ($item->variant)
                                                                            <small
                                                                                class="text-muted d-block">{{ $item->variant->name }}</small>
                                                                        @endif

                                                                    </div>
                                                                </div>
                                                            </td>

                                                            <td class="align-middle text-center px-2">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center">
                                                                    <!-- Nút giảm -->
                                                                    <form
                                                                        action="{{ route('staff.invoices.decreaseItem', $invoice->invoice_id) }}"
                                                                        method="POST" class="me-1">
                                                                        @csrf
                                                                        <input type="hidden" name="detail_id"
                                                                            value="{{ $item->detail_id }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                                                            style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                            <i class="fas fa-minus"
                                                                                style="font-size: 8px;"></i>
                                                                        </button>
                                                                    </form>

                                                                    <!-- Số lượng -->
                                                                    <span class="mx-2 fw-bold"
                                                                        style="min-width: 25px; text-align: center;">
                                                                        {{ $item->quantity }}
                                                                    </span>

                                                                    <!-- Nút tăng -->
                                                                    <form
                                                                        action="{{ route('staff.invoices.increaseItem', $invoice->invoice_id) }}"
                                                                        method="POST" class="ms-1">
                                                                        @csrf
                                                                        <input type="hidden" name="detail_id"
                                                                            value="{{ $item->detail_id }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                            {{ $item->availableToOrder <= 0 ? 'disabled' : '' }}
                                                                            style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                                            <i class="fas fa-plus"
                                                                                style="font-size: 8px;"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                @if ($item->availableToOrder <= 0)
                                                                    <small class="text-danger d-block mt-1">Hết
                                                                        hàng</small>
                                                                @else
                                                                    <small class="text-muted d-block mt-1"></small>
                                                                @endif
                                                            </td>

                                                            <td class="align-middle text-end px-3">
                                                                <div class="fw-bold">
                                                                    {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                    VND
                                                                </div>
                                                            </td>


                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <!-- Thông báo khi không có món pending -->
                                    @if (!$invoice->pendingItems || $invoice->pendingItems->count() == 0)
                                        @if ($invoice->sentItems && $invoice->sentItems->count() > 0)
                                            <div class="text-center p-3 bg-light border-top">
                                                <div class="text-success mb-2">
                                                    <i class="fas fa-check-circle fa-2x"></i>
                                                </div>
                                                <p class="mb-1 fw-medium">Tất cả món đã được gửi đến bếp</p>
                                                <small class="text-muted">Bạn có thể tiếp tục order thêm món mới từ
                                                    menu</small>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <!-- Giỏ hàng trống -->
                                    <div class="empty-cart text-center p-4">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                        <p class="mb-1">Chưa có món ăn nào trong đơn hàng</p>
                                        <small class="text-muted">Vui lòng chọn món từ menu bên trái</small>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer: Tổng tiền & thanh toán -->
                            <div class="card-footer bg-white border-top">
                                <!-- Thống kê chi tiết -->
                                @if ($invoice->sentItems && $invoice->sentItems->count() > 0)
                                    <div class="row text-center mb-3 small">
                                        <div class="col-6">
                                            <div class="border-end pe-2">
                                                <div class="text-muted">Đã gửi bếp</div>
                                                <div class="fw-bold text-success">
                                                    @php
                                                        $sentTotal = $invoice->sentItems->sum(function ($item) {
                                                            return $item->quantity * $item->price;
                                                        });
                                                    @endphp
                                                    {{ number_format($sentTotal, 0, ',', '.') }} ₫
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="ps-2">
                                                <div class="text-muted">Chờ gửi bếp</div>
                                                <div class="fw-bold text-primary">
                                                    @php
                                                        $pendingTotal = $invoice->pendingItems
                                                            ? $invoice->pendingItems->sum(function ($item) {
                                                                return $item->quantity * $item->price;
                                                            })
                                                            : 0;
                                                    @endphp
                                                    {{ number_format($pendingTotal, 0, ',', '.') }} ₫
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Tổng cộng -->
                                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                    <span class="fw-medium">Tổng cộng:</span>
                                    <span class="text-danger fw-bold fs-5">
                                        {{ number_format($invoice->total_price, 0, ',', '.') }} VND
                                    </span>
                                </div>

                                <!-- Nút hành động -->
                                @if ($invoice->pendingItems && $invoice->pendingItems->count() > 0)
                                    <div class="d-grid">
                                        <form action="{{ route('staff.invoices.sendToKitchen', $invoice->invoice_id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100 order-complete-btn"
                                                onclick="return confirm('Xác nhận gửi {{ $invoice->pendingItems->count() }} món mới đến đầu bếp?')">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                Gửi {{ $invoice->pendingItems->count() }} món đến bếp
                                            </button>
                                        </form>
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
