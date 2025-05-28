<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from techzaa.in/larkon/admin/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Jan 2025 03:28:08 GMT -->

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Dashboard | Larkon - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- jQuery (phải load đầu tiên) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- App favicon -->
    @include('admin.layouts.partials.style')
    <!-- Theme Config js (Require in all Page) -->
    <style>
        .user-avatar {
            width: 45px;
            /* Tăng kích thước ảnh */
            height: 45px;
            border: 3px solid #fff;
            /* Thêm viền trắng */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            /* Hiệu ứng bóng nhẹ */
            object-fit: cover;
            /* Cắt ảnh vừa khung tròn */
        }
    </style>
</head>

<body>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        <header class="topbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <div class="d-flex align-items-center">
                        <!-- Menu Toggle Button -->
                        <div class="topbar-item">
                            <button type="button" class="button-toggle-menu me-2">
                                <iconify-icon icon="solar:hamburger-menu-broken"
                                    class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>

                        <!-- Menu Toggle Button -->
                        <div class="topbar-item">
                            <h4 class="fw-bold topbar-button pe-none text-uppercase mb-0">Welcome!</h4>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1">

                        <!-- Theme Color (Light/Dark) -->
                        <div class="topbar-item">
                            <button type="button" class="topbar-button" id="light-dark-mode">
                                <iconify-icon icon="solar:moon-bold-duotone" class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>

                        <!-- Notification -->
                        <div class="dropdown topbar-item">
                            <button type="button" class="topbar-button position-relative"
                                id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <iconify-icon icon="solar:bell-bing-bold-duotone"
                                    class="fs-24 align-middle"></iconify-icon>
                                <span
                                    class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">3<span
                                        class="visually-hidden">unread messages</span></span>
                            </button>
                            <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="javascript: void(0);" class="text-dark text-decoration-underline">
                                                <small>Clear All</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div data-simplebar style="max-height: 280px;">
                                    <!-- Item -->
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom text-wrap">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="/admin/assets/images/users/avatar-1.jpg"
                                                    class="img-fluid me-2 avatar-sm rounded-circle" alt="avatar-1" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0"><span class="fw-medium">Josephine Thompson
                                                    </span>commented on admin panel <span>" Wow 😍! this
                                                        admin looks good and awesome design"</span></p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- Item -->
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm me-2">
                                                    <span
                                                        class="avatar-title bg-soft-info text-info fs-20 rounded-circle">
                                                        D
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-semibold">Donoghue Susan</p>
                                                <p class="mb-0 text-wrap">
                                                    Hi, How are you? What about our next meeting
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- Item -->
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="/admin/assets/images/users/avatar-3.jpg"
                                                    class="img-fluid me-2 avatar-sm rounded-circle" alt="avatar-3" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-semibold">Jacob Gines</p>
                                                <p class="mb-0 text-wrap">Answered to your comment on the
                                                    cash flow forecast's graph 🔔.</p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- Item -->
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm me-2">
                                                    <span
                                                        class="avatar-title bg-soft-warning text-warning fs-20 rounded-circle">
                                                        <iconify-icon
                                                            icon="iconamoon:comment-dots-duotone"></iconify-icon>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-semibold text-wrap">You have received
                                                    <b>20</b> new messages in the
                                                    conversation
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- Item -->
                                    <a href="javascript:void(0);" class="dropdown-item py-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="/admin/assets/images/users/avatar-5.jpg"
                                                    class="img-fluid me-2 avatar-sm rounded-circle" alt="avatar-5" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-semibold">Shawn Bunch</p>
                                                <p class="mb-0 text-wrap">
                                                    Commented on Admin
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="text-center py-3">
                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm">View All
                                        Notification <i class="bx bx-right-arrow-alt ms-1"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Setting -->
                        <div class="topbar-item d-none d-md-flex">
                            <button type="button" class="topbar-button" id="theme-settings-btn"
                                data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas"
                                aria-controls="theme-settings-offcanvas">
                                <iconify-icon icon="solar:settings-bold-duotone"
                                    class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>

                        <!-- Activity -->
                        <div class="topbar-item d-none d-md-flex">
                            <button type="button" class="topbar-button" id="theme-settings-btn"
                                data-bs-toggle="offcanvas" data-bs-target="#theme-activity-offcanvas"
                                aria-controls="theme-settings-offcanvas">
                                <iconify-icon icon="solar:clock-circle-bold-duotone"
                                    class="fs-24 align-middle"></iconify-icon>
                            </button>
                        </div>

                        <!-- User -->
                        @if (Auth::check())
                            <!-- Nếu đã đăng nhập -->
                            <div class="dropdown topbar-item">
                                <a type="button" class="topbar-button" id="page-header-user-dropdown"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-flex align-items-center">
                                        <img class="rounded-circle user-avatar" width="32"
                                            src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRTVFN0VCIi8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzciIHI9IjE4IiBmaWxsPSIjOUM5Q0EzIi8+CjxwYXRoIGQ9Ik0yMCA4MEM3My45MSA0OC44NSA5MS42NiA0OC44NSA4MCA4MEwyMCA4MFoiIGZpbGw9IiM5QzlDQTMiLz4KPC9zdmc+' }}"
                                            alt="avatar">
                                    </span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <h6 class="dropdown-header">Chào mừng, {{ Auth::user()->name }}!</h6>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fa-solid fa-user text-muted fs-18 align-middle me-1"></i>
                                        <span class="align-middle">Hồ sơ</span>
                                    </a>

                                    <a class="dropdown-item" href="{{ route('chat.index') }}">
                                        <i class="bx bx-message-dots text-muted fs-18 align-middle me-1"></i><span
                                            class="align-middle">Tin nhắn</span>
                                    </a>

                                    <a class="dropdown-item" href="pages-pricing.html">
                                        <i class="bx bx-wallet text-muted fs-18 align-middle me-1"></i><span
                                            class="align-middle">Bảng giá</span>
                                    </a>

                                    <a class="dropdown-item" href="pages-faqs.html">
                                        <i class="bx bx-help-circle text-muted fs-18 align-middle me-1"></i><span
                                            class="align-middle">Trợ giúp</span>
                                    </a>

                                    <a class="dropdown-item" href="{{ route('profile.changePassword') }}">
                                        <i class="bx bx-lock text-muted fs-18 align-middle me-1"></i><span
                                            class="align-middle">Đổi mật khẩu</span>
                                    </a>
                                    <div class="dropdown-divider my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa-solid fa-right-from-bracket fs-18 align-middle me-1"></i>
                                            <span class="align-middle">Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="dropdown topbar-item">
                                <a type="button" class="topbar-button" id="page-header-user-dropdown"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-flex align-items-center">
                                        <img class="rounded-circle" width="32"
                                            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRTVFN0VCIi8+CjxjaXJjbGUgY3g9IjUwIiBjeT0iMzciIHI9IjE4IiBmaWxsPSIjOUM5Q0EzIi8+CjxwYXRoIGQ9Ik0yMCA4MEM3My45MSA0OC44NSA5MS42NiA0OC44NSA4MCA4MEwyMCA4MFoiIGZpbGw9IiM5QzlDQTMiLz4KPC9zdmc+"
                                            alt="default-avatar">
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <h6 class="dropdown-header">Khách hàng chưa đăng nhập!</h6>
                                    <div class="dropdown-divider my-1"></div>

                                    <a class="dropdown-item text-success" href="{{ route('login') }}">
                                        <i class="bx bx-log-in fs-18 align-middle me-1"></i>
                                        <span class="align-middle">Đăng nhập</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <!-- App Search-->
                        <form class="app-search d-none d-md-block ms-2">
                            <div class="position-relative">
                                <input type="search" class="form-control" placeholder="Search..."
                                    autocomplete="off" value="">
                                <iconify-icon icon="solar:magnifer-linear" class="search-widget-icon"></iconify-icon>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Activity Timeline -->
        <div>
            <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-activity-offcanvas"
                style="max-width: 450px; width: 100%;">
                <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
                    <h5 class="text-white m-0 fw-semibold">Activity Stream</h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-0">
                    <div data-simplebar class="h-100 p-4">
                        <div class="position-relative ms-2">
                            <span class="position-absolute start-0  top-0 border border-dashed h-100"></span>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 avatar-sm translate-middle-x bg-danger d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                            icon="iconamoon:folder-check-duotone"></iconify-icon></span>
                                    <div class="ms-2">
                                        <h5 class="mb-1 text-dark fw-semibold fs-15 lh-base">Report-Fix /
                                            Update </h5>
                                        <p class="d-flex align-items-center">Add 3 files to <span
                                                class=" d-flex align-items-center text-primary ms-1"><iconify-icon
                                                    icon="iconamoon:file-light"></iconify-icon>
                                                Tasks</span></p>
                                        <div class="bg-light bg-opacity-50 rounded-2 p-2">
                                            <div class="row">
                                                <div class="col-lg-6 border-end border-light">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bx bxl-figma fs-20 text-red"></i>
                                                        <a href="#!" class="text-dark fw-medium">Concept.fig</a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bx bxl-file-doc fs-20 text-success"></i>
                                                        <a href="#!" class="text-dark fw-medium">larkon.docs</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="mt-2 text-muted">Monday , 4:24 PM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 avatar-sm translate-middle-x bg-success d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                            icon="iconamoon:check-circle-1-duotone"></iconify-icon></span>
                                    <div class="ms-2">
                                        <h5 class="mb-1 text-dark fw-semibold fs-15 lh-base">Project Status
                                        </h5>
                                        <p class="d-flex align-items-center mb-0">Marked<span
                                                class=" d-flex align-items-center text-primary mx-1"><iconify-icon
                                                    icon="iconamoon:file-light"></iconify-icon> Design
                                            </span> as <span
                                                class="badge bg-success-subtle text-success px-2 py-1 ms-1">
                                                Completed</span></p>
                                        <div
                                            class="d-flex align-items-center gap-3 mt-1 bg-light bg-opacity-50 p-2 rounded-2">
                                            <a href="#!" class="fw-medium text-dark">UI/UX Figma Design</a>
                                            <div class="ms-auto">
                                                <a href="#!" class="fw-medium text-primary fs-18"
                                                    data-bs-toggle="tooltip" data-bs-title="Download"
                                                    data-bs-placement="bottom"><iconify-icon
                                                        icon="iconamoon:cloud-download-duotone"></iconify-icon></a>
                                            </div>
                                        </div>
                                        <h6 class="mt-3 text-muted">Monday , 3:00 PM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 avatar-sm translate-middle-x bg-primary d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-16">UI</span>
                                    <div class="ms-2">
                                        <h5 class="mb-1 text-dark fw-semibold fs-15">Larkon Application UI
                                            v2.0.0 <span class="badge bg-primary-subtle text-primary px-2 py-1 ms-1">
                                                Latest</span>
                                        </h5>
                                        <p>Get access to over 20+ pages including a dashboard layout, charts,
                                            kanban board, calendar, and pre-order E-commerce & Marketing
                                            pages.</p>
                                        <div class="mt-2">
                                            <a href="#!" class="btn btn-light btn-sm">Download Zip</a>
                                        </div>
                                        <h6 class="mt-3 text-muted">Monday , 2:10 PM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 translate-middle-x bg-success bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><img
                                            src="/admin/assets/images/users/avatar-7.jpg" alt="avatar-5"
                                            class="avatar-sm rounded-circle"></span>
                                    <div class="ms-2">
                                        <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Alex Smith
                                            Attached Photos
                                        </h5>
                                        <div class="row g-2 mt-2">
                                            <div class="col-lg-4">
                                                <a href="#!">
                                                    <img src="/admin/assets/images/small/img-6.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </a>
                                            </div>
                                            <div class="col-lg-4">
                                                <a href="#!">
                                                    <img src="/admin/assets/images/small/img-3.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </a>
                                            </div>
                                            <div class="col-lg-4">
                                                <a href="#!">
                                                    <img src="/admin/assets/images/small/img-4.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </a>
                                            </div>
                                        </div>
                                        <h6 class="mt-3 text-muted">Monday 1:00 PM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 translate-middle-x bg-success bg-gradient d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><img
                                            src="/admin/assets/images/users/avatar-6.jpg" alt="avatar-5"
                                            class="avatar-sm rounded-circle"></span>
                                    <div class="ms-2">
                                        <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Rebecca J. added
                                            a new team member
                                        </h5>
                                        <p class="d-flex align-items-center gap-1"><iconify-icon
                                                icon="iconamoon:check-circle-1-duotone"
                                                class="text-success"></iconify-icon> Added a new member to
                                            Front Dashboard</p>
                                        <h6 class="mt-3 text-muted">Monday 10:00 AM</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative ps-4">
                                <div class="mb-4">
                                    <span
                                        class="position-absolute start-0 avatar-sm translate-middle-x bg-warning d-inline-flex align-items-center justify-content-center rounded-circle text-light fs-20"><iconify-icon
                                            icon="iconamoon:certificate-badge-duotone"></iconify-icon></span>
                                    <div class="ms-2">
                                        <h5 class="mb-0 text-dark fw-semibold fs-15 lh-base">Achievements
                                        </h5>
                                        <p class="d-flex align-items-center gap-1 mt-1">Earned a <iconify-icon
                                                icon="iconamoon:certificate-badge-duotone"
                                                class="text-danger fs-20"></iconify-icon>" Best Product
                                            Award"</p>
                                        <h6 class="mt-3 text-muted">Monday 9:30 AM</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="#!" class="btn btn-outline-dark w-100">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar (Theme Settings) -->
        <div>
            <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas">
                <div class="d-flex align-items-center bg-primary p-3 offcanvas-header">
                    <h5 class="text-white m-0">Theme Settings</h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-0">
                    <div data-simplebar class="h-100">
                        <div class="p-3 settings-bar">

                            <div>
                                <h5 class="mb-3 font-16 fw-semibold">Color Scheme</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        id="layout-color-light" value="light">
                                    <label class="form-check-label" for="layout-color-light">Light</label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-bs-theme"
                                        id="layout-color-dark" value="dark">
                                    <label class="form-check-label" for="layout-color-dark">Dark</label>
                                </div>
                            </div>

                            <div>
                                <h5 class="my-3 font-16 fw-semibold">Topbar Color</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-topbar-color"
                                        id="topbar-color-light" value="light">
                                    <label class="form-check-label" for="topbar-color-light">Light</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-topbar-color"
                                        id="topbar-color-dark" value="dark">
                                    <label class="form-check-label" for="topbar-color-dark">Dark</label>
                                </div>
                            </div>


                            <div>
                                <h5 class="my-3 font-16 fw-semibold">Menu Color</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-color"
                                        id="leftbar-color-light" value="light">
                                    <label class="form-check-label" for="leftbar-color-light">
                                        Light
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-color"
                                        id="leftbar-color-dark" value="dark">
                                    <label class="form-check-label" for="leftbar-color-dark">
                                        Dark
                                    </label>
                                </div>
                            </div>

                            <div>
                                <h5 class="my-3 font-16 fw-semibold">Sidebar Size</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size"
                                        id="leftbar-size-default" value="default">
                                    <label class="form-check-label" for="leftbar-size-default">
                                        Default
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size"
                                        id="leftbar-size-small" value="condensed">
                                    <label class="form-check-label" for="leftbar-size-small">
                                        Condensed
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size"
                                        id="leftbar-hidden" value="hidden">
                                    <label class="form-check-label" for="leftbar-hidden">
                                        Hidden
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size"
                                        id="leftbar-size-small-hover-active" value="sm-hover-active">
                                    <label class="form-check-label" for="leftbar-size-small-hover-active">
                                        Small Hover Active
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="data-menu-size"
                                        id="leftbar-size-small-hover" value="sm-hover">
                                    <label class="form-check-label" for="leftbar-size-small-hover">
                                        Small Hover
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="offcanvas-footer border-top p-3 text-center">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-danger w-100" id="reset-layout">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        <div class="main-nav">
            <!-- Sidebar Logo -->
            <div class="logo-box">
                {{-- Logo cho dark theme (giữ nguyên) --}}
                <a href="index.html" class="logo-dark">
                    <img src="/admin/assets/images/logo-sm.png" class="logo-sm" alt="logo sm">
                    <img src="/admin/assets/images/logo-dark.png" class="logo-lg" alt="logo dark">
                </a>

                {{-- FOREACH HIỂN THỊ TẤT CẢ LOGO --}}
                <a href="dashboard" class="logo-light">
                    @php
                        $logos = App\Models\Logo::all(); // Lấy tất cả logo
                    @endphp

                    @foreach ($logos as $logo)
                        <img src="{{ $logo->image_url }}" class="logo-lg custom-logo-lg"
                            alt="logo {{ $logo->id }}" style="width: 100%;">
                    @endforeach
                </a>
            </div>


            <!-- Menu Toggle Button (sm-hover) -->
            {{-- <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
                <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"
                    class="button-sm-hover-icon"></iconify-icon>
            </button> --}}

            <div class="scrollbar" data-simplebar>
                <ul class="navbar-nav" id="navbar-nav">

                    @auth
                        <!-- GENERAL SECTION - Available for all authenticated users -->
                        <li class="menu-title">General</li>

                        <!-- Dashboard - Different routes based on role -->
                        <li class="nav-item">
                            @if (auth()->user()->role === 'owner')
                                <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
                                @elseif(auth()->user()->role === 'staff')
                                    <a class="nav-link" href="{{ route('staff.index') }}">
                                    @elseif(auth()->user()->role === 'chef')
                                        <a class="nav-link" href="{{ route('chef.dashboard') }}">
                                        @elseif(auth()->user()->role === 'customer')
                                            <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                            @else
                                                <a class="nav-link" href="{{ route('owner.dashboard') }}">
                            @endif
                            <span class="nav-icon">
                                <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                            </span>
                            <span class="nav-text"> Dashboard </span>
                            </a>
                        </li>

                        <!-- Profile - Available for all authenticated users -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile') }}">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Profile </span>
                            </a>
                        </li>

                        <!-- Chat - Available for all authenticated users -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chat.index') }}">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:chat-round-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Chat </span>
                            </a>
                        </li>

                        <!-- OWNER ONLY SECTIONS -->
                        @if (auth()->user()->role === 'owner')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.logos.index') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:gallery-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý Logo </span>
                                </a>
                            </li>
                            <!-- Product Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarProducts">
                                    <span class="nav-icon">
                                        <iconify-icon icon="maki:grocery"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Sản phẩm </span>
                                </a>
                                <div class="collapse" id="sidebarProducts">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('dish_list') }}">Danh sách</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('variant_list') }}">Biến thể</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Category Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarCategory" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarCategory">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Danh mục </span>
                                </a>
                                <div class="collapse" id="sidebarCategory">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('category-list') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Sub Category Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarSubCategory" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarSubCategory">
                                    <span class="nav-icon">
                                        <iconify-icon icon="line-md:clipboard-list"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Danh mục con </span>
                                </a>
                                <div class="collapse" id="sidebarSubCategory">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('sub_category_list') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Area Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarAreas" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarAreas">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:card-send-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý khu vực </span>
                                </a>
                                <div class="collapse" id="sidebarAreas">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('areas.index') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Table Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarTables" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarTables">
                                    <span class="nav-icon">
                                        <iconify-icon icon="mdi:table-chair"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý bàn </span>
                                </a>
                                <div class="collapse" id="sidebarTables">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('tables.index') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Reservation Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarReservations" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarReservations">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:bookmark-square-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý đặt bàn </span>
                                </a>
                                <div class="collapse" id="sidebarReservations">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('admin.reservations.index') }}">Danh
                                                sách đặt bàn</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Invoice/Orders Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarInvoice" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarInvoice">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý đơn hàng </span>
                                </a>
                                <div class="collapse" id="sidebarInvoice">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('invoices.index') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Inventory Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarInventory" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarInventory">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý kho </span>
                                </a>
                                <div class="collapse" id="sidebarInventory">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('inventory.index') }}">Kho hàng</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('supplier.index') }}">Nhà cung cấp</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('purchase.index') }}">Mua hàng</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('inventory_logs.index') }}">Lịch
                                                sử</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Staff Management -->
                            <li class="menu-title mt-2">Quản lý nhân sự</li>

                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarUsers" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarUsers">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý người dùng </span>
                                </a>
                                <div class="collapse" id="sidebarUsers">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('user_list') }}">Danh sách</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Attendance Management -->
                            {{-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.list') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:calendar-mark-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Chấm công </span>
                                </a>
                            </li> --}}
                        @endif

                        <!-- MANAGER ONLY SECTIONS -->
                        @if (auth()->user()->role === 'manager')
                            <li class="menu-title mt-2">Quản lý</li>

                            <!-- Attendance Management -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('manager.attendance.list') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:calendar-mark-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Chấm công </span>
                                </a>
                            </li>

                            <!-- Salary Management -->
                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarSalary" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarSalary">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Quản lý lương </span>
                                </a>
                                <div class="collapse" id="sidebarSalary">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('salary.settings') }}">Cài đặt</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('salary.calculate') }}">Tính lương</a>
                                        </li>
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="{{ route('salary.history') }}">Lịch sử</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        <!-- STAFF ONLY SECTIONS -->
                        @if (auth()->user()->role === 'staff')
                            <li class="menu-title mt-2">Nhân viên</li>

                            <!-- Reservation Management -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('staff.reservations.index') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:bookmark-square-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Đặt bàn </span>
                                </a>
                            </li>

                            <!-- Invoice Management -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('staff.invoices.create') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Tạo đơn hàng </span>
                                </a>
                            </li>
                        @endif

                        <!-- CHEF ONLY SECTIONS -->
                        @if (auth()->user()->role === 'chef')
                            <li class="menu-title mt-2">Bếp</li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('chef.dashboard') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:chef-hat-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Đơn hàng bếp </span>
                                </a>
                            </li>
                        @endif

                        <!-- CUSTOMER ONLY SECTIONS -->
                        @if (auth()->user()->role === 'customer')
                            <li class="menu-title mt-2">Khách hàng</li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.select-table') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="mdi:table-chair"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Chọn bàn </span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.reservations') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:bookmark-square-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Đặt bàn của tôi </span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.floor-plan') }}">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:map-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Sơ đồ tầng </span>
                                </a>
                            </li>
                        @endif

                        <!-- PAYMENT SECTION - Available for all users -->
                        @if (in_array(auth()->user()->role, ['owner', 'staff', 'customer']))
                            <li class="menu-title mt-2">Thanh toán</li>

                            <li class="nav-item">
                                <a class="nav-link menu-arrow" href="#sidebarPayment" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarPayment">
                                    <span class="nav-icon">
                                        <iconify-icon icon="solar:card-bold-duotone"></iconify-icon>
                                    </span>
                                    <span class="nav-text"> Thanh toán </span>
                                </a>
                                <div class="collapse" id="sidebarPayment">
                                    <ul class="nav sub-navbar-nav">
                                        <li class="sub-nav-item">
                                            <a class="sub-nav-link" href="#"
                                                onclick="alert('VNPay Payment')">VNPay</a>
                                        </li>
                                        @if (auth()->user()->role === 'owner')
                                            <li class="sub-nav-item">
                                                <a class="sub-nav-link" href="#"
                                                    onclick="alert('PayPal Payment')">PayPal</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        <!-- LOGOUT SECTION -->
                        <li class="menu-title mt-2">Khác</li>

                        <li class="nav-item">
                            <a class="nav-link" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:logout-2-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Đăng xuất </span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @else
                        <!-- GUEST SECTION - Show when not authenticated -->
                        <li class="menu-title">Khách</li>

                        <!-- Login -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:login-3-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Đăng nhập </span>
                            </a>
                        </li>

                        <!-- Register -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:user-plus-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Đăng ký </span>
                            </a>
                        </li>

                        <!-- Information -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:info-circle-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Thông tin </span>
                            </a>
                        </li>

                        <!-- Contact -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-icon">
                                    <iconify-icon icon="solar:phone-bold-duotone"></iconify-icon>
                                </span>
                                <span class="nav-text"> Liên hệ </span>
                            </a>
                        </li>

                    @endauth

                </ul>
            </div>
        </div>
        <!-- ========== App Menu End ========== -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            {{-- @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                </div>
            @endif --}}

            <!-- Start Container Fluid -->
            @yield('content')
            <!-- End Container Fluid -->

            <!-- ========== Footer Start ========== -->
            <footer class="footer">
                {{-- <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> &copy; Larkon. Crafted by
                            <iconify-icon icon="iconamoon:heart-duotone"
                                class="fs-18 align-middle text-danger"></iconify-icon> <a
                                href="https://1.envato.market/techzaa" class="fw-bold footer-text"
                                target="_blank">Techzaa</a>
                        </div>
                    </div>
                </div> --}}
                <div class="container-fluid">
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
                </div>
            </footer>
            <!-- ========== Footer End ========== -->

        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    @include('admin.layouts.partials.script')

</body>


<!-- Mirrored from techzaa.in/larkon/admin/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Jan 2025 03:29:01 GMT -->

</html>
