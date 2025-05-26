<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashBoardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        try {
            // Lấy tham số filter
            $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->get('to_date', now()->format('Y-m-d'));
            $areaId = $request->get('area_id');

            // Lấy danh sách khu vực
            $areas = DB::table('areas')->orderBy('name')->get();

            // Lấy thống kê tổng quan
            $stats = $this->dashboardService->getRevenueStats($fromDate, $toDate, $areaId);
            
            // Lấy dữ liệu cho các bảng và biểu đồ
            $topDishes = $this->dashboardService->getTopDishes($fromDate, $toDate, $areaId);
            $areaRevenue = $this->dashboardService->getRevenueByArea($fromDate, $toDate);
            $categoryData = $this->dashboardService->getCategoryPerformance($fromDate, $toDate, $areaId);
            $tableUsage = $this->dashboardService->getTableUsage($fromDate, $toDate, $areaId);
            
            // Dữ liệu cho biểu đồ
            $dailyRevenue = $this->dashboardService->getDailyRevenue($fromDate, $toDate, $areaId);

            // Chuẩn bị dữ liệu cho view
            $dailyLabels = [];
            $dailyRevenueData = [];
            if ($dailyRevenue && count($dailyRevenue) > 0) {
                foreach ($dailyRevenue as $day) {
                    $dailyLabels[] = \Carbon\Carbon::parse($day->date)->format('d/m');
                    $dailyRevenueData[] = (float) $day->revenue;
                }
            }

            $areaLabels = [];
            $areaRevenueData = [];
            if ($areaRevenue && count($areaRevenue) > 0) {
                foreach ($areaRevenue as $area) {
                    $floorText = $area->floor ? " (T{$area->floor})" : "";
                    $areaLabels[] = $area->name . $floorText;
                    $areaRevenueData[] = (float) $area->total_revenue;
                }
            }

            $categoryLabels = [];
            $categoryQuantityData = [];
            if ($categoryData && count($categoryData) > 0) {
                foreach ($categoryData as $category) {
                    $categoryLabels[] = $category->name;
                    $categoryQuantityData[] = (int) $category->total_quantity;
                }
            }

            return view('admin.dashboard.index', compact(
                'areas',
                'stats',
                'topDishes',
                'tableUsage',
                'areaRevenue',
                'categoryData',
                'dailyLabels',
                'dailyRevenueData',
                'areaLabels',
                'areaRevenueData',
                'categoryLabels',
                'categoryQuantityData',
                'fromDate',
                'toDate'
            ));

        } catch (\Exception $e) {
            // Log lỗi và hiển thị thông báo
            Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Trả về view với dữ liệu mặc định
            return view('admin.dashboard.index', [
                'areas' => collect([]),
                'stats' => [
                    'total_revenue' => 0,
                    'total_orders' => 0,
                    'cancelled_orders' => 0,
                    'avg_order_value' => 0
                ],
                'topDishes' => collect([]),
                'tableUsage' => collect([]),
                'areaRevenue' => collect([]),
                'categoryData' => collect([]),
                'dailyLabels' => [],
                'dailyRevenueData' => [],
                'areaLabels' => [],
                'areaRevenueData' => [],
                'categoryLabels' => [],
                'categoryQuantityData' => [],
                'fromDate' => now()->startOfMonth()->format('Y-m-d'),
                'toDate' => now()->format('Y-m-d')
            ])->with('error', 'Có lỗi xảy ra khi tải dashboard: ' . $e->getMessage());
        }
    }

    public function updateStats(Request $request)
    {
        // Bỏ function này vì chúng ta không cần AJAX
        return redirect()->route('admin.dashboard.index', $request->all());
    }

    public function export(Request $request)
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->input('to_date', now()->format('Y-m-d'));
            $areaId = $request->input('area_id');

            // Lấy dữ liệu để export
            $stats = $this->dashboardService->getRevenueStats($fromDate, $toDate, $areaId);
            $topDishes = $this->dashboardService->getTopDishes($fromDate, $toDate, $areaId);

            // Tạo nội dung CSV
            $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
            $csv .= "BÁO CÁO THỐNG KÊ NHÀ HÀNG\n";
            $csv .= "Từ ngày: {$fromDate} đến {$toDate}\n";
            $csv .= "Ngày xuất: " . now()->format('d/m/Y H:i:s') . "\n\n";
            
            $csv .= "TỔNG QUAN\n";
            $csv .= "Tổng doanh thu," . number_format($stats['total_revenue']) . " VNĐ\n";
            $csv .= "Tổng đơn hàng," . $stats['total_orders'] . "\n";
            $csv .= "Đơn đã hủy," . $stats['cancelled_orders'] . "\n";
            $csv .= "Giá trị đơn TB," . number_format($stats['avg_order_value']) . " VNĐ\n\n";
            
            $csv .= "TOP MÓN BÁN CHẠY\n";
            $csv .= "Tên món,Số lượng,Doanh thu\n";
            foreach ($topDishes as $dish) {
                $csv .= $dish->name . "," . $dish->total_quantity . "," . number_format($dish->total_revenue) . "\n";
            }

            $filename = 'bao-cao-thong-ke-' . $fromDate . '-to-' . $toDate . '.csv';
            
            return response($csv)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi khi xuất báo cáo: ' . $e->getMessage());
        }
    }
}