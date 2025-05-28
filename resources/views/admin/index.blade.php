@extends('admin.layouts.master')
@section('content')
        <!-- Start Container Fluid -->
        <div class="container-fluid">

            <!-- Welcome Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-restaurant fs-1 mb-3"></i>
                            <h1 class="display-4 fw-bold mb-3">Chào mừng đến với Hiquila</h1>
                            <p class="lead">Nhà hàng hải sản cao cấp - Nơi hội tụ hương vị biển cả</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Container Fluid -->

        <!-- ========== Footer Start ========== -->
        <footer class="footer">
            {{-- <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> &copy; Ocean Pearl Restaurant. Crafted with
                        <iconify-icon icon="iconamoon:heart-duotone"
                            class="fs-18 align-middle text-danger"></iconify-icon> 
                        for seafood lovers
                    </div>
                </div>
            </div> --}}
        </footer>
        <!-- ========== Footer End ========== -->
@endsection