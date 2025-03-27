@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>
                <div class="topbar-item">
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Chỉnh Sửa Khu Vực</h4>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Thông Tin Khu Vực</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('areas.update', $area->area_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')                 
                    <input type="hidden" name="id" value="<?php echo $area['area_id']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khu vực</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $area['name']; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo $area['description']; ?></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control" name="status">
                                <option value="Hoạt động" <?php echo $area['status'] == 'Hoạt động' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="Bảo trì" <?php echo $area['status'] == 'Bảo trì' ? 'selected' : ''; ?>>Bảo trì</option>
                                <option value="Đóng cửa" <?php echo $area['status'] == 'Đóng cửa' ? 'selected' : ''; ?>>Đóng cửa</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa</label>
                            <input type="number" class="form-control" name="capacity" value="<?php echo $area['capacity']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tầng</label>
                            <input type="number" class="form-control" name="floor" value="<?php echo $area['floor']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cho phép hút thuốc?</label>
                            <select class="form-control" name="is_smoking">
                                <option value="1" <?php echo $area['is_smoking'] == 1 ? 'selected' : ''; ?>>Có</option>
                                <option value="0" <?php echo $area['is_smoking'] == 0 ? 'selected' : ''; ?>>Không</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khu VIP?</label>
                            <select class="form-control" name="is_vip">
                                <option value="1" <?php echo $area['is_vip'] == 1 ? 'selected' : ''; ?>>Có</option>
                                <option value="0" <?php echo $area['is_vip'] == 0 ? 'selected' : ''; ?>>Không</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phụ thu</label>
                            <input type="number" step="0.01" class="form-control" name="surcharge"
                                value="<?php echo $area['surcharge']; ?>">
                        </div>

                        <div class="image-container">
                            <label class="form-label">Hình ảnh hiện tại</label><br>
                            <img id="imagePreview"
                            src="{{ $area->image ? asset('storage/' . $area->image) : '' }}" class="preview-img"
                            style="{{ $area->image ? '' : 'display: none;' }}" width="200px">
                        </div>
                        <hr>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thay đổi hình ảnh</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                        <a href="areas_list.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
