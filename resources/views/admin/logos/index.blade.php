@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">🍤 Quản lý Logo Ocean Pearl</h4>
                    <a href="{{ route('admin.logos.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Thêm Logo
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif  

                    <div class="row">
                        @forelse($logos as $logo)
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <img src="{{ $logo->image_url }}" 
                                             alt="Logo" 
                                             class="img-fluid mb-3" 
                                             style="max-height: 100px; object-fit: contain;">
                                        
                                        <div class="btn-group w-100">
                                            <a href="{{ route('admin.logos.edit', $logo) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('admin.logos.destroy', $logo) }}" 
                                                  method="POST" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa logo này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bx bx-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <h5 class="text-muted">Chưa có logo nào</h5>
                                    <a href="{{ route('admin.logos.create') }}" class="btn btn-primary">
                                        Thêm logo đầu tiên
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
