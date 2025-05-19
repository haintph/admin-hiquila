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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\InventoryLogController;
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

        //Thanh toan vnpay
        Route::get('/vnpay-payment/{invoice_id}', [VnpayController::class, 'createPayment'])->name('vnpay.payment');
        Route::get('/vnpay-return', [VnpayController::class, 'vnpayReturn'])->name('vnpay.return');
        //Vị trí bố cụccục
        Route::resource('areas', AreaController::class);
        Route::put('/areas/update/{id}', [AreaController::class, 'update'])->name('areas.update');

        Route::get('/admin/areas/{area}/manage-hours', [App\Http\Controllers\AreaOperatingHourController::class, 'manageHours'])
            ->name('areas.manageHours');

        // Route để cập nhật giờ hoạt động từ form
        Route::post('/admin/areas/{area}/update-hours', [App\Http\Controllers\AreaOperatingHourController::class, 'updateHours'])
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

        // Danh sách hóa đơn
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        // Tạo hóa đơn mới
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::get('/get-tables/{area_id}', [InvoiceController::class, 'getTables']);

        Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
        // Chỉnh sửa hóa đơn
        Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice_id}/update', [InvoiceController::class, 'update'])->name('invoices.update');
        //Thêm món ăn
        Route::post('/invoices/{id}/add-dish', [InvoiceController::class, 'addDish'])->name('invoices.addDish');
        //Thanh toán
        Route::get('/invoices/{id}/checkout', [InvoiceController::class, 'checkout'])->name('invoices.checkout');
        Route::get('/invoices/{id}/payment', [InvoiceController::class, 'payment'])->name('invoices.payment');
        Route::get('/invoices/{id}/confirm-payment', [InvoiceController::class, 'confirmPayment'])->name('invoices.confirmPayment');
        Route::post('/invoices/{id}/add-dish-with-variant', [InvoiceController::class, 'addDishWithVariant'])->name('invoices.addDishWithVariant');
        // Xóa hóa đơn
        Route::delete('/invoices/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        // In hóa đơn
        Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        // Route lấy danh mục con
        Route::get('/get-subcategories/{category_id}', [App\Http\Controllers\CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
        // Thêm các route cho tăng/giảm số lượng
        Route::post('invoices/{id}/increase-item', [InvoiceController::class, 'increaseItem'])->name('invoices.increaseItem');
        Route::post('invoices/{id}/decrease-item', [InvoiceController::class, 'decreaseItem'])->name('invoices.decreaseItem');
        // Route cập nhật số lượng món trong hóa đơn
        Route::put('/invoices/{invoice_id}/items/{item_id}/update-quantity', [App\Http\Controllers\InvoiceController::class, 'updateQuantity'])->name('invoices.updateQuantity');

        // Route xóa món khỏi hóa đơn
        Route::delete('/invoices/{invoice_id}/items/{item_id}/remove', [App\Http\Controllers\InvoiceController::class, 'removeItem'])->name('invoices.removeItem');
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
    // Trang dành cho Quản lý
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager', function () {
            return "Trang dành cho Quản lý";
        });
    });
    // Trang dành cho Nhân viên
    Route::middleware(['role:staff'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
    });
});
