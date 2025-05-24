@extends('admin.layouts.master')
@section('content')
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center gap-1">
                            <h4 class="card-title flex-grow-1">Danh Sách Điểm Danh Nhân Viên</h4>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover table-centered">
                                    <thead class="bg-light-subtle">
                                        <tr>
                                            <th>Avatar</th>
                                            <th>Tên Nhân Viên</th>
                                            <th>Email</th>
                                            <th>Vai Trò</th>
                                            <th>Trạng Thái</th>
                                            <th>Ca Làm Việc</th>
                                            <th>Thời Gian Check-in</th>
                                            <th>Thời Gian Check-out</th>
                                            <th>Số Giờ Làm Việc</th>
                                            <th>Hành Động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($staffs as $staff)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/' . $staff->avatar) }}" alt="Avatar" class="rounded-circle" width="50" height="50">
                                                </td>
                                                <td>{{ $staff->name }}</td>
                                                <td>{{ $staff->email }}</td>
                                                <td>
                                                    @switch($staff->role)
                                                        @case('manager')
                                                            <span class="badge bg-primary">Quản lý</span>
                                                            @break
                                                        @case('staff')
                                                            <span class="badge bg-info">Nhân viên</span>
                                                            @break
                                                        @case('chef')
                                                            <span class="badge bg-warning">Đầu bếp</span>
                                                            @break
                                                        @case('cashier')
                                                            <span class="badge bg-success">Thu ngân</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($staff->role) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <form action="{{ route('attendance.update', $staff->id) }}" method="POST">
                                                        @csrf
                                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                                            <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Có mặt</option>
                                                            <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Vắng mặt</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                
                                                <td>
                                                    <form action="{{ route('attendance.updateShift', $staff->id) }}" method="POST">
                                                        @csrf
                                                        <select name="shift" class="form-select" onchange="this.form.submit()">
                                                            <option value="morning" {{ $staff->shift == 'morning' ? 'selected' : '' }}>Ca sáng</option>
                                                            <option value="afternoon" {{ $staff->shift == 'afternoon' ? 'selected' : '' }}>Ca chiều</option>
                                                            <option value="full_day" {{ $staff->shift == 'full_day' ? 'selected' : '' }}>Cả ngày</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    @if ($staff->check_in_time)
                                                        {{ \Carbon\Carbon::parse($staff->check_in_time)->format('H:i d/m/Y') }}
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($staff->check_out_time)
                                                        {{ \Carbon\Carbon::parse($staff->check_out_time)->format('H:i d/m/Y') }}
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($staff->check_in_time && $staff->check_out_time)
                                                        @php
                                                            $workHours = \Carbon\Carbon::parse($staff->check_in_time)->diffInHours(\Carbon\Carbon::parse($staff->check_out_time));
                                                            $workMinutes = \Carbon\Carbon::parse($staff->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($staff->check_out_time)) % 60;
                                                        @endphp
                                                        {{ $workHours }} giờ {{ $workMinutes }} phút
                                                    @else
                                                        --
                                                    @endif
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-1">
                                                        @if (!$staff->check_out_time)
                                                            <form action="{{ route('attendance.checkOut', $staff->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning btn-sm">Check Out</button>
                                                            </form>
                                                        @endif
                                                        
                                                        <!-- Nút Reset -->
                                                        <form action="{{ route('attendance.reset', $staff->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn reset điểm danh?')">Reset</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            {{ $staffs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection