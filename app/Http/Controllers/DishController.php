<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'Chủ') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        return view('admin.dish.list'); // Trả về trang list.blade.php
    }
}
