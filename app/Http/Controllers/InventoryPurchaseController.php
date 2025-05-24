<?php

namespace App\Http\Controllers;

use App\Models\InventoryPurchase;
use Illuminate\Http\Request;

class InventoryPurchaseController extends Controller
{
    public function index()
    {
        $purchases = InventoryPurchase::with('supplier')->get();
        return view('purchase.index', compact('purchases'));
    }

    public function store(Request $request)
    {
        InventoryPurchase::create($request->all());
        return redirect()->route('purchase.index')->with('success', 'Đơn nhập hàng đã được thêm!');
    }
}
