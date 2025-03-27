<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Table;
use App\Models\Dish;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('table')->paginate(10);

        // Tính toán số lượng hóa đơn theo trạng thái
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::where('status', 'Đang chuẩn bị')->count();
        $paidInvoices = Invoice::where('status', 'Đã thanh toán')->count();

        return view('admin.invoices.index', compact('invoices', 'totalInvoices', 'pendingInvoices', 'paidInvoices'));
    }

    public function create()
    {
        $areas = Area::with(['tables' => function ($query) {
            $query->where('status', 'Trống');
        }])->get();
        // dd($areas->toArray());

        $dishes = Dish::all();

        return view('admin.invoices.create', compact('areas', 'dishes'));
    }
    public function getTables($area_id)
    {
        $tables = Table::where('area_id', $area_id)->where('status', 'Trống')->get();
        return response()->json($tables);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,table_id',
        ]);

        // Tạo hóa đơn mới
        $invoice = Invoice::create([
            'table_id' => $request->table_id,
            'total_price' => 0,
            'status' => 'Đang chuẩn bị',
        ]);

        // Cập nhật trạng thái bàn thành "Đang sử dụng"
        Table::where('table_id', $request->table_id)->update(['status' => 'Đang phục vụ']);

        return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã được tạo!');
    }


    public function edit($id)
    {
        $invoice = Invoice::with('items.dish')->findOrFail($id);
        $dishes = Dish::all();
        return view('admin.invoices.edit', compact('invoice', 'dishes'));
    }

    public function addDish(Request $request, $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $dish = Dish::findOrFail($request->dish_id);

        // Kiểm tra xem số lượng món ăn có đủ không
        if ($request->quantity > $dish->stock) {
            return redirect()->back()->with('error', $dish->name . ' không đủ hàng trong kho!');
        }

        // Trừ kho ngay khi thêm món
        $dish->decrement('stock', $request->quantity);

        // Thêm món vào hóa đơn
        InvoiceDetail::create([
            'invoice_id' => $invoice_id,
            'dish_id' => $dish->id,
            'quantity' => $request->quantity,
            'price' => $dish->price, // Lưu giá tại thời điểm đó
        ]);

        // Cập nhật tổng tiền hóa đơn
        $total_price = InvoiceDetail::where('invoice_id', $invoice_id)
            ->sum(DB::raw('quantity * price'));
        $invoice->update(['total_price' => $total_price]);

        return redirect()->back()->with('success', 'Thêm món ăn thành công!');
    }

    public function checkout($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Cập nhật trạng thái hóa đơn và bàn
        $invoice->update(['status' => 'Đã thanh toán']);
        Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

        return redirect()->route('invoices.index')->with('success', 'Thanh toán thành công!');
    }


    public function print($id)
    {
        $invoice = Invoice::with('items.dish', 'table')->findOrFail($id);

        return view('admin.invoices.print', compact('invoice'));
    }
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Cập nhật trạng thái bàn về "Trống" nếu hóa đơn đang phục vụ
        Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

        // Xóa hóa đơn
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã bị xóa!');
    }
    public function payment($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Mã ngân hàng và số tài khoản
        $bankCode = "BIDV"; // Đổi thành ngân hàng bạn dùng
        $accountNumber = "1234567890"; // Nhập số tài khoản ngân hàng của bạn

        // Số tiền và thông tin hóa đơn
        $amount = number_format($invoice->total_price, 2, '.', '');
        $addInfo = "Thanh toan hoa don #" . $invoice->invoice_id;

        // Tạo link QR
        $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-compact.png?amount={$amount}&addInfo=" . urlencode($addInfo);

        return view('admin.invoices.qr_payment', compact('invoice', 'qrUrl'));
    }

    public function confirmPayment($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Cập nhật trạng thái hóa đơn
        $invoice->update(['status' => 'Đã thanh toán']);

        // Cập nhật trạng thái bàn ăn
        Table::where('table_id', $invoice->table_id)->update(['status' => 'Trống']);

        return redirect()->route('invoices.index')->with('success', 'Hóa đơn đã được xác nhận thanh toán!');
    }
    public function checkPayment($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice && $invoice->status == 'Đã thanh toán') {
            return response()->json(['paid' => true]);
        }
        return response()->json(['paid' => false]);
    }
}
