<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AttendanceController extends Controller
{
    public function list()
    {
        $staffs = User::where('role', 'staff')
            ->whereIn('status', ['active', 'inactive'])
            ->paginate(10);

        return view('manager.attendance.list', compact('staffs'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'staff')->firstOrFail();
    
        // Kiểm tra nếu nhân viên đang "có mặt" thì không cho phép thay đổi trạng thái
        if ($user->status === 'active') {
            return back()->with('error', 'Không thể thay đổi trạng thái của nhân viên đang có mặt!');
        }
    
        if ($user->status !== 'terminated') {
            $user->status = $request->status;
    
            // Nếu nhân viên điểm danh lần đầu, lưu thời gian check-in
            if ($request->status == 'active' && !$user->check_in_time) {
                $user->check_in_time = now();
            }
    
            $user->save();
        }
    
        return redirect()->route('attendance.list')->with('success', 'Cập nhật điểm danh thành công!');
    }

    public function checkOut(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Kiểm tra nếu nhân viên đã check-in trước đó
        if ($user->check_in_time) {
            $user->check_out_time = now(); // Lưu lại thời gian check-out
        }

        // Đặt lại trạng thái thành "inactive"
        // $user->status = '';

        $user->save();

        return back()->with('success', 'Check-out thành công! Thời gian đã được lưu.');
    }

    public function resetAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Đặt trạng thái về "vắng mặt"
        $user->status = 'inactive';

        // Reset thời gian check-in và check-out
        $user->check_in_time = null;
        $user->check_out_time = null;

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
        $user = User::findOrFail($id);
        $user->shift = $request->shift;
        $user->save();

        return back()->with('success', 'Ca làm việc đã được cập nhật.');
    }
}
