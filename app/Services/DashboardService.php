<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get revenue statistics for dashboard
     */
    public function getRevenueStats($fromDate, $toDate, $areaId = null)
    {
        $baseQuery = $this->buildBaseQuery($fromDate, $toDate, $areaId);
        
        $totalRevenue = (clone $baseQuery)
            ->whereIn('invoices.status', ['Hoàn thành', 'Đã thanh toán'])
            ->sum('invoices.total_price');

        $totalOrders = (clone $baseQuery)->count();
        
        $cancelledOrders = DB::table('invoices')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('status', 'Hủy đơn')
            ->count();

        return [
            'total_revenue' => $totalRevenue ?: 0,
            'total_orders' => $totalOrders ?: 0,
            'cancelled_orders' => $cancelledOrders ?: 0,
            'avg_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0
        ];
    }

    /**
     * Get top selling dishes
     */
    public function getTopDishes($fromDate, $toDate, $areaId = null, $limit = 10)
    {
        $query = DB::table('invoice_details')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
            ->join('dishes', 'invoice_details.dish_id', '=', 'dishes.id')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate])
            ->whereIn('invoices.status', ['Hoàn thành', 'Đã thanh toán']);

        if ($areaId) {
            $query->join('tables', 'invoices.table_id', '=', 'tables.table_id')
                  ->where('tables.area_id', $areaId);
        }

        return $query->select(
                'dishes.name',
                DB::raw('SUM(invoice_details.quantity) as total_quantity'),
                DB::raw('SUM(invoice_details.price * invoice_details.quantity) as total_revenue')
            )
            ->groupBy('dishes.id', 'dishes.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get revenue by area - Có thêm thông tin tầng
     */
    public function getRevenueByArea($fromDate, $toDate)
    {
        return DB::table('invoices')
            ->join('tables', 'invoices.table_id', '=', 'tables.table_id')
            ->join('areas', 'tables.area_id', '=', 'areas.area_id')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate])
            ->whereIn('invoices.status', ['Hoàn thành', 'Đã thanh toán'])
            ->select(
                'areas.name',
                'areas.code',
                'areas.floor',
                DB::raw('SUM(invoices.total_price) as total_revenue'),
                DB::raw('COUNT(invoices.invoice_id) as total_orders')
            )
            ->groupBy('areas.area_id', 'areas.name', 'areas.code', 'areas.floor')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Get category performance
     */
    public function getCategoryPerformance($fromDate, $toDate, $areaId = null)
    {
        $query = DB::table('invoice_details')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
            ->join('dishes', 'invoice_details.dish_id', '=', 'dishes.id')
            ->join('sub_categories', 'dishes.sub_category_id', '=', 'sub_categories.id')
            ->join('categories', 'sub_categories.parent_id', '=', 'categories.id')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate])
            ->whereIn('invoices.status', ['Hoàn thành', 'Đã thanh toán']);

        if ($areaId) {
            $query->join('tables', 'invoices.table_id', '=', 'tables.table_id')
                  ->where('tables.area_id', $areaId);
        }

        return $query->select(
                'categories.name',
                DB::raw('SUM(invoice_details.quantity) as total_quantity'),
                DB::raw('SUM(invoice_details.price * invoice_details.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT dishes.id) as unique_dishes')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_quantity', 'desc')
            ->get();
    }

    /**
     * Get table usage statistics - Có thêm thông tin tầng
     */
    public function getTableUsage($fromDate, $toDate, $areaId = null)
    {
        $query = DB::table('invoices')
            ->join('tables', 'invoices.table_id', '=', 'tables.table_id')
            ->join('areas', 'tables.area_id', '=', 'areas.area_id')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate])
            ->whereIn('invoices.status', ['Hoàn thành', 'Đã thanh toán']);

        if ($areaId) {
            $query->where('tables.area_id', $areaId);
        }

        return $query->select(
                'tables.table_number',
                'tables.capacity',
                'tables.table_type',
                'areas.name as area_name',
                'areas.code as area_code',
                'areas.floor',
                DB::raw('COUNT(invoices.invoice_id) as usage_count'),
                DB::raw('SUM(invoices.total_price) as total_revenue'),
                DB::raw('AVG(invoices.total_price) as avg_revenue_per_use')
            )
            ->groupBy(
                'tables.table_id', 
                'tables.table_number', 
                'tables.capacity',
                'tables.table_type',
                'areas.name', 
                'areas.code',
                'areas.floor'
            )
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    /**
     * Get daily revenue data for charts
     */
    public function getDailyRevenue($fromDate, $toDate, $areaId = null)
    {
        $query = DB::table('invoices')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('status', ['Hoàn thành', 'Đã thanh toán']);

        if ($areaId) {
            $query->join('tables', 'invoices.table_id', '=', 'tables.table_id')
                  ->where('tables.area_id', $areaId);
        }

        return $query->select(
                DB::raw('DATE(invoices.created_at) as date'),
                DB::raw('SUM(invoices.total_price) as revenue'),
                DB::raw('COUNT(invoices.invoice_id) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get monthly revenue data for charts
     */
    public function getMonthlyRevenue($year = null, $areaId = null)
    {
        $year = $year ?: now()->year;
        
        $query = DB::table('invoices')
            ->whereYear('created_at', $year)
            ->whereIn('status', ['Hoàn thành', 'Đã thanh toán']);

        if ($areaId) {
            $query->join('tables', 'invoices.table_id', '=', 'tables.table_id')
                  ->where('tables.area_id', $areaId);
        }

        return $query->select(
                DB::raw('MONTH(invoices.created_at) as month'),
                DB::raw('SUM(invoices.total_price) as revenue'),
                DB::raw('COUNT(invoices.invoice_id) as orders_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Build base query with common filters
     */
    private function buildBaseQuery($fromDate, $toDate, $areaId = null)
    {
        $query = DB::table('invoices')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate])
            ->where('invoices.status', '!=', 'Hủy đơn');

        if ($areaId) {
            $query->join('tables', 'invoices.table_id', '=', 'tables.table_id')
                  ->where('tables.area_id', $areaId);
        }

        return $query;
    }

    /**
     * Format data for chart display
     */
    public function formatChartData($data, $labelField, $valueField, $dateFormat = null)
    {
        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $label = $item->{$labelField};
            
            if ($dateFormat && $labelField === 'date') {
                $label = Carbon::parse($label)->format($dateFormat);
            }
            
            $labels[] = $label;
            $values[] = $item->{$valueField};
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }
}