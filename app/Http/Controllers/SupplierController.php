<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        Supplier::create($request->all());
        return redirect()->route('supplier.index')->with('success', 'Nhà cung cấp đã được thêm!');
    }
}
