@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thêm Logo Mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.logos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Chọn hình ảnh logo</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*" 
                                   required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="preview-container" style="display: none;">
                            <label class="form-label">Xem trước</label>
                            <div class="text-center">
                                <img id="preview-image" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.logos.index') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu Logo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection