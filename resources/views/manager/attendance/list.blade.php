@extends('admin.layouts.master')
@section('content')
    <div class="page-content">
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
                                                            <option value="night" {{ $staff->shift == 'night' ? 'selected' : '' }}>Ca tối</option>
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
                                                    <form action="{{ route('attendance.checkOut', $staff->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm">Check Out</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <!-- Nút Reset -->
                                                    <form action="{{ route('attendance.reset', $staff->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">Reset</button>
                                                    </form>
                                                </td>
                                                
                                                
                                                {{-- <td>
                                                    @if (!$staff->check_out_time)
                                                        <form action="{{ route('attendance.checkOut', $staff->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm">Check Out</button>
                                                        </form>
                                                    @else
                                                        <span class="text-success">Đã check-out</span>
                                                    @endif
                                                </td> --}}
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
    </div>
@endsection