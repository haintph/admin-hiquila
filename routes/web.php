<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DishController;

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

