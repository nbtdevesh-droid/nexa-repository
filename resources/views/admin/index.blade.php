<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @if (Auth::guard('web')->user())
        <title>Admin-@yield('title')</title>
    @else
        <title>Staff-Member-@yield('title')</title>
    @endif

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/dist/css/adminlte.min.css') }}">
    <!-- TOASTR -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/toastr/toastr.min.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/dropzone/min/dropzone.min.css') }}">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-----Custom Style Sheet------->
    <link rel="stylesheet" href="{{ asset('/admin-assets/assets/css/style.css') }}">
    <!-- jQuery -->
    <script src="{{ asset('/admin-assets/plugins/jquery/jquery.min.js') }}"></script>
    @yield('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="" src="{{ asset('/admin-assets/assets/img/menu-icon/login_logo.png') }}"
                alt="login_logo">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-0">
            <!-- Left navbar links -->
            <ul class="navbar-nav d-md-none">
            </ul>

            <!-- Right navbar links -->
            <div class="menu_header_block_nav ml-auto">
                <ul class="navbar-nav  nav_menu_block">
                    <div class="notification_message_block_nav">
                        <!-- Messages Dropdown Menu -->
                        <li class="nav-item dropdown border_menu">
                            <a class="nav-link" data-toggle="dropdown" href="#">
                                <img src="{{ asset('/admin-assets/assets/img/menu-icon/notification_svg.svg') }}"
                                    width="16" height="20" alt="notification_svg">
                                <!-- <i class="far fa-comments"></i>
                                <span class="badge badge-danger navbar-badge">3</span> -->
                            </a>
                            <div class=" dropdown-menu dropdown-menu-lg dropdown-menu-right message_show_block">
                                <div class="header_menu_heading">
                                    <h6>Notification</h6>
                                </div>
                                <div class="message_text_img">
                                    @php
                                        $notifications = DB::table('other_notification_list')
                                            ->orderBy('id', 'desc')
                                            ->get();

                                        $isWebUser = Auth::guard('web')->check();
                                        $userId = $isWebUser ? null : Auth::guard('member')->user()->id;
                                        
                                        // If it's not a web user, filter notifications based on the user id
                                        if (!$isWebUser) {
                                            $filteredNotifications = [];
                                            foreach ($notifications as $notification) {
                                                $recived_id = json_decode($notification->other_recive_notification_id, true);

                                                if (is_array($recived_id) && in_array($userId, $recived_id)) {
                                                    $filteredNotifications[] = $notification;
                                                } elseif (empty($recived_id)) {
                                                    $filteredNotifications[] = $notification;
                                                }
                                            }
                                            $notifications = collect($filteredNotifications);
                                        }
                                        $notifications = $notifications->take(4);
                                    @endphp         
                                    @if($notifications->isEmpty())
                                        <p class="text-center">No new notifications</p>
                                    @else
                                        @foreach($notifications as $notification)
                                            <div class="tree_col">
                                                <div class="img_col_round">
                                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/order_panding.svg') }}"
                                                        width="20" height="20px" alt="order_panding">
                                                </div>
                                                <div class="name_col_font">
                                                    <h6>{{ $notification->other_recive_about }}</h6>
                                                    <p>{{ $notification->other_recive_notification_content }}</p>
                                                    <small class="">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="view_all_btn_message">
                                    <a href="{{ route('admin.notifications') }}">View All</a>
                                </div>
                            </div>
                        </li>
                    </div>
                    <div class="profile_header_nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link p-0 pr-3" data-toggle="dropdown" href="#">
                                @if (Auth::guard('web')->user())
                                    @php
                                        $user = Auth::guard('web')->user();
                                        $profileImgPath =
                                            $user && $user->pro_img
                                                ? asset("/admin-assets/assets/img/profile_img/admin/{$user->pro_img}")
                                                : asset('/admin-assets/assets/img/profile_img/user/common.png');
                                    @endphp

                                    @if ($user)
                                        <div class="pro_file">
                                            <img src="{{ $profileImgPath }}" class="img-circle elevation-2"
                                                width="40" height="40" alt="">
                                            <div class="profile_header">
                                                <h4 class="h4 mb-0"><strong>{{ $user->name }}</strong></h4>
                                                <div class="user_mail">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @elseif(Auth::guard('member')->user())
                                    @php
                                        $user = Auth::guard('member')->user();
                                        $profileImgPath =
                                            $user && $user->image
                                                ? asset("/admin-assets/assets/img/profile_img/staff/{$user->image}")
                                                : asset('/admin-assets/assets/img/profile_img/user/common.png');
                                    @endphp

                                    @if ($user)
                                        <div class="pro_file">
                                            <img src="{{ $profileImgPath }}" class="img-circle elevation-2"
                                                width="40" height="40" alt="">
                                            <div class="profile_header">
                                                <h4 class="h4 mb-0"><strong>{{ $user->first_name }}</strong></h4>
                                                <div class="user_mail">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3">
                                @if (Auth::guard('web')->user())
                                    <h4 class="h4 mb-0"><strong>{{ Auth::guard('web')->user()->name }}</strong></h4>
                                    <div class="mb-3">{{ Auth::guard('web')->user()->email }}</div>
                                @elseif (Auth::guard('member')->user())
                                    <h4 class="h4 mb-0">
                                        <strong>{{ Auth::guard('member')->user()->first_name }}</strong>
                                    </h4>
                                    <div class="mb-3">{{ Auth::guard('member')->user()->email }}</div>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.profile') }}" class="dropdown-item">
                                    <i class="fas fa-user-cog mr-2"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" onclick="confirmLogout(event)" class="dropdown-item log_out_admin"
                                    style="color: #FF8300">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </li>
                    </div>
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link logo_bg mx-auto">
                <img src="{{ asset('/admin-assets/dist/img/logo_dashboard.png') }}" alt="logo_dashboard"
                    class="brand-image">
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                </div>
                <!-- Sidebar Menu -->
                <nav class="mt-2 sidebar_nav">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <a href="{{ route('admin.dashboard') }}"
                            class="dash_nav_links nav-link active_links {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
                            <img src="{{ asset('/admin-assets/assets/img/menu-icon/dashboard-icon.svg') }}"
                                width="30" height="30" alt="dashboard-icon">
                            <p>Dashboard</p>
                        </a>
                        @php
                            $currentRoute = Route::currentRouteName();
                            $menuOpen =
                                Str::startsWith($currentRoute, 'staff.') || Str::startsWith($currentRoute, 'user.');
                            $menuOpen1 =
                                Str::startsWith($currentRoute, 'customer.support') || Str::startsWith($currentRoute, 'privacy.policy') || Str::startsWith($currentRoute, 'terms.use') ||
                                Str::startsWith($currentRoute, 'home.banner') || Str::startsWith($currentRoute, 'pages.home.icon.update');
                            $menuOpen2 =
                                Str::startsWith($currentRoute, 'setting.flash_deal') || Str::startsWith($currentRoute, 'setting.add-flash_deal') || Str::startsWith($currentRoute, 'setting.deal.edit') || Str::startsWith($currentRoute, 'coupon.') || Str::startsWith($currentRoute, 'setting.shipping_charges') || Str::startsWith($currentRoute, 'setting.shipping_charges');
                        @endphp
                        @if (Auth::guard('web')->user())
                            <li class="nav-item user_li {{ $menuOpen ? 'menu-open' : '' }}">
                                <a href="#" class="dash_nav_links nav-link">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/user-icon.svg') }}"
                                        width="23" height="28" alt="user-icon">
                                    <!-- <i class=" nav-icon fa-solid fa-user"></i>-->
                                    <p style="">
                                        User
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item drop_down_user">
                                        <a href="{{ route('staff.index') }}"
                                            class="nav-link {{ Str::startsWith($currentRoute, 'staff.') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Staff Member</p>
                                        </a>
                                    </li>
                                    <li class="nav-item drop_down_user">
                                        <a href="{{ route('user.index') }}"
                                            class="nav-link {{ Str::startsWith($currentRoute, 'user.') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Wholesaler</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('category.index') }}"
                                    class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'category.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/category-icon.svg') }}"
                                        width="30" height="30" alt="category-icon">
                                    <p>Categories</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('brand.index') }}"
                                    class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'brand.') ? 'active' : '' }}">
                                    <!-- <i class="nav-icon fa fa-users"></i> -->
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/brand-icon.svg') }}"
                                        width="30" height="30" alt="brand-icon">
                                    <p>Brand</p>
                                </a>
                            </li>


                            <li class="nav-item user_li {{ $menuOpen2 ? 'menu-open' : '' }}">
                                <a href="#" class="dash_nav_links nav-link ">
                                    <i class="fas fa-user-cog "></i>
                                    {{-- <img src="{{asset('public/admin-assets/assets/img/menu-icon/setting_svg.svg')}}"> --}}
                                    <p>Promotional Tools <i class="fas fa-angle-left right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('coupon.index') }}"
                                            class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'coupon.') ? 'active' : '' }}">
                                            <!-- <i class="nav-icon fa fa-users"></i> -->
                                            {{-- <img src="{{ asset('/admin-assets/assets/img/menu-icon/brand-icon.svg') }}"
                                                width="30" height="30" alt="coupon-icon"> --}}
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Coupon</p>
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('setting.flash_deal') }}" 
                                        class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'setting.flash_deal') || Str::startsWith($currentRoute, 'setting.add-flash_deal') || Str::startsWith($currentRoute, 'setting.deal.edit') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Flash Deal</p>
                                        </a>
                                    </li>

                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('setting.shipping_charges') }}" 
                                        class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'setting.shipping_charges') || Str::startsWith($currentRoute, 'setting.shipping_charges') ? 'active' : ''  }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Shipping Charges</p>
                                        </a>
                                    </li>

                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('products.index') }}"
                                    class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'products.') ? 'active' : '' }}">
                                    <!-- <i class="nav-icon fa fa-users"></i> -->
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/all-products-icon.svg') }}"
                                        width="30" height="30" alt="all-products-icon">
                                    <p>All Products</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('order.index') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'order.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/order-list.svg') }}"
                                        width="23" height="30" alt="order-list-icon">
                                    <p>Order List</p>
                                </a>
                            </li>

                            {{-- <li class="nav-item">
                                <a href="{{ route('warehouse.index') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'warehouse.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/privacy-policy-icon.svg') }}"
                                        width="23" height="25" alt="privacy-policy-icon">
                                    <p style="margin-left:6px;">WareHouse</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ route('all.reviews') }}" class="dash_nav_links nav-link {{ request()->routeIs('all.reviews') || request()->routeIs('add.reviews') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/privacy-policy-icon.svg') }}"
                                        width="23" height="25" alt="privacy-policy-icon">
                                    <p style="margin-left:6px;">Reviews</p>
                                </a>
                            </li>
                            
                            <li class="nav-item user_li {{ $menuOpen1 ? 'menu-open' : '' }}">
                                <a href="#" class="dash_nav_links nav-link ">
                                    <i class="nav-icon fas fa-copy" style="width: 15px;"></i>
                                    <p> Pages <i class="fas fa-angle-left right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('home.banner') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'home.banner') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Home Banner</p>
                                        </a>
                                    </li>
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('pages.home.icon.update') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'pages.home.icon.update') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Home Icon</p>
                                        </a>
                                    </li>
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('customer.support') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'customer.support') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Customer Support</p>
                                        </a>
                                    </li>
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('privacy.policy') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'privacy.policy') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Privacy Policy</p>
                                        </a>
                                    </li>
                                    <li class="nav-item drop_down_page">
                                        <a href="{{ route('terms.use') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'terms.use') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Terms of Use</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('app.notification.setting') }}" class="dash_nav_links nav-link">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/log-out-icon.svg') }}" width="30" height="30" alt="log-out-icon">
                                    <p>App setting</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="dash_nav_links nav-link" onclick="confirmLogout(event)">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/log-out-icon.svg') }}" width="30" height="30" alt="log-out-icon">
                                    <p>Logout</p>
                                </a>
                            </li>
                        @elseif (Auth::guard('member')->user())
                            <li class="nav-item">
                                <a href="{{ route('category.index') }}"
                                    class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'category.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/category-icon.svg') }}"
                                        width="30" height="30" alt="category-icon">
                                    <p>Categories</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('brand.index') }}"
                                    class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'brand.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/brand-icon.svg') }}"
                                        width="30" height="30" alt="brand-icon">
                                    <!-- <i class="nav-icon fa fa-users"></i> -->
                                    <p>Brand</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('products.index') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'products.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/all-products-icon.svg') }}"
                                        width="30" height="30" alt="all-products-icon">
                                    <!-- <i class="nav-icon fa fa-users"></i> -->
                                    <p>My Products</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('order.index') }}" class="dash_nav_links nav-link {{ Str::startsWith($currentRoute, 'order.') ? 'active' : '' }}">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/order-list.svg') }}"
                                        width="23" height="30" alt="order-list-icon">
                                    <!-- <i class="nav-icon fa fa-users"></i> -->
                                    <p>Received Order</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#" class="dash_nav_links nav-link" onclick="confirmLogout(event)">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/log-out-icon.svg') }}" width="30" height="30" alt="log-out-icon">
                                    <p>Logout</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper dashboard-wrap">
            @yield ("content")
        </div>

        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>

    </div>
    <!-- ./wrapper -->
    <script type="text/javascript">
        var base_url = "<?php echo url('') . '/'; ?>"
        var csrf_token = "{{ csrf_token() }}"
        $(function() {
            Dropzone.autoDiscover = false;
            // Summernote
            $('.summernote').summernote({
                height: '200px'
            });
        });
    </script>
    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap -->
    <script src="{{ asset('/admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('/admin-assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/admin-assets/dist/js/adminlte.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('/admin-assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('/admin-assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- PAGE PLUGINS -->
    <!-- toastr -->
    <script src="{{ asset('/admin-assets/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('/admin-assets/assets/js/adminjs.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options.timeOut = 5000;

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
     <script>
        function confirmLogout(event) {
            event.preventDefault(); // Prevents default anchor action

            swal({
                title: "Are you sure?",
                text: "Do you want to logout?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willLogout) => {
                if (willLogout) {
                    window.location.href = "{{ route('admin.logout') }}";
                }
            });
        }
    </script>
    @yield('customJs')
</body>
</html>