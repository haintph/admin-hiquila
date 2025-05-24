@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <!-- Tiêu đề trang -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-0">Thay Đổi Mật Khẩu</h3>
            </div>
        </div>

        <!-- Form đổi mật khẩu -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Cập Nhật Mật Khẩu</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Ảnh đại diện -->
                    <div class="col-lg-4 text-center">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="img-fluid rounded mb-3" style="max-width: 250px;">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="img-fluid rounded mb-3" style="max-width: 250px;">
                        @endif
                    </div>
                    <!-- Form đổi mật khẩu -->
                    <div class="col-lg-8">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('profile.updatePassword') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required>
                                @error('new_password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nhập lại mật khẩu mới</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Cập Nhật Mật Khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
