<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SalarySetting;
use App\Models\SalaryRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    // Hiển thị cài đặt lương theo role
    public function settings()
    {
        $settings = SalarySetting::all()->keyBy('role');
        $roles = ['manager', 'staff', 'chef', 'cashier'];

        return view('manager.salary.settings', compact('settings', 'roles'));
    }

    // Cập nhật cài đặt lương
    public function updateSettings(Request $request)
    {
        // Validation cho phép để trống (nullable)
        $request->validate([
            'settings.*.base_salary' => 'nullable|numeric|min:0|max:999999999',
            'settings.*.hourly_rate' => 'nullable|numeric|min:0|max:999999',
            'settings.*.required_hours_per_month' => 'nullable|integer|min:0|max:500',
            'settings.*.overtime_rate' => 'nullable|numeric|min:0|max:999999',
        ], [
            'settings.*.base_salary.numeric' => 'Lương cơ bản phải là số',
            'settings.*.hourly_rate.numeric' => 'Lương theo giờ phải là số',
            'settings.*.required_hours_per_month.integer' => 'Số giờ chuẩn phải là số nguyên',
            'settings.*.overtime_rate.numeric' => 'Lương tăng ca phải là số',
        ]);

        try {
            foreach ($request->settings as $role => $data) {
                // Parse số từ format VN và xử lý giá trị trống
                $baseSalary = !empty($data['base_salary']) ?
                    (float) str_replace(['.', ','], ['', '.'], $data['base_salary']) : 0;

                $hourlyRate = !empty($data['hourly_rate']) ?
                    (float) str_replace(['.', ','], ['', '.'], $data['hourly_rate']) : 0;

                $overtimeRate = !empty($data['overtime_rate']) ?
                    (float) str_replace(['.', ','], ['', '.'], $data['overtime_rate']) : 0;

                $requiredHours = !empty($data['required_hours_per_month']) ?
                    (int) $data['required_hours_per_month'] : 240;

                SalarySetting::updateOrCreate(
                    ['role' => $role],
                    [
                        'base_salary' => $baseSalary,
                        'hourly_rate' => $hourlyRate,
                        'required_hours_per_month' => $requiredHours,
                        'overtime_rate' => $overtimeRate,
                    ]
                );
            }

            return back()->with('success', 'Cài đặt lương đã được cập nhật thành công!');

        } catch (\Exception $e) {
            Log::error('Error updating salary settings: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage());
        }
    }

    // Tính lương cho tháng cụ thể
    public function calculateSalary(Request $request)
    {
        $month = $request->month ?? date('n');
        $year = $request->year ?? date('Y');

        $users = User::whereIn('role', ['manager', 'staff', 'chef', 'cashier'])->get();
        $results = [];

        foreach ($users as $user) {
            $salaryData = $this->calculateUserSalary($user, $month, $year);
            $results[] = $salaryData;
        }

        return view('manager.salary.calculate', compact('results', 'month', 'year'));
    }

    // Tính lương cho 1 user cụ thể
    // private function calculateUserSalary($user, $month, $year)
    // {
    //     // Lấy cài đặt lương theo role
    //     $setting = SalarySetting::where('role', $user->role)->first();
    //     if (!$setting) {
    //         return [
    //             'user' => $user,
    //             'error' => 'Chưa cài đặt lương cho role này'
    //         ];
    //     }

    //     // Tính số ngày trong tháng
    //     $daysInMonth = Carbon::create($year, $month)->daysInMonth;
    //     $startDate = Carbon::create($year, $month, 1)->startOfDay();
    //     $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay();

    //     // Lấy dữ liệu điểm danh trong tháng
    //     $attendanceData = $this->getAttendanceData($user->id, $startDate, $endDate);

    //     // Tính toán các thành phần lương
    //     $baseSalary = $setting->base_salary;
    //     $totalHours = $attendanceData['total_hours'];
    //     $daysWorked = $attendanceData['days_worked'];

    //     // Tính lương theo giờ
    //     $hourlySalary = $totalHours * $setting->hourly_rate;

    //     // Tính lương tăng ca (nếu vượt quá giờ chuẩn)
    //     $overtimeHours = max(0, $totalHours - $setting->required_hours_per_month);
    //     $overtimeSalary = $overtimeHours * $setting->overtime_rate;

    //     // Tổng lương
    //     $totalSalary = $baseSalary + $hourlySalary + $overtimeSalary;

    //     return [
    //         'user' => $user,
    //         'setting' => $setting,
    //         'base_salary' => $baseSalary,
    //         'hourly_salary' => $hourlySalary,
    //         'overtime_salary' => $overtimeSalary,
    //         'total_salary' => $totalSalary,
    //         'total_hours' => $totalHours,
    //         'overtime_hours' => $overtimeHours,
    //         'days_worked' => $daysWorked,
    //         'attendance_details' => $attendanceData['details']
    //     ];
    // }
    // Trong SalaryController.php
    private function calculateUserSalary($user, $month, $year)
    {
        $setting = SalarySetting::where('role', $user->role)->first();
        if (!$setting) {
            return [
                'user' => $user,
                'error' => 'Chưa cài đặt lương cho role này'
            ];
        }

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = Carbon::create($year, $month)->endOfMonth()->endOfDay();

        $attendanceData = $this->getAttendanceData($user->id, $startDate, $endDate);

        $baseSalary = $setting->base_salary;
        $totalHours = $attendanceData['total_hours'];
        $daysWorked = $attendanceData['days_worked'];

        // FIXED: Tính lương theo giờ ĐÚNG
        $hourlySalary = $totalHours * $setting->hourly_rate;

        // Tính lương tăng ca
        $overtimeHours = max(0, $totalHours - $setting->required_hours_per_month);
        $overtimeSalary = $overtimeHours * $setting->overtime_rate;

        // FIXED: Tổng lương = Lương cơ bản + Lương theo giờ + Lương tăng ca
        $totalSalary = $baseSalary + $hourlySalary + $overtimeSalary;

        // Debug log
        Log::info("Salary calculation for {$user->name}: Base={$baseSalary}, Hours={$totalHours}, HourlyRate={$setting->hourly_rate}, HourlySalary={$hourlySalary}, Total={$totalSalary}");

        return [
            'user' => $user,
            'setting' => $setting,
            'base_salary' => $baseSalary,
            'hourly_salary' => $hourlySalary,
            'overtime_salary' => $overtimeSalary,
            'total_salary' => $totalSalary,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'days_worked' => $daysWorked,
            'attendance_details' => $attendanceData['details']
        ];
    }

    // Lấy dữ liệu điểm danh của user trong khoảng thời gian
    private function getAttendanceData($userId, $startDate, $endDate)
    {
        // Lấy tất cả bản ghi điểm danh trong tháng
        $attendanceRecords = DB::table('users')
            ->select('check_in_time', 'check_out_time', 'check_day', 'shift')
            ->where('id', $userId)
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->whereBetween('check_day', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $totalHours = 0;
        $daysWorked = 0;
        $details = [];

        foreach ($attendanceRecords as $record) {
            if ($record->check_in_time && $record->check_out_time) {
                $checkIn = Carbon::parse($record->check_in_time)->setTimezone('Asia/Ho_Chi_Minh');
                $checkOut = Carbon::parse($record->check_out_time)->setTimezone('Asia/Ho_Chi_Minh');

                // Tính chính xác số giờ (bao gồm phút)
                $hoursWorked = $checkIn->diffInMinutes($checkOut) / 60;

                $totalHours += $hoursWorked;
                $daysWorked++;

                $details[] = [
                    'date' => $record->check_day,
                    'check_in' => $checkIn->format('H:i'),
                    'check_out' => $checkOut->format('H:i'),
                    'hours' => round($hoursWorked, 2),
                    'shift' => $record->shift
                ];
            }
        }

        return [
            'total_hours' => round($totalHours, 2),
            'days_worked' => $daysWorked,
            'details' => $details
        ];
    }

    // Lưu bảng lương
    public function saveSalary(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $salaryData = $request->salary_data;

        foreach ($salaryData as $userId => $data) {
            SalaryRecord::updateOrCreate(
                [
                    'user_id' => $userId,
                    'month' => $month,
                    'year' => $year
                ],
                [
                    'base_salary' => $data['base_salary'],
                    'hourly_salary' => $data['hourly_salary'],
                    'overtime_salary' => $data['overtime_salary'],
                    'bonus' => $data['bonus'] ?? 0,
                    'deduction' => $data['deduction'] ?? 0,
                    'total_salary' => $data['total_salary'],
                    'total_hours_worked' => $data['total_hours'],
                    'overtime_hours' => $data['overtime_hours'],
                    'days_worked' => $data['days_worked'],
                    'note' => $data['note'] ?? null
                ]
            );
        }

        return back()->with('success', 'Bảng lương đã được lưu!');
    }

    // Trong SalaryController.php
    public function history(Request $request)
    {
        $query = SalaryRecord::with('user')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        // Filter by multiple months
        if ($request->has('month') && !empty($request->month)) {
            $months = is_array($request->month) ? $request->month : [$request->month];
            $query->whereIn('month', array_filter($months));
        }

        // Filter by multiple years
        if ($request->has('year') && !empty($request->year)) {
            $years = is_array($request->year) ? $request->year : [$request->year];
            $query->whereIn('year', array_filter($years));
        }

        // Filter by specific user
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $records = $query->paginate(20);

        // Get all users for filter dropdown
        $users = User::whereIn('role', ['manager', 'staff', 'chef', 'cashier'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manager.salary.history', compact('records', 'users'));
    }

    // Export filtered data
    public function exportFiltered(Request $request)
    {
        $query = SalaryRecord::with('user');

        // Apply same filters as history method
        if ($request->has('month') && !empty($request->month)) {
            $months = is_array($request->month) ? $request->month : [$request->month];
            $query->whereIn('month', array_filter($months));
        }

        if ($request->has('year') && !empty($request->year)) {
            $years = is_array($request->year) ? $request->year : [$request->year];
            $query->whereIn('year', array_filter($years));
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $records = $query->get();

        // Export logic here
        return $this->generateExcel($records, 'filtered_salary_records');
    }

    // Export all data
    public function exportAll()
    {
        $records = SalaryRecord::with('user')->get();
        return $this->generateExcel($records, 'all_salary_records');
    }
}