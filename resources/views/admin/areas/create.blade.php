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
                    <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Thêm Khu Vực</h4>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Thông Tin Khu Vực</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('areas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf                
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khu vực</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control" name="status">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Bảo trì">Bảo trì</option>
                                <option value="Đóng cửa">Đóng cửa</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa</label>
                            <input type="number" class="form-control" name="capacity">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tầng</label>
                            <input type="number" class="form-control" name="floor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cho phép hút thuốc?</label>
                            <select class="form-control" name="is_smoking">
                                <option value="1">Có</option>
                                <option value="0">Không</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khu VIP?</label>
                            <select class="form-control" name="is_vip">
                                <option value="1">Có</option>
                                <option value="0">Không</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phụ thu</label>
                            <input type="number" step="0.01" class="form-control" name="surcharge">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">Thêm Khu Vực</button>
                        <a href="areas_list.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
