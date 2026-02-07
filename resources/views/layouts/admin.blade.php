<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title> sketo</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('logo.png')}}">
    <link rel="icon" type="image/x-icon" href="{{asset('logo.png')}}">

    <!-- Modern Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="{{asset('fontawesome/all.min.css')}}">
    <script src="{{asset('fontawesome/js/all.min.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/vendors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/weather-icons/climacons.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/meteocons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/morris.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/chartist.css')}}">
    <link rel="stylesheet" type="text/css"
        href="{{asset('app-assets/vendors/css/charts/chartist-plugin-tooltip.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/app.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/custom-rtl.css')}}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{asset('app-assets/css-rtl/core/menu/menu-types/vertical-menu-modern.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/pages/timeline.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/pages/dashboard-ecommerce.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style-rtl.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/new-layout.css')}}">
    <!-- <link rel="stylesheet"  href="{{asset('css/fontawesome.min.css')}}"> -->
    <!-- END Custom CSS-->



</head>

<body class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- Loading Spinner -->
    <div id="loading-spinner">
        <div class="sketo-loader"></div>
    </div>


    <!-- fixed-top-->
    <nav
        class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row align-items-center justify-content-between w-100">
                    <li class="nav-item mobile-menu d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                            href="#"><i class="ft-menu font-large-1 text-white"></i></a></li>

                    <li class="nav-item d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <a class="navbar-brand py-0 d-flex align-items-center" href="{{route('dashboard')}}">
                                <img src="{{asset('logo.png')}}" style="width: 28px;" class="mr-2 ml-2">
                                <span class="brand-text text-white font-weight-bold"
                                    style="font-size: 1.1rem; letter-spacing: 0.5px;">SKETO</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item d-none d-md-block"><a class="nav-link modern-nav-toggle pr-0"
                            data-toggle="collapse"><i class="toggle-icon ft-toggle-right font-medium-3 text-white"
                                data-ticon="ft-toggle-right"></i>
                            <!-- <span class="badge badge-soft-success ml-2 py-1 px-2" style="font-size: 0.6rem; vertical-align: middle; box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);">LIVE</span> -->
                        </a></li>

                    <li class="nav-item d-md-none">
                        <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i
                                class="la la-ellipsis-v text-white"></i></a>
                    </li>
                </ul>
            </div>
            <div class="navbar-brand-center">
                <a class="navbar-brand d-flex align-items-center" href="{{route('dashboard')}}">
                    <img class="brand-logo" src="{{asset('logo.png')}}" style="width: 35px;">
                    <h3 class="brand-text mb-0 ml-2 mr-2">sketo</h3>
                </a>
            </div>

            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item d-flex align-items-center mr-3 me-3">
                            <div class="d-flex align-items-center">
                                <a href="{{route('cashier.viewCart')}}" class="btn btn-primary"> الكاشير </a>
                            </div>
                        </li>
                        <li class="dropdown dropdown-user nav-item">
                            <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <span class="mr-1">مرحبا,
                                    <span class="user-name text-bold-700"> {{Auth::user()->name}} </span>
                                </span>
                                <span class="avatar avatar-online">
                                    <img src="{{asset('app-assets/images/portrait/small/avatar-s-19.png')}}"
                                        alt="avatar"><i></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item"
                                    href="{{route('profile.edit')}}"><i class="ft-user"></i> تعديل بيانات الحساب </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ft-power"></i> تسجيل الخروج
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content">
            <div class="mobile-close-sidebar d-md-none">
                <a href="#" class="menu-toggle text-white"><i class="ft-x font-large-1"></i></a>
            </div>
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                @php
                    $user = auth()->user();
                    // Check if user has admin role
                    $isAdmin = $user && ($user->hasRole('admin') || $user->hasRole('super-admin'));
                @endphp @if($isAdmin)
                    <li class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }} nav-item">
                        <a href="{{route('dashboard')}}"><i class="la la-home"></i><span class="menu-title"
                                data-i18n="">لوحة التحكم</span></a>
                    </li>
                    <li class="{{ Request::is('shifts*') ? 'active' : '' }} nav-item">
                        <a href="{{route('shifts.index')}}"><i class="la la-clock-o"></i><span class="menu-title"
                                data-i18n="">إدارة الورديات</span></a>
                    </li>
                @endif

                @if($isAdmin)
                    <li class=" nav-item"><a href="#"><i class="la la-cube"></i><span class="menu-title"
                                data-i18n="nav.dash.main">المنتجات</a>
                        <ul class="menu-content">
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'categories.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('categories.index')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-list"></i> عرض فئات المنتجات </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'categories.create' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('categories.create')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-plus"></i> اضافة فئة منتج</a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'brands.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('brands.index')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-certificate"></i> عرض الماركات </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'brands.create' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('brands.create')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-plus-circle"></i> اضافة ماركة</a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'products.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('products.index')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-cubes"></i> عرض المنتجات </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'products.create' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('products.create')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-plus"></i> اضافة منتج</a>
                                </li>
                            @endif
                            <!-- @if($isAdmin)
                                                                                    <li class="{{ Route::currentRouteName() == 'quantity.updates' ? 'active':'' }} ">
                                                                                        <a class="menu-item" href="{{route('quantity.updates')}}" data-i18n="nav.dash.crypto"> تقارير المنتجات </a>
                                                                                    </li>
                                                                                @endif -->
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'product.transactions' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('product.transactions')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-exchange"></i> ادارة كمية المنتجات </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if($isAdmin)
                    <li class=" nav-item"><a href="#"><i class="la la-calculator"></i><span class="menu-title"
                                data-i18n="nav.dash.main"> الكاشير </a>
                        <ul class="menu-content">
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'cashier.viewCart' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('cashier.viewCart')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-shopping-basket"></i> اضافة فاتورة </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'invoices.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('invoices.index')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-file-text"></i> عرض فواتير البيع </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'clients.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('clients.index')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-users"></i> ادارة العملاء </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'customer-returns.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('customer-returns.index')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-reply"></i> مرتجعات العملاء </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if($isAdmin)
                    <li class=" nav-item"><a href="#"><i class="la la-shopping-cart"></i><span class="menu-title"
                                data-i18n="nav.dash.main"> فواتير الشراء </a>
                        <ul class="menu-content">
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'purchases.create' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('purchases.create')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-plus-square"></i> اضافة فاتورة </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'purchases.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('purchases.index')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-file-invoice"></i> عرض فواتير الشراء </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'reports.productTransfers' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('reports.productTransfers')}}"
                                        data-i18n="nav.dash.ecommerce"><i class="la la-truck"></i> تقرير نقل المنتجات </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'suppliers.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('suppliers.index')}}" data-i18n="nav.dash.ecommerce">
                                        <i class="la la-briefcase"></i> ادارة الموردين </a>
                                </li>
                            @endif
                            @if($isAdmin)
                                <li class="{{ Route::currentRouteName() == 'supplier-returns.index' ? 'active' : '' }} ">
                                    <a class="menu-item" href="{{route('supplier-returns.index')}}" data-i18n="nav.dash.crypto">
                                        <i class="la la-undo"></i> مرتجعات الموردين </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if($isAdmin)
                    <li class=" nav-item"><a href="#"><i class="la la-bar-chart"></i><span class="menu-title"
                                data-i18n="nav.dash.main"> تقارير المبيعات </a>
                        <ul class="menu-content">
                            <li class="{{ Route::currentRouteName() == 'reports.daily' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('reports.daily')}}" data-i18n="nav.dash.ecommerce">
                                    <i class="la la-calendar-check-o"></i> التقارير اليومية للمبيعات </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'reports.monthly' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('reports.monthly')}}" data-i18n="nav.dash.crypto">
                                    <i class="la la-calendar-plus-o"></i> التقارير الشهرية للمبيعات </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'reports.dateRange' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('reports.dateRange')}}" data-i18n="nav.dash.crypto">
                                    <i class="la la-calendar"></i> تقارير المبيعات بالتاريخ</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if($isAdmin)
                    <li class=" nav-item"><a href="#"><i class="la la-key"></i><span class="menu-title"
                                data-i18n="nav.dash.main"> الاذونات </a>
                        <ul class="menu-content">
                            <li class="{{ Route::currentRouteName() == 'roles.create' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('roles.create')}}" data-i18n="nav.dash.ecommerce">
                                    <i class="la la-user-plus"></i> اضافة ادوار </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'roles.index' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('roles.index')}}" data-i18n="nav.dash.crypto">
                                    <i class="la la-users"></i> عرض الادوار </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'permissions.create' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('permissions.create')}}" data-i18n="nav.dash.ecommerce">
                                    <i class="la la-toggle-on"></i> اضافة صلاحية </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('permissions.index')}}" data-i18n="nav.dash.crypto">
                                    <i class="la la-check-square"></i> عرض الصلاحيات </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'role_user.index' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('role_user.index')}}" data-i18n="nav.dash.crypto">
                                    <i class="la la-user-secret"></i> ادوار المستخدمين </a>
                            </li>
                            <li class="{{ Route::currentRouteName() == 'users.create' ? 'active' : '' }} ">
                                <a class="menu-item" href="{{route('users.create')}}" data-i18n="nav.dash.ecommerce">
                                    <i class="la la-user-plus"></i> اضافة مستخدم </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if($isAdmin)
                    <li class="{{ Route::currentRouteName() == 'treasury' ? 'active' : '' }} nav-item">
                        <a href="{{route('treasury')}}"><i class="la la-bank"></i><span class="menu-title"
                                data-i18n="nav.dash.crypto"> الخزينة</span></a>
                    </li>
                @endif

            </ul>

            <!-- Sidebar Footer Profile -->
            <div class="sidebar-user-card mt-auto">
                <img src="{{asset('app-assets/images/portrait/small/avatar-s-19.png')}}" alt="avatar"
                    class="sidebar-user-avatar">
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{Auth::user()->name}}</span>
                    <span class="sidebar-user-role">مشرف النظام</span>
                </div>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                    class="ml-auto text-muted hover-primary" title="تسجيل الخروج">
                    <i class="ft-power"></i>
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>



    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <footer class="footer footer-static footer-light navbar-border navbar-shadow">
        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
            <span class="float-md-left d-block d-md-inline-block">Copyright &copy;
                <a href="https://wa.me/201555622169" target="_blank" style="color: #25D366; text-decoration: none;">
                    <strong>Eng: Bola Eshaq</strong>
                </a>
            </span>
            <span class="float-md-right d-block d-md-inline-blockd-none d-lg-block">01555622169
                <i class="fas fa-phone pink"></i>
        </p>
    </footer>
    <!-- BEGIN VENDOR JS-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('app-assets/vendors/js/charts/chartist.min.js')}}" type="text/javascript"></script>
    {{--
    <script src="{{asset('app-assets/vendors/js/charts/chartist-plugin-tooltip.min.js')}}"
        type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/charts/raphael-min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/charts/morris.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/timeline/horizontal-timeline.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN MODERN JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/customizer.js')}}" type="text/javascript"></script>
    <!-- END MODERN JS-->
    <script>
        // Universal Spinner Removal Logic
        function hideSpinner() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.transition = 'opacity 0.5s ease';
                spinner.style.opacity = '0';
                setTimeout(() => {
                    spinner.style.display = 'none';
                }, 500);
            }
        }

        // Hide on window load
        window.addEventListener('load', hideSpinner);

        // Hide on DOMContentLoaded as a faster alternative
        document.addEventListener('DOMContentLoaded', function () {
            if (document.readyState === 'complete') {
                hideSpinner();
            }
        });

        // Fail-safe: Hide after 4 seconds regardless of load state
        setTimeout(hideSpinner, 4000);

        // Mobile Sidebar Close Logic
        document.addEventListener('click', function (event) {
            var menu = document.querySelector('.main-menu');
            var body = document.body;

            if (body.classList.contains('menu-open')) {
                // Check if click is inside menu or on any toggle button
                if (menu.contains(event.target) || event.target.closest('.menu-toggle')) {
                    return;
                }

                // Clicked outside - Close Menu
                var mainToggle = document.querySelector('.nav-link.menu-toggle');
                if (mainToggle) mainToggle.click();
                else body.classList.remove('menu-open');
            }
        });
    </script>

    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}" type="text/javascript"></script>

    <!-- END PAGE LEVEL JS-->
    @stack('scripts')
</body>

</html>