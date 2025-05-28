@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="text-center mb-4">Đăng ký tài khoản</h4>
                        
                        {{-- Hiển thị thông báo lỗi từ session --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Hiển thị thông báo thành công (nếu có) --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Hiển thị lỗi validation --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Tên khách hàng --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        id="name" value="{{ old('name') }}" placeholder="Nhập họ và tên của bạn" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" placeholder="Nhập mật khẩu của bạn" required>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation" placeholder="Nhập lại mật khẩu của bạn" required>
                                </div>
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại của bạn">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Địa chỉ --}}
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                          id="address" rows="2" placeholder="Nhập địa chỉ của bạn">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Giới tính --}}
                            <div class="mb-3">
                                <label class="form-label">Giới tính</label>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male" 
                                                   value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="male">
                                                Nam
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female" 
                                                   value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="female">
                                                Nữ
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="other" 
                                                   value="other" {{ old('gender') == 'other' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="other">
                                                Khác
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('gender')
                                    <div class="text-danger small mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Preview avatar --}}
                            <div class="mb-3">
                                <img id="avatar-preview" src="#" alt="Preview" 
                                     style="display: none; max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                            </div>

                            {{-- Checkbox đồng ý điều khoản --}}
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                                       name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="terms">
                                    Tôi đồng ý với <a href="#" target="_blank">Điều khoản sử dụng</a> và 
                                    <a href="#" target="_blank">Chính sách bảo mật</a> <span class="text-danger">*</span>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nút đăng ký --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Đăng ký tài khoản</button>
                            </div>
                        </form>

                        {{-- Link đăng nhập --}}
                        <div class="text-center mt-3">
                            <p class="mb-0">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript cho preview avatar --}}
    <script>
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('avatar-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
@endsection