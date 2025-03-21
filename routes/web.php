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
//Vị trí bố cụccục
Route::resource('areas', AreaController::class);

//Phân quyền 
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Chỉ Chủ mới vào được /admin/settings
    Route::middleware(['role:Chủ'])->group(function () {
        Route::get('/settings', function () {
            return "Trang quản lý dành cho Chủ";
        })->name('admin.settings');
        Route::middleware(['role:Chủ'])->group(function () {
            Route::get('/products', [DishController::class, 'index'])->name('products.list');
        });

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

    });
    // Trang dành cho Quản lý
    Route::middleware(['role:Quản lý'])->group(function () {
        Route::get('/manager', function () {
            return "Trang dành cho Quản lý";
        });
    });
    // Trang dành cho Nhân viên
    Route::middleware(['role:Nhân viên'])->group(function () {
        Route::get('/staff', function () {
            return "Trang dành cho Nhân viên";
        });
    });
});

