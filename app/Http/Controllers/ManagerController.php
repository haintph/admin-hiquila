<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\SalarySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagerController extends Controller
{
    public function index()
    {
        return view('manager.index');
    }
    public function list()
    {
        // Lấy tất cả role trừ owner
        $staffs = User::whereIn('role', ['manager', 'staff', 'chef', 'cashier'])
            ->whereIn('status', ['active', 'inactive'])
            ->paginate(10);

        return view('manager.attendance.list', compact('staffs'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->whereIn('role', ['manager', 'staff', 'chef', 'cashier'])
            ->firstOrFail();

        // Lấy thời gian hiện tại với timezone Việt Nam
        $currentTime = Carbon::now('Asia/Ho_Chi_Minh');

        if ($request->status == 'active' && $user->status !== 'active') {
            // Kiểm tra ca làm việc
            $canCheckIn = $this->validateShiftTime($user->shift, $currentTime);

            if (!$canCheckIn['allowed']) {
                return back()->with('error', $canCheckIn['message']);
            }
        }

        if ($user->status !== 'terminated') {
            $user->status = $request->status;

            // Cập nhật thời gian check-in là thời điểm hiện tại khi chuyển sang active
            if ($request->status == 'active' && !$user->check_in_time) {
                $user->check_in_time = $currentTime->toDateTimeString(); // Lưu đầy đủ datetime
                $user->check_day = $currentTime->toDateString(); // Lưu ngày
                
                // Log để theo dõi
                Log::info("User {$user->name} checked in at: " . $currentTime->format('Y-m-d H:i:s'));
            }

            $user->save();
        }

        return redirect()->route('manager.attendance.list')->with('success', 'Cập nhật điểm danh thành công!');
    }

    private function validateShiftTime($shift, $currentTime)
    {
        $hour = $currentTime->hour;
        $minute = $currentTime->minute;
        $timeString = $currentTime->format('H:i');

        // Định nghĩa khung giờ (có thể cho phép check-in sớm 15 phút)
        $shifts = [
            'morning' => [
                'start' => 5.75,  // 5:45 (cho phép check-in sớm 15 phút)
                'end' => 14.25,   // 14:15 (cho phép check-in muộn 15 phút)
                'name' => 'Ca sáng (6:00 - 14:00)'
            ],
            'afternoon' => [
                'start' => 13.75, // 13:45
                'end' => 22.25,   // 22:15
                'name' => 'Ca chiều (14:00 - 22:00)'
            ],
            'full_day' => [
                'start' => 5.75,  // 5:45
                'end' => 22.25,   // 22:15
                'name' => 'Cả ngày (6:00 - 22:00)'
            ]
        ];

        $currentDecimal = $hour + ($minute / 60);
        $shiftInfo = $shifts[$shift] ?? $shifts['morning'];

        if ($currentDecimal < $shiftInfo['start']) {
            return [
                'allowed' => false,
                'message' => "Quá sớm để điểm danh! Hiện tại: {$timeString}. {$shiftInfo['name']} bắt đầu từ " .
                    $this->decimalToTime($shiftInfo['start'])
            ];
        }

        if ($currentDecimal > $shiftInfo['end']) {
            return [
                'allowed' => false,
                'message' => "Quá muộn để điểm danh! Hiện tại: {$timeString}. {$shiftInfo['name']} đã kết thúc lúc " .
                    $this->decimalToTime($shiftInfo['end'])
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    private function decimalToTime($decimal)
    {
        $hours = floor($decimal);
        $minutes = ($decimal - $hours) * 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function checkOut(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Lấy thời gian hiện tại với timezone Việt Nam
        $currentTime = Carbon::now('Asia/Ho_Chi_Minh');

        // Kiểm tra nếu nhân viên đã check-in trước đó
        if ($user->check_in_time) {
            // Lưu thời gian check-out là thời điểm hiện tại
            $user->check_out_time = $currentTime->toDateTimeString();

            // Tính toán số giờ làm việc chính xác
            if ($user->check_in_time && $user->check_out_time) {
                $checkInTime = Carbon::parse($user->check_in_time)->setTimezone('Asia/Ho_Chi_Minh');
                $checkOutTime = Carbon::parse($user->check_out_time)->setTimezone('Asia/Ho_Chi_Minh');
                
                // Tính chính xác số giờ (bao gồm phút và giây)
                $workMinutes = $checkInTime->diffInMinutes($checkOutTime);
                $workHours = round($workMinutes / 60, 2); // Làm tròn 2 chữ số thập phân
                
                $user->workHours = $workHours;
                
                // Log để theo dõi
                Log::info("User {$user->name} checked out at: " . $currentTime->format('Y-m-d H:i:s') . ", worked {$workHours} hours");
            }
        }

        // Có thể đặt trạng thái về inactive sau khi checkout (tuỳ chọn)
        $user->status = 'inactive';

        $user->save();

        return back()->with('success', 'Check-out thành công! Thời gian đã được lưu.');
    }

    public function resetAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentTime = Carbon::now('Asia/Ho_Chi_Minh');

        // Log trước khi reset
        Log::info("Resetting attendance for {$user->name} at " . $currentTime->format('Y-m-d H:i:s'));
        
        // Đặt trạng thái về "vắng mặt"
        $user->status = 'inactive';

        // Reset thời gian check-in, check-out, ngày check-in và số giờ làm việc
        $user->check_in_time = null;
        $user->check_out_time = null;
        $user->check_day = null;
        $user->workHours = 0;

        $user->save();

        return back()->with('success', 'Reset thành công lúc ' . $currentTime->format('H:i:s d/m/Y'));
    }

    public function updateNote(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->note = $request->note;
        $user->save();

        return back()->with('success', 'Ghi chú đã được cập nhật.');
    }

    public function updateShift(Request $request, $id)
    {
        $request->validate([
            'shift' => 'required|in:morning,afternoon,full_day'
        ]);

        $user = User::findOrFail($id);

        // Các trường hợp không cho phép thay đổi ca
        if ($user->status === 'active' && $user->check_in_time) {
            return back()->with('error', 'Nhân viên đang trong ca làm việc, không thể thay đổi!');
        }

        if ($user->check_out_time) {
            return back()->with('error', 'Ca làm việc đã hoàn thành, không thể thay đổi!');
        }

        // Reset thời gian check-in nếu có khi đổi ca (tuỳ chọn)
        if ($user->check_in_time && $user->status !== 'active') {
            $user->check_in_time = null;
            $user->check_day = null;
        }

        $user->shift = $request->shift;
        $user->save();

        return back()->with('success', 'Ca làm việc đã được cập nhật.');
    }
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
