<?php

use App\Http\Controllers\Admin\LogoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
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
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashBoardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalaryController;
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

// ===========================================
// PUBLIC ROUTES (Không cần đăng nhập)
// ===========================================

// Trang chủ khi chưa đăng nhập
Route::get('/', function () {
    return view('admin.index');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ===========================================
// AUTHENTICATED ROUTES (Cần đăng nhập)
// ===========================================

Route::middleware(['auth'])->group(function () {

    // ===========================================
    // PROFILE ROUTES (Cho tất cả user đã đăng nhập)
    // ===========================================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/change-password', function () {
        return view('admin.auth.ChangePassword');
    })->name('profile.changePassword');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // ===========================================
    // CHAT ROUTES (Cho tất cả user đã đăng nhập)
    // ===========================================
    Route::prefix('chat')->name('chat.')->group(function () {
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

    // ===========================================
    // PAYMENT ROUTES (Cho tất cả user đã đăng nhập)
    // ===========================================
    Route::get('/invoices/{id}/payment', [InvoiceController::class, 'payment'])->name('invoices.payment');
    Route::post('/invoices/{id}/checkout', [InvoiceController::class, 'checkout'])->name('invoices.checkout');
    Route::post('/invoices/{id}/confirm-payment', [InvoiceController::class, 'confirmPayment'])->name('invoices.confirmPayment');

    // VNPay Routes
    Route::get('/vnpay-payment/{invoice_id}', [VnpayController::class, 'createPayment'])->name('vnpay.payment');
    Route::get('/vnpay-return', [VnpayController::class, 'vnpayReturn'])->name('vnpay.return');

    // Owner Dashboard
    Route::get('/owner', [AdminController::class, 'index'])->name('owner.dashboard');

    // ===========================================
    // OWNER ROUTES
    // ===========================================
    Route::middleware(['role:owner'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('logos', LogoController::class);
        });
        // PayPal Routes
        Route::get('/paypal/payment/{invoice_id}', [PayPalController::class, 'createPayment'])->name('paypal.payment');
        Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
        Route::get('/invoices/check-payment/{invoice_id}', [InvoiceController::class, 'checkPayment'])->name('invoices.checkPayment');

        // Dashboard Routes
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
        Route::post('/admin/dashboard/update-stats', [DashboardController::class, 'updateStats'])->name('admin.dashboard.update');
        Route::get('/admin/dashboard/export', [DashboardController::class, 'export'])->name('admin.dashboard.export');

        // Area Management
        Route::resource('areas', AreaController::class);
        Route::put('/areas/update/{id}', [AreaController::class, 'update'])->name('areas.update');
        Route::get('/admin/areas/{area}/manage-hours', [AreaOperatingHourController::class, 'manageHours'])->name('areas.manageHours');
        Route::post('/admin/areas/{area}/update-hours', [AreaOperatingHourController::class, 'updateHours'])->name('areas.updateHours');
        Route::get('/admin/areas/update-statuses', [AreaOperatingHourController::class, 'updateAreaStatuses'])->name('areas.updateAreaStatuses');
        Route::get('/admin/areas/{area}/add-time-slot', [AreaOperatingHourController::class, 'addTimeSlot'])->name('areas.addTimeSlot');
        Route::get('/admin/areas/{area}/remove-time-slot/{timeSlotId}', [AreaOperatingHourController::class, 'removeTimeSlot'])->name('areas.removeTimeSlot');

        // Table Management
        Route::resource('tables', TableController::class);

        // Reservation Management
        Route::prefix('admin/reservations')->name('admin.reservations.')->group(function () {
            Route::get('/', [ReservationController::class, 'index'])->name('index');
            Route::get('/{tableId}', [ReservationController::class, 'show'])->name('show');
            Route::post('/{tableId}/checkin', [ReservationController::class, 'checkin'])->name('checkin');
            Route::delete('/{tableId}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
            Route::get('/search/quick', [ReservationController::class, 'quickSearch'])->name('quick-search');
        });

        // Invoice Management
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/get-tables/{area_id}', [InvoiceController::class, 'getTables']);
        Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice_id}/update', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{id}/add-dish', [InvoiceController::class, 'addDish'])->name('invoices.addDish');
        Route::post('/invoices/{id}/add-dish-with-variant', [InvoiceController::class, 'addDishWithVariant'])->name('invoices.addDishWithVariant');
        Route::post('invoices/{id}/increase-item', [InvoiceController::class, 'increaseItem'])->name('invoices.increaseItem');
        Route::post('invoices/{id}/decrease-item', [InvoiceController::class, 'decreaseItem'])->name('invoices.decreaseItem');
        Route::delete('/invoices/{invoice_id}/items/{item_id}/remove', [InvoiceController::class, 'removeItem'])->name('invoices.removeItem');
        Route::put('/invoices/{invoice_id}/items/{item_id}/update-quantity', [InvoiceController::class, 'updateQuantity'])->name('invoices.updateQuantity');
        Route::post('/invoices/{invoice}/send-to-kitchen', [InvoiceController::class, 'sendToKitchen'])->name('invoices.sendToKitchen');
        Route::patch('/invoices/{id}/finish', [InvoiceController::class, 'finishAndCleanTable'])->name('invoices.finish');
        Route::patch('/invoices/{id}/quick-clean', [InvoiceController::class, 'quickClean'])->name('invoices.quick-clean');
        Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::delete('/invoices/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // Attendance Management (Owner)
        Route::get('/admin/attendance', [AttendanceController::class, 'list'])->name('admin.attendance.list');
        Route::post('/admin/attendance/update/{id}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::post('/admin/attendance/note/update/{id}', [AttendanceController::class, 'updateNote'])->name('admin.attendance.note.update');
        Route::post('/admin/attendance/reset/{id}', [AttendanceController::class, 'resetAttendance'])->name('admin.attendance.reset');
        Route::post('/admin/attendance/{id}/note', [AttendanceController::class, 'updateNote'])->name('admin.attendance.note');
        Route::post('/admin/attendance/{id}/checkout', [AttendanceController::class, 'checkOut'])->name('admin.attendance.checkOut');
        Route::post('/admin/attendance/update-shift/{id}', [AttendanceController::class, 'updateShift'])->name('admin.attendance.updateShift');

        // User Management
        Route::get('user_list', [UserController::class, 'list'])->name('user_list');
        Route::get('user_create', [UserController::class, 'create'])->name('user_create');
        Route::post('user_store', [UserController::class, 'store'])->name('user_store');
        Route::get('user_edit/{id}', [UserController::class, 'edit'])->name('user_edit');
        Route::put('user_update/{id}', [UserController::class, 'update'])->name('user_update');
        Route::delete('user_destroy/{id}', [UserController::class, 'destroy'])->name('user_destroy');
        Route::get('user_detail/{id}', [UserController::class, 'detail'])->name('user_detail');

        // Category Management
        Route::get('category-list', [CategoryController::class, 'list'])->name('category-list');
        Route::get('category-create', [CategoryController::class, 'create'])->name('category-create');
        Route::post('category_store', [CategoryController::class, 'store'])->name('category_store');
        Route::get('category_edit/{id}', [CategoryController::class, 'edit'])->name('category_edit');
        Route::put('category_update/{id}', [CategoryController::class, 'update'])->name('category_update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('category_detail/{id}', [CategoryController::class, 'detail'])->name('category_detail');

        // Sub Category Management
        Route::get('sub_category_list', [SubCategoryController::class, 'list'])->name('sub_category_list');
        Route::get('sub_category_create', [SubCategoryController::class, 'create'])->name('sub_category_create');
        Route::post('sub_category_store', [SubCategoryController::class, 'store'])->name('sub_category_store');
        Route::get('sub_category_edit/{id}', [SubCategoryController::class, 'edit'])->name('sub_category_edit');
        Route::put('sub_category_update/{id}', [SubCategoryController::class, 'update'])->name('sub_category_update');
        Route::delete('sub_category_destroy/{id}', [SubCategoryController::class, 'destroy'])->name('sub_category_destroy');
        Route::get('sub_category_detail/{id}', [SubCategoryController::class, 'detail'])->name('sub_category_detail');

        // Dish Management
        Route::get('dish_list', [DishController::class, 'list'])->name('dish_list');
        Route::get('dish_create', [DishController::class, 'create'])->name('dish_create');
        Route::post('dish_store', [DishController::class, 'store'])->name('dish_store');
        Route::get('dish_edit/{id}', [DishController::class, 'edit'])->name('dish_edit');
        Route::put('dish_update/{id}', [DishController::class, 'update'])->name('dish_update');
        Route::delete('dish_destroy/{id}', [DishController::class, 'destroy'])->name('dish_destroy');
        Route::get('/search-dishes', [DishController::class, 'search'])->name('dishes.search');
        Route::get('/dish_detail/show/{id}', [DishController::class, 'show'])->name('dish_detail');

        // Dish Variant Management
        Route::get('variants', [DishVariantController::class, 'list'])->name('variant_list');
        Route::get('/variants/create/{dish_id}', [DishVariantController::class, 'create'])->name('variants.create');
        Route::get('/variants/edit/{id}', [DishVariantController::class, 'edit'])->name('variants.edit');
        Route::put('/variants/update/{id}', [DishVariantController::class, 'update'])->name('variants.update');
        Route::post('/variants/store', [DishVariantController::class, 'store'])->name('variants.store');
        Route::delete('/variants/destroy/{id}', [DishVariantController::class, 'destroy'])->name('variants.destroy');

        // Dish Image Management
        Route::post('/dish/image/update/{id}', [DishController::class, 'updateImage'])->name('dish_image_update');
        Route::post('/dishes/{dish}/upload-images', [DishImageController::class, 'store'])->name('dishes.upload_images');
        Route::delete('/dish/image/delete/{id}', [DishController::class, 'deleteImage'])->name('dish_image_delete');

        // Inventory Management
        Route::resource('inventory', InventoryController::class);
        Route::resource('supplier', SupplierController::class);
        Route::resource('purchase', InventoryPurchaseController::class);
        Route::resource('inventory_logs', InventoryLogController::class);
    });

    // ===========================================
    // MANAGER ROUTES
    // ===========================================
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager', [ManagerController::class, 'index'])->name('manager.index');
        // Attendance Management (Manager)
        Route::prefix('manager')->name('manager.')->group(function () {
            Route::get('/attendance', [ManagerController::class, 'list'])->name('attendance.list');
            Route::post('/attendance/update/{id}', [ManagerController::class, 'update'])->name('attendance.update');
            Route::post('/attendance/note/update/{id}', [ManagerController::class, 'updateNote'])->name('attendance.note.update');
            Route::post('/attendance/reset/{id}', [ManagerController::class, 'resetAttendance'])->name('attendance.reset');
            Route::post('/attendance/{id}/note', [ManagerController::class, 'updateNote'])->name('attendance.note');
            Route::post('/attendance/{id}/checkout', [ManagerController::class, 'checkOut'])->name('attendance.checkOut');
            Route::post('/attendance/update-shift/{id}', [ManagerController::class, 'updateShift'])->name('attendance.updateShift');
        });

        // Salary Management
        Route::prefix('salary')->name('salary.')->group(function () {
            Route::get('/settings', [ManagerController::class, 'settings'])->name('settings');
            Route::post('/settings', [ManagerController::class, 'updateSettings'])->name('updateSettings');
            Route::get('/calculate', [ManagerController::class, 'calculateSalary'])->name('calculate');
            Route::post('/save', [ManagerController::class, 'saveSalary'])->name('save');
            Route::get('/history', [ManagerController::class, 'history'])->name('history');
            Route::get('/export/{month}/{year}', [ManagerController::class, 'exportSalary'])->name('export');
        });
    });

    // ===========================================
    // STAFF ROUTES
    // ===========================================
    Route::middleware(['role:staff'])->group(function () {

        // Staff Dashboard
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');

        // Staff Reservation Management
        Route::prefix('staff/reservations')->name('staff.reservations.')->group(function () {
            Route::get('/', [StaffController::class, 'dash'])->name('index');
            Route::get('/{tableId}', [StaffController::class, 'show'])->name('show');
            Route::post('/{tableId}/checkin', [StaffController::class, 'checkin'])->name('checkin');
            Route::delete('/{tableId}/cancel', [StaffController::class, 'cancel'])->name('cancel');
            Route::get('/search/quick', [StaffController::class, 'quickSearch'])->name('quick-search');
        });

        // Staff Invoice Management
        Route::prefix('staff/invoices')->name('staff.invoices.')->group(function () {
            Route::get('/{id}/payment', [StaffController::class, 'payment'])->name('payment');
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::post('/store', [StaffController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::patch('/{id}/quick-clean', [StaffController::class, 'quickClean'])->name('quick-clean');
            Route::post('/{id}/add-dish', [StaffController::class, 'addDish'])->name('addDish');
            Route::patch('/{id}/finish', [StaffController::class, 'finishAndCleanTable'])->name('finish');
            Route::post('/{id}/add-dish-with-variant', [StaffController::class, 'addDishWithVariant'])->name('addDishWithVariant');
            Route::post('/{invoice}/send-to-kitchen', [StaffController::class, 'sendToKitchen'])->name('sendToKitchen');
            Route::post('/{id}/increase-item', [StaffController::class, 'increaseItem'])->name('increaseItem');
            Route::post('/{id}/decrease-item', [StaffController::class, 'decreaseItem'])->name('decreaseItem');
            Route::delete('/{invoice_id}/items/{item_id}/remove', [StaffController::class, 'removeItem'])->name('removeItem');
            Route::get('/{id}/print', [StaffController::class, 'print'])->name('print');
            Route::delete('/{id}/delete', [StaffController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/checkout', [StaffController::class, 'checkout'])->name('checkout');
            Route::get('/check-payment/{invoice_id}', [StaffController::class, 'checkPayment'])->name('checkPayment');
        });

        // Staff Table Management
        Route::get('/staff/get-tables/{area_id}', [StaffController::class, 'getTables'])->name('staff.getTables');
    });

    // ===========================================
    // CHEF ROUTES
    // ===========================================
    Route::middleware(['role:chef'])->group(function () {
        Route::get('/chef', [ChefController::class, 'index'])->name('chef.dashboard');
        Route::post('/chef/confirm-order/{invoice}', [ChefController::class, 'confirmOrder'])->name('chef.confirmOrder');
    });

    // ===========================================
    // CUSTOMER ROUTES
    // ===========================================
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
        Route::get('/select-table', [CustomerController::class, 'selectTable'])->name('customer.select-table');
        Route::get('/reserve-table/{tableId}', [CustomerController::class, 'reserveTable'])->name('customer.reserve-table');
        Route::get('/floor-plan/{floor?}', [CustomerController::class, 'floorPlan'])->name('customer.floor-plan');
        Route::get('/reservations', [CustomerController::class, 'myReservations'])->name('customer.reservations');
        Route::post('/reservations', [CustomerController::class, 'makeReservation'])->name('customer.reservations.store');
        Route::delete('/reservations/{tableId}', [CustomerController::class, 'cancelReservation'])->name('customer.reservations.cancel');
    });
});
