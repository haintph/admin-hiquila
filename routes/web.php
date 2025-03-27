<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DishImageController;
use App\Http\Controllers\DishVariantController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ManagerController;



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
//Đăng nhập
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


//Vị trí bố cục
Route::resource('areas', AreaController::class);

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
    // Chat
    // Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    // Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    // Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    // Route::get('/update-status', function () {
    //     if (Auth::check()) {
    //         Cache::put('user-is-online-' . Auth::id(), true, now()->addMinutes(5));
    //     }
    //     return response()->json(['status' => 'updated']);
    // });



    // Chỉ Chủ mới vào được /admin/settings
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/settings', function () {
            return "Trang quản lý dành cho Chủ";
        })->name('admin.settings');
        // Route::middleware(['role:owner'])->group(function () {
        //     Route::get('/products', [DishController::class, 'index'])->name('products.list');
        // });

        // Category management
        Route::get('category-list', [CategoryController::class, 'list'])->name('category-list');
        Route::get('category-create', [CategoryController::class, 'create'])->name('category-create');
        Route::post('category_store', [CategoryController::class, 'store'])->name('category_store');
        Route::get('category_edit/{id}', [CategoryController::class, 'edit'])->name('category_edit');
        Route::put('category_update/{id}', [CategoryController::class, 'update'])->name('category_update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('category_detail/{id}', [CategoryController::class, 'detail'])->name('category_detail');

        // 🌟 Quản lý danh mục con (sub_categories)

        Route::get('sub_category_list', [SubCategoryController::class, 'list'])->name('sub_category_list');
        Route::get('sub_category_create', [SubCategoryController::class, 'create'])->name('sub_category_create');
        Route::post('sub_category_store', [SubCategoryController::class, 'store'])->name('sub_category_store');
        Route::get('sub_category_edit/{id}', [SubCategoryController::class, 'edit'])->name('sub_category_edit');
        Route::put('sub_category_update/{id}', [SubCategoryController::class, 'update'])->name('sub_category_update');
        Route::delete('sub_category_destroy/{id}', [SubCategoryController::class, 'destroy'])->name('sub_category_destroy');
        Route::get('sub_category_detail/{id}', [SubCategoryController::class, 'detail'])->name('sub_category_detail');

        // 🌟 Quản lý món ăn ( dishes)
        Route::get('dish_list', [DishController::class, 'list'])->name('dish_list');
        Route::get('dish_create', [DishController::class, 'create'])->name('dish_create');
        Route::post('dish_store', [DishController::class, 'store'])->name('dish_store');
        Route::get('dish_edit/{id}', [DishController::class, 'edit'])->name('dish_edit');
        Route::put('dish_update/{id}', [DishController::class, 'update'])->name('dish_update');
        Route::delete('dish_destroy/{id}', [DishController::class, 'destroy'])->name('dish_destroy');
        //vảiant
        // Route::get('/dishes/{id}', [DishController::class, 'show'])->name('dishes.show');
        Route::get('/dish_detail/show/{id}', [DishController::class, 'show'])->name('dish_detail');
        Route::get('/variants/edit/{id}', [DishVariantController::class, 'edit'])->name('variants.edit');
        Route::get('/variants/create/{dish_id}', [DishVariantController::class, 'create'])->name('variants.create');
        Route::post('/variants/store', [DishVariantController::class, 'store'])->name('variants.store');
        // Route::get('dish_detail/{id}', [DishController::class, 'detail'])->name('dish_detail');

        //ablum ảnh
        Route::post('/dish/image/update/{id}', [DishController::class, 'updateImage'])->name('dish_image_update');
        Route::post('/dishes/{dish}/upload-images', [DishImageController::class, 'store'])->name('dishes.upload_images');
        Route::delete('/dish/image/delete/{id}', [DishController::class, 'deleteImage'])->name('dish_image_delete');

        // 🌟 Quản lý người dùng (users)
        Route::get('user_list', [UserController::class, 'list'])->name('user_list');
        Route::get('user_create', [UserController::class, 'create'])->name('user_create');
        Route::post('user_store', [UserController::class, 'store'])->name('user_store');
        Route::get('user_edit/{id}', [UserController::class, 'edit'])->name('user_edit');
        Route::put('user_update/{id}', [UserController::class, 'update'])->name('user_update');
        Route::delete('user_destroy/{id}', [UserController::class, 'destroy'])->name('user_destroy');
        Route::get('user_detail/{id}', [UserController::class, 'detail'])->name('user_detail');
    });

    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager', [ManagerController::class, 'index'])->name('manager.dashboard');
        Route::get('/manager/attendance', [AttendanceController::class, 'list'])->name('attendance.list');
        Route::post('/manager/attendance/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::post('/attendance/note/update/{id}', [AttendanceController::class, 'updateNote'])->name('attendance.note.update');
        Route::post('/attendance/reset/{id}', [AttendanceController::class, 'resetAttendance'])->name('attendance.reset');


        Route::post('/attendance/{id}/note', [AttendanceController::class, 'updateNote'])->name('attendance.note');
        Route::post('/attendance/{id}/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');

       

Route::post('/attendance/update-shift/{id}', [AttendanceController::class, 'updateShift'])->name('attendance.updateShift');



    });



    // 🔹 Nhân viên (Staff)
    Route::middleware(['role:staff'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');
    });
});
