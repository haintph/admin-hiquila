<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishImageController;
use App\Http\Controllers\DishVariantController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AreaOperatingHourController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StaffController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Trang chủ khi chưa đăng nhập
Route::get('/', function () {
    return view('admin.index'); // Thay bằng giao diện trang chủ của bạn
});
//Đăn nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Profile
Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
//Change password - Profile
Route::get('/profile/change-password', function () {
    return view('admin.auth.ChangePassword');
})->name('profile.changePassword');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

Route::prefix('chat')->name('chat.')->middleware(['auth'])->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/{user}', [ChatController::class, 'show'])->name('show');
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::post('/mark-as-read', [ChatController::class, 'markAsRead'])->name('markAsRead');
    Route::delete('/message/{message}', [ChatController::class, 'deleteMessage'])->name('deleteMessage');
    Route::post('/reaction', [ChatController::class, 'addReaction'])->name('addReaction');

    // Online status management
    Route::get('/update-status', function () {
        if (Auth::check()) {
            Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
            Cache::put('user-last-seen-' . Auth::id(), now(), now()->addDays(1));
        }
        return response()->json(['status' => 'updated']);
    })->name('updateStatus');

    Route::get('/get-unread-count', [ChatController::class, 'getUnreadCount'])->name('unreadCount');
});

//Phân quyền 
Route::middleware(['auth'])->group(function () {
    Route::get('/owner', [AdminController::class, 'index'])->name('owner.dashboard');

    // Chỉ Chủ mới vào được /admin/settings
    Route::middleware(['role:owner'])->group(function () {
        //paypal
        Route::get('/paypal/payment/{invoice_id}', [PayPalController::class, 'createPayment'])->name('paypal.payment');
        Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
        Route::get('/invoices/check-payment/{invoice_id}', [InvoiceController::class, 'checkPayment'])->name('invoices.checkPayment');

        //Vị trí bố cụccục
        Route::resource('areas', AreaController::class);
        Route::put('/areas/update/{id}', [AreaController::class, 'update'])->name('areas.update');

        Route::get('/admin/areas/{area}/manage-hours', [AreaOperatingHourController::class, 'manageHours'])
            ->name('areas.manageHours');

        Route::post('/admin/areas/{area}/update-hours', [AreaOperatingHourController::class, 'updateHours'])
            ->name('areas.updateHours');

        // Route để cập nhật trạng thái của khu vực dựa trên giờ hoạt động
        Route::get('/admin/areas/update-statuses', [App\Http\Controllers\AreaOperatingHourController::class, 'updateAreaStatuses'])
            ->name('areas.updateAreaStatuses');

        // Thêm routes mới cho thêm/xóa khung giờ trực tiếp
        Route::get('/admin/areas/{area}/add-time-slot', [App\Http\Controllers\AreaOperatingHourController::class, 'addTimeSlot'])
            ->name('areas.addTimeSlot');

        Route::get('/admin/areas/{area}/remove-time-slot/{timeSlotId}', [App\Http\Controllers\AreaOperatingHourController::class, 'removeTimeSlot'])
            ->name('areas.removeTimeSlot');

        //Quản lý bàn 
        Route::resource('tables', TableController::class);

        // Quản lý đặt bàn
        Route::prefix('admin/reservations')->name('admin.reservations.')->group(function () {
            // Danh sách đặt bàn
            Route::get('/', [ReservationController::class, 'index'])
                ->name('index');

            // Chi tiết đặt bàn
            Route::get('/{tableId}', [ReservationController::class, 'show'])
                ->name('show');

            // Check-in khách hàng
            Route::post('/{tableId}/checkin', [ReservationController::class, 'checkin'])
                ->name('checkin');

            // Hủy đặt bàn
            Route::delete('/{tableId}/cancel', [ReservationController::class, 'cancel'])
                ->name('cancel');

            // Tìm kiếm nhanh
            Route::get('/search/quick', [ReservationController::class, 'quickSearch'])
                ->name('quick-search');
        });
        // Danh sách hóa đơn
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

        // Tạo hóa đơn mới
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/get-tables/{area_id}', [InvoiceController::class, 'getTables']);

        // Chỉnh sửa hóa đơn (order)
        Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice_id}/update', [InvoiceController::class, 'update'])->name('invoices.update');

        // Thêm món ăn
        Route::post('/invoices/{id}/add-dish', [InvoiceController::class, 'addDish'])->name('invoices.addDish');
        Route::post('/invoices/{id}/add-dish-with-variant', [InvoiceController::class, 'addDishWithVariant'])->name('invoices.addDishWithVariant');

        // Quản lý số lượng món
        Route::post('invoices/{id}/increase-item', [InvoiceController::class, 'increaseItem'])->name('invoices.increaseItem');
        Route::post('invoices/{id}/decrease-item', [InvoiceController::class, 'decreaseItem'])->name('invoices.decreaseItem');
        Route::delete('/invoices/{invoice_id}/items/{item_id}/remove', [InvoiceController::class, 'removeItem'])->name('invoices.removeItem');
        Route::put('/invoices/{invoice_id}/items/{item_id}/update-quantity', [InvoiceController::class, 'updateQuantity'])->name('invoices.updateQuantity');

        // Gửi đến bếp
        Route::post('/invoices/{invoice}/send-to-kitchen', [InvoiceController::class, 'sendToKitchen'])->name('invoices.sendToKitchen');

        // THANH TOÁN - SỬA ĐỂ TRÁNH CONFLICT
        Route::get('/invoices/{id}/payment', [InvoiceController::class, 'payment'])->name('invoices.payment');
        Route::post('/invoices/{id}/checkout', [InvoiceController::class, 'checkout'])->name('invoices.checkout');  // Đổi thành POST
        Route::post('/invoices/{id}/confirm-payment', [InvoiceController::class, 'confirmPayment'])->name('invoices.confirmPayment');

        // HOÀN TẤT VÀ DỌN BÀN - SỬA ĐƯỜNG DẪN
        Route::patch('/invoices/{id}/finish', [InvoiceController::class, 'finishAndCleanTable'])->name('invoices.finish');
        Route::patch('/invoices/{id}/quick-clean', [InvoiceController::class, 'quickClean'])->name('invoices.quick-clean');

        // In hóa đơn
        Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');

        // Kiểm tra thanh toán
        Route::get('/invoices/check-payment/{invoice_id}', [InvoiceController::class, 'checkPayment'])->name('invoices.checkPayment');

        // Xóa hóa đơn
        Route::delete('/invoices/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        // Category management
        Route::get('category-list', [CategoryController::class, 'list'])->name('category-list');
        Route::get('category-create', [CategoryController::class, 'create'])->name('category-create');
        Route::post('category_store', [CategoryController::class, 'store'])->name('category_store');
        Route::get('category_edit/{id}', [CategoryController::class, 'edit'])->name('category_edit');
        Route::put('category_update/{id}', [CategoryController::class, 'update'])->name('category_update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('category_detail/{id}', [CategoryController::class, 'detail'])->name('category_detail');

        // Quản lý danh mục con (sub_categories)

        Route::get('sub_category_list', [SubCategoryController::class, 'list'])->name('sub_category_list');
        Route::get('sub_category_create', [SubCategoryController::class, 'create'])->name('sub_category_create');
        Route::post('sub_category_store', [SubCategoryController::class, 'store'])->name('sub_category_store');
        Route::get('sub_category_edit/{id}', [SubCategoryController::class, 'edit'])->name('sub_category_edit');
        Route::put('sub_category_update/{id}', [SubCategoryController::class, 'update'])->name('sub_category_update');
        Route::delete('sub_category_destroy/{id}', [SubCategoryController::class, 'destroy'])->name('sub_category_destroy');
        Route::get('sub_category_detail/{id}', [SubCategoryController::class, 'detail'])->name('sub_category_detail');

        // Quản lý món ăn ( dishes)
        Route::get('dish_list', [DishController::class, 'list'])->name('dish_list');
        Route::get('dish_create', [DishController::class, 'create'])->name('dish_create');
        Route::post('dish_store', [DishController::class, 'store'])->name('dish_store');
        Route::get('dish_edit/{id}', [DishController::class, 'edit'])->name('dish_edit');
        Route::put('dish_update/{id}', [DishController::class, 'update'])->name('dish_update');
        Route::delete('dish_destroy/{id}', [DishController::class, 'destroy'])->name('dish_destroy');

        //search món ăn
        Route::get('/search-dishes', [DishController::class, 'search'])->name('dishes.search');

        // Route::get('/dishes/{id}', [DishController::class, 'show'])->name('dishes.show');
        Route::get('/dish_detail/show/{id}', [DishController::class, 'show'])->name('dish_detail');
        //vảiant
        Route::get('variants', [DishVariantController::class, 'list'])->name('variant_list');
        Route::get('/variants/create/{dish_id}', [DishVariantController::class, 'create'])->name('variants.create');
        Route::get('/variants/edit/{id}', [DishVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/variants/update/{id}', [DishVariantController::class, 'update'])->name('variants.update');
        Route::post('/variants/store', [DishVariantController::class, 'store'])->name('variants.store');
        Route::delete('/variants/destroy/{id}', [DishVariantController::class, 'destroy'])->name('variants.destroy');
        // Route::get('dish_detail/{id}', [DishController::class, 'detail'])->name('dish_detail');

        //ablum ảnh
        Route::post('/dish/image/update/{id}', [DishController::class, 'updateImage'])->name('dish_image_update');
        Route::post('/dishes/{dish}/upload-images', [DishImageController::class, 'store'])->name('dishes.upload_images');
        Route::delete('/dish/image/delete/{id}', [DishController::class, 'deleteImage'])->name('dish_image_delete');

        // Quản lý người dùng (users)
        Route::get('user_list', [UserController::class, 'list'])->name('user_list');
        Route::get('user_create', [UserController::class, 'create'])->name('user_create');
        Route::post('user_store', [UserController::class, 'store'])->name('user_store');
        Route::get('user_edit/{id}', [UserController::class, 'edit'])->name('user_edit');
        Route::put('user_update/{id}', [UserController::class, 'update'])->name('user_update');
        Route::delete('user_destroy/{id}', [UserController::class, 'destroy'])->name('user_destroy');
        Route::get('user_detail/{id}', [UserController::class, 'detail'])->name('user_detail');
        //Quản lý kho
        Route::resource('inventory', InventoryController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('purchase', InventoryPurchaseController::class);
        //Lịch sử nhập - xuất
        Route::resource('inventory_logs', InventoryLogController::class);
    });
    // Trang dành cho Quản lý==================================================
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager', function () {
            return "Trang dành cho Quản lý";
        });
    });
    // Trang dành cho Nhân viên===========================
    Route::middleware(['role:staff'])->group(function () {
        // Dashboard
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        // Thêm route confirmPayment
        Route::post('/invoices/{id}/confirm-payment', [InvoiceController::class, 'confirmPayment'])->name('invoices.confirmPayment');
        // Quản lý hóa đơn
        Route::get('/staff/invoices/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff/invoices/store', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/invoices/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');

        // Thêm món ăn vào hóa đơn
        Route::post('/staff/invoices/{id}/add-dish', [StaffController::class, 'addDish'])->name('staff.addDish');
        Route::post('/staff/invoices/{id}/add-dish-with-variant', [StaffController::class, 'addDishWithVariant'])->name('staff.addDishWithVariant');

        // Gửi đơn hàng đến bếp
        Route::post('/staff/invoices/{invoice}/send-to-kitchen', [StaffController::class, 'sendToKitchen'])->name('staff.sendToKitchen');

        // Quản lý món ăn trong giỏ hàng
        Route::post('/staff/invoices/{id}/increase-item', [StaffController::class, 'increaseItem'])->name('staff.increaseItem');
        Route::post('/staff/invoices/{id}/decrease-item', [StaffController::class, 'decreaseItem'])->name('staff.decreaseItem');
        Route::delete('/staff/invoices/{invoice_id}/items/{item_id}/remove', [StaffController::class, 'removeItem'])->name('staff.removeItem');

        // Thanh toán
        Route::get('/staff/invoices/{id}/payment', [StaffController::class, 'payment'])->name('staff.payment');
        Route::get('/staff/invoices/{id}/checkout', [StaffController::class, 'checkout'])->name('staff.checkout');
        Route::get('/staff/invoices/{id}/confirm-payment', [StaffController::class, 'confirmPayment'])->name('staff.confirmPayment');

        // In hóa đơn
        Route::get('/staff/invoices/{id}/print', [StaffController::class, 'print'])->name('staff.print');

        // Xóa hóa đơn
        Route::delete('/staff/invoices/{id}/delete', [StaffController::class, 'destroy'])->name('staff.destroy');
        // Trong nhóm Route::middleware(['auth', 'role:staff']), thêm dòng sau:
        Route::get('/staff/invoices/check-payment/{invoice_id}', [StaffController::class, 'checkPayment'])->name('staff.checkPayment');
        // Lấy danh sách bàn theo khu vực
        Route::get('/staff/get-tables/{area_id}', [StaffController::class, 'getTables'])->name('staff.getTables');
    });
    // Trang dành cho Đầu bếp=====================
    Route::middleware(['role:chef'])->group(function () {
        Route::get('/chef', [ChefController::class, 'index'])->name('chef.dashboard');
        Route::post('/chef/confirm-order/{invoice}', [ChefController::class, 'confirmOrder'])->name('chef.confirmOrder');
    });
    // Trang dành cho Khách hàng=====================
    Route::middleware(['role:customer'])->group(function (): void {
        // Dashboard
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');

        // Chọn bàn đơn giản
        Route::get('/select-table', [CustomerController::class, 'selectTable'])->name('customer.select-table');
        Route::get('/reserve-table/{tableId}', [CustomerController::class, 'reserveTable'])->name('customer.reserve-table');

        // Floor plan (giữ lại nếu muốn)
        Route::get('/floor-plan/{floor?}', [CustomerController::class, 'floorPlan'])->name('customer.floor-plan');

        // Reservations
        Route::get('/reservations', [CustomerController::class, 'myReservations'])->name('customer.reservations');
        Route::post('/reservations', [CustomerController::class, 'makeReservation'])->name('customer.reservations.store');
        Route::delete('/reservations/{tableId}', [CustomerController::class, 'cancelReservation'])->name('customer.reservations.cancel');
    });

    // Đặt route VNPay ở ngoài middleware role để cả owner và staff đều có thể truy cập
    Route::get('/vnpay-payment/{invoice_id}', [VnpayController::class, 'createPayment'])->name('vnpay.payment')->middleware('auth');
    Route::get('/vnpay-return', [VnpayController::class, 'vnpayReturn'])->name('vnpay.return')->middleware('auth');
});
