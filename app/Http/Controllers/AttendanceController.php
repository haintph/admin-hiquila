<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AttendanceController extends Controller
{
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

        $currentTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');

        if ($request->status == 'active' && $user->status !== 'active') {

            // Kiểm tra ca làm việc
            $canCheckIn = $this->validateShiftTime($user->shift, $currentTime);

            if (!$canCheckIn['allowed']) {
                return back()->with('error', $canCheckIn['message']);
            }
        }

        if ($user->status !== 'terminated') {
            $user->status = $request->status;

            if ($request->status == 'active' && !$user->check_in_time) {
                $user->check_in_time = $currentTime;
                $user->check_day = $currentTime->toDateString();
            }

            $user->save();
        }

        return redirect()->route('attendance.list')->with('success', 'Cập nhật điểm danh thành công!');
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
                'message' => " Quá sớm để điểm danh! Hiện tại: {$timeString}. {$shiftInfo['name']} bắt đầu từ " .
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

        // Kiểm tra nếu nhân viên đã check-in trước đó
        if ($user->check_in_time) {
            $user->check_out_time = now(); // Lưu lại thời gian check-out

            // Tính toán số giờ làm việc và lưu vào database
            if ($user->check_in_time && $user->check_out_time) {
                $checkInTime = \Carbon\Carbon::parse($user->check_in_time);
                $checkOutTime = \Carbon\Carbon::parse($user->check_out_time);
                $workHours = $checkInTime->diffInHours($checkOutTime);
                $user->workHours = $workHours;
            }
        }

        // Đặt lại trạng thái thành "inactive"
        // $user->status = 'inactive';

        $user->save();

        return back()->with('success', 'Check-out thành công! Thời gian đã được lưu.');
    }

    public function resetAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Đặt trạng thái về "vắng mặt"
        $user->status = 'inactive';

        // Reset thời gian check-in, check-out, ngày check-in và số giờ làm việc
        $user->check_in_time = null;
        $user->check_out_time = null;
        $user->check_day = null;
        $user->workHours = 0;

        $user->save();

        return back()->with('success', 'Reset thành công! Trạng thái và thời gian đã được đặt lại.');
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
}