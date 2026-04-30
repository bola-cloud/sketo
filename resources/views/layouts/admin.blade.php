<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-textdirection="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@yield('title', 'Sketo')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('logo.png')}}?v=1.1">
    <link rel="icon" type="image/x-icon" href="{{asset('logo.png')}}?v=1.1">

    <!-- Modern Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script>
        // Immediate Theme Check to prevent FOUC (applied at HTML level)
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>


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
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/premium-ui.css')}}">
    <!-- Dexie.js for IndexedDB -->
    <script src="https://unpkg.com/dexie@latest/dist/dexie.js"></script>
    <script src="{{asset('assets/js/sketo-sync.js')}}"></script>
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
        class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow premium-navbar">
        <div class="navbar-wrapper">
            <div class="navbar-header" style="background: transparent;">
                <ul class="nav navbar-nav flex-row align-items-center justify-content-between w-100">
                    <li class="nav-item mobile-menu d-md-none"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                            href="#"><i class="ft-menu font-large-1 text-white"></i></a></li>

                    <li class="nav-item d-none d-md-block">
                        <div class="d-flex align-items-center ml-2">
                            <a class="navbar-brand py-0 d-flex align-items-center" href="{{route('dashboard')}}">
                                <img src="{{asset('logo.png')}}?v=1.1"
                                    style="width: 38px; filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.4));"
                                    class="mr-2 ml-2">
                                <span class="brand-text text-gradient-premium font-weight-bold"
                                    style="font-size: 1.2rem; letter-spacing: 0.5px;">{{ auth()->user()->vendor->business_name ?? 'SKETO' }}</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item d-none d-md-block"><a class="nav-link modern-nav-toggle pr-0"
                            data-toggle="collapse">
                            <i class="toggle-icon ft-toggle-right font-medium-3 text-white opacity-75"
                                data-ticon="ft-toggle-right"></i>
                        </a></li>

                    <li class="nav-item d-md-none">
                        <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i
                                class="la la-ellipsis-v text-white"></i></a>
                    </li>
                </ul>
            </div>
            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item d-flex align-items-center mr-3">
                            <a href="{{route('cashier.viewCart')}}"
                                class="btn btn-primary btn-sm px-3 shadow-sm rounded-xl">
                                <i class="la la-shopping-cart"></i> {{ __('app.sidebar.cashier') }} </a>
                        </li>

                        {{-- Sync Status Indicator --}}
                        <li class="nav-item d-flex align-items-center mr-2">
                            <span id="sync-status-indicator" class="text-success" title="Online - Database Synced"
                                style="cursor: help;">
                                <i class="la la-cloud-upload font-medium-3"></i>
                            </span>
                        </li>

                        {{-- Theme Toggle --}}
                        <li class="nav-item d-flex align-items-center">
                            <div class="theme-switch-wrapper">
                                <label class="theme-switch" for="checkbox">
                                    <input type="checkbox" id="checkbox" />
                                    <div class="slider">
                                        <i class="la la-moon"></i>
                                        <i class="la la-sun"></i>
                                    </div>
                                </label>
                            </div>
                        </li>
                        <li class="dropdown dropdown-language nav-item">
                            <a class="dropdown-toggle nav-link text-white opacity-75" id="dropdown-flag" href="#"
                                data-toggle="dropdown">
                                <i class="la la-globe font-medium-3"></i>
                                <span
                                    class="selected-language font-weight-bold">{{ App::getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right rounded-xl shadow-lg border-0 premium-dropdown"
                                aria-labelledby="dropdown-flag">
                                <a class="dropdown-item hover-bg-glass" href="{{ route('language.switch', 'ar') }}">
                                    العربية </a>
                                <a class="dropdown-item hover-bg-glass" href="{{ route('language.switch', 'en') }}">
                                    English </a>
                            </div>
                        </li>
                        <li class="dropdown dropdown-user nav-item">
                            <a class="dropdown-toggle nav-link dropdown-user-link d-flex align-items-center" href="#"
                                data-toggle="dropdown">
                                <div class="text-right d-none d-lg-block mx-3">
                                    <span class="user-name text-white font-weight-bold">{{Auth::user()->name}}</span>
                                </div>
                                <span class="avatar avatar-online shadow-glow"
                                    style="width: 44px; border: 2px solid var(--p-emerald);">
                                    <img src="{{asset('app-assets/images/portrait/small/avatar-s-19.png')}}"
                                        alt="avatar" class="rounded-circle">
                                </span>
                            </a>
                            <div
                                class="dropdown-menu dropdown-menu-right rounded-xl shadow-lg border-0 premium-dropdown">
                                <a class="dropdown-item hover-bg-glass" href="{{route('profile.edit')}}"><i
                                        class="ft-user"></i>
                                    {{ __('app.sidebar.profile') ?? 'Profile' }} </a>
                                <div class="dropdown-divider" style="border-top-color: rgba(255,255,255,0.1);"></div>
                                <a class="dropdown-item text-danger hover-bg-glass" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ft-power"></i> {{ __('app.common.logout') ?? 'Logout' }}
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
                    $isSuperAdmin = $user && $user->hasRole('super_admin');
                    $isAdmin = $user && ($user->hasRole('admin') || $user->hasRole('owner') || $isSuperAdmin);
                @endphp

                @if($isAdmin)
                {{-- 1. Daily Operations - العمليات اليومية --}}
                <li class="navigation-header"><span>{{ __('العمليات اليومية') }}</span></li>
                <li class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }} nav-item">
                    <a href="{{route('dashboard')}}"><i class="la la-home"></i><span class="menu-title">{{ __('app.sidebar.dashboard') }}</span></a>
                </li>
                <li class="{{ Route::currentRouteName() == 'cashier.viewCart' ? 'active' : '' }} nav-item">
                    <a href="{{route('cashier.viewCart')}}"><i class="la la-calculator"></i><span class="menu-title">{{ __('app.sidebar.add_invoice') }}</span></a>
                </li>
                <li class="{{ Route::currentRouteName() == 'invoices.index' ? 'active' : '' }} nav-item">
                    <a href="{{route('invoices.index')}}"><i class="la la-file-text"></i><span class="menu-title">{{ __('app.sidebar.view_invoices') }}</span></a>
                </li>
                <li class="{{ Request::is('shifts*') ? 'active' : '' }} nav-item">
                    <a href="{{route('shifts.index')}}"><i class="la la-clock-o"></i><span class="menu-title">{{ __('app.sidebar.shift_management') }}</span></a>
                </li>
                @planFeature('treasury')
                <li class="{{ Route::currentRouteName() == 'treasury' ? 'active' : '' }} nav-item">
                    <a href="{{route('treasury')}}"><i class="la la-bank"></i><span class="menu-title">{{ __('app.sidebar.treasury') }}</span></a>
                </li>
                @endplanFeature

                {{-- 2. Inventory & Purchases - المخازن والمشتريات --}}
                <li class="navigation-header"><span>{{ __('المخازن والمشتريات') }}</span></li>
                <li class="nav-item has-sub {{ Request::is('products*') || Request::is('categories*') || Request::is('brands*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-cube"></i><span class="menu-title">{{ __('app.sidebar.products') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'products.index' ? 'active' : '' }}"><a href="{{route('products.index')}}"><i class="la la-cubes"></i> {{ __('app.sidebar.view_products') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'products.create' ? 'active' : '' }}"><a href="{{route('products.create')}}"><i class="la la-plus"></i> {{ __('app.sidebar.add_product') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'categories.index' ? 'active' : '' }}"><a href="{{route('categories.index')}}"><i class="la la-list"></i> {{ __('app.sidebar.view_categories') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'brands.index' ? 'active' : '' }}"><a href="{{route('brands.index')}}"><i class="la la-certificate"></i> {{ __('app.sidebar.view_brands') }}</a></li>
                    </ul>
                </li>
                <li class="nav-item has-sub {{ Request::is('purchases*') || Request::is('suppliers*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-shopping-cart"></i><span class="menu-title">{{ __('app.sidebar.purchase_invoices') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'purchases.index' ? 'active' : '' }}"><a href="{{route('purchases.index')}}"><i class="la la-file-invoice"></i> {{ __('app.sidebar.view_purchase_invoices') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'purchases.create' ? 'active' : '' }}"><a href="{{route('purchases.create')}}"><i class="la la-plus-square"></i> {{ __('app.sidebar.add_purchase_invoice') }}</a></li>
                        @planFeature('suppliers')
                        <li class="{{ Route::currentRouteName() == 'suppliers.index' ? 'active' : '' }}"><a href="{{route('suppliers.index')}}"><i class="la la-briefcase"></i> {{ __('app.sidebar.manage_suppliers') }}</a></li>
                        @endplanFeature
                    </ul>
                </li>

                {{-- 3. Clients & Returns - العملاء والمرتجعات --}}
                <li class="navigation-header"><span>{{ __('العملاء والمرتجعات') }}</span></li>
                @planFeature('clients')
                <li class="{{ Route::currentRouteName() == 'clients.index' ? 'active' : '' }} nav-item">
                    <a href="{{route('clients.index')}}"><i class="la la-users"></i><span class="menu-title">{{ __('app.sidebar.manage_clients') }}</span></a>
                </li>
                @endplanFeature
                @planFeature('returns')
                <li class="nav-item has-sub {{ Request::is('*-returns*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-reply-all"></i><span class="menu-title">{{ __('المرتجعات') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'customer-returns.index' ? 'active' : '' }}"><a href="{{route('customer-returns.index')}}"><i class="la la-reply"></i> {{ __('app.sidebar.customer_returns') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'supplier-returns.index' ? 'active' : '' }}"><a href="{{route('supplier-returns.index')}}"><i class="la la-undo"></i> {{ __('app.sidebar.supplier_returns') }}</a></li>
                    </ul>
                </li>
                @endplanFeature

                {{-- 4. Reports & Analytics - التقارير والإحصائيات --}}
                <li class="navigation-header"><span>{{ __('التقارير والإحصائيات') }}</span></li>
                @planFeature('reports_advanced')
                <li class="nav-item has-sub {{ Request::is('reports/statistics*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-pie-chart"></i><span class="menu-title">{{ __('app.sidebar.financial_reports') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'reports.statistics.financial_summary' ? 'active' : '' }}"><a href="{{route('reports.statistics.financial_summary')}}"><i class="la la-file-text"></i> {{ __('app.sidebar.income_statement') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'reports.statistics.inventory_valuation' ? 'active' : '' }}"><a href="{{route('reports.statistics.inventory_valuation')}}"><i class="la la-tags"></i> {{ __('app.sidebar.inventory_valuation') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'reports.statistics.aging_report' ? 'active' : '' }}"><a href="{{route('reports.statistics.aging_report')}}"><i class="la la-hourglass-half"></i> {{ __('app.sidebar.aging_report') }}</a></li>
                    </ul>
                </li>
                <li class="nav-item has-sub {{ Request::is('reports/sales*') || Request::is('reports/daily*') || Request::is('reports/monthly*') || Request::is('reports/date-range*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-bar-chart"></i><span class="menu-title">{{ __('app.sidebar.sales_reports') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'reports.daily' ? 'active' : '' }}"><a href="{{route('reports.daily')}}"><i class="la la-calendar-check-o"></i> {{ __('app.sidebar.daily_reports') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'reports.monthly' ? 'active' : '' }}"><a href="{{route('reports.monthly')}}"><i class="la la-calendar-plus-o"></i> {{ __('app.sidebar.monthly_reports') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'reports.dateRange' ? 'active' : '' }}"><a href="{{route('reports.dateRange')}}"><i class="la la-calendar"></i> {{ __('app.sidebar.date_range_reports') }}</a></li>
                    </ul>
                </li>
                @endplanFeature
                <li class="nav-item has-sub {{ Request::is('reports/product-transfers*') || Route::currentRouteName() == 'product.transactions' ? 'open' : '' }}">
                    <a href="#"><i class="la la-exchange"></i><span class="menu-title">{{ __('حركة المنتجات') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'reports.productTransfers' ? 'active' : '' }}"><a href="{{route('reports.productTransfers')}}"><i class="la la-truck"></i> {{ __('app.sidebar.product_transfer_report') }}</a></li>
                        @planFeature('audit_logs')
                        <li class="{{ Route::currentRouteName() == 'product.transactions' ? 'active' : '' }}"><a href="{{route('product.transactions')}}"><i class="la la-history"></i> {{ __('app.sidebar.product_transfer_report') }}</a></li>
                        @endplanFeature
                    </ul>
                </li>

                {{-- 5. System Settings - إعدادات النظام --}}
                <li class="navigation-header"><span>{{ __('إعدادات النظام') }}</span></li>
                @planFeature('users')
                <li class="nav-item has-sub {{ Request::is('roles*') || Request::is('permissions*') || Request::is('users*') || Request::is('role-user*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-key"></i><span class="menu-title">{{ __('app.sidebar.permissions') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'users.create' ? 'active' : '' }}"><a href="{{route('users.create')}}"><i class="la la-user-plus"></i> {{ __('app.sidebar.add_user') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'role_user.index' ? 'active' : '' }}"><a href="{{route('role_user.index')}}"><i class="la la-user-secret"></i> {{ __('app.sidebar.user_roles') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'roles.index' ? 'active' : '' }}"><a href="{{route('roles.index')}}"><i class="la la-users"></i> {{ __('app.sidebar.view_roles') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'permissions.index' ? 'active' : '' }}"><a href="{{route('permissions.index')}}"><i class="la la-check-square"></i> {{ __('app.sidebar.view_permissions') }}</a></li>
                    </ul>
                </li>
                @endplanFeature

                @if($isSuperAdmin)
                <li class="nav-item has-sub {{ Request::is('super-admin*') ? 'open' : '' }}">
                    <a href="#"><i class="la la-server"></i><span class="menu-title">{{ __('app.sidebar.platform_management') }}</span></a>
                    <ul class="menu-content">
                        <li class="{{ Route::currentRouteName() == 'super-admin.dashboard' ? 'active' : '' }}"><a href="{{route('super-admin.dashboard')}}"><i class="la la-chart-area"></i> {{ __('app.sidebar.platform_overview') }}</a></li>
                        <li class="{{ Route::currentRouteName() == 'super-admin.vendors.index' ? 'active' : '' }}"><a href="{{route('super-admin.vendors.index')}}"><i class="la la-industry"></i> {{ __('app.sidebar.vendors') }}</a></li>
                    </ul>
                </li>
                @endif
        @endif

            </ul>

            <!-- Sidebar Footer Profile -->
            <div class="sidebar-user-card shadow-lg">
                <img src="{{asset('app-assets/images/portrait/small/avatar-s-19.png')}}" alt="avatar"
                    class="sidebar-user-avatar">
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{Auth::user()->name}}</span>
                    <span class="sidebar-user-role">{{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}</span>
                </div>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                    class="ml-auto p-2 rounded-lg hover-bg-light" style="color: var(--p-slate-400);"
                    title="{{ __('app.common.logout') }}">
                    <i class="ft-power h5 mb-0"></i>
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
    <footer class="footer footer-static">
        <p class="clearfix mb-0 px-2 text-center text-md-left">
            <span class="float-md-left d-block d-md-inline-block">
                {{ __('app.common.copyright') }} &copy; {{ date('Y') }}
                <a href="https://wa.me/201555622169" target="_blank" class="developer-link ml-1">
                    <strong>{{ __('app.common.developer') }}</strong>
                </a>
            </span>
            <span class="float-md-right d-block d-md-inline-block d-none d-lg-block">
                01555622169 <i class="la la-phone pink"></i>
            </span>
        </p>
    </footer>
    <!-- FIX FOR ELECTRON NODE INTEGRATION -->
    <script>
        if (typeof module === 'object') {
            window.module = module;
            module = undefined;
        }
    </script>
    
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
        // Restore module if it was hidden
        if (window.module) {
            module = window.module;
        }
    </script>
    <script>
        // Theme Toggle Logic
        const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
        const currentTheme = localStorage.getItem('theme');

        if (currentTheme) {
            if (currentTheme === 'light') {
                toggleSwitch.checked = true;
                document.documentElement.classList.add('light-mode');
                document.body.classList.add('light-mode');
            }
        }

        function switchTheme(e) {
            if (e.target.checked) {
                document.documentElement.classList.add('light-mode');
                document.body.classList.add('light-mode');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.remove('light-mode');
                document.body.classList.remove('light-mode');
                localStorage.setItem('theme', 'dark');
            }
        }

        toggleSwitch.addEventListener('change', switchTheme, false);

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

        // Global Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // F1: Cashier
            if (e.key === 'F1') {
                e.preventDefault();
                window.location.href = "{{ route('cashier.viewCart') }}";
            }
            // F2: Products
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = "{{ route('products.index') }}";
            }
            // F4: Treasury
            if (e.key === 'F4') {
                e.preventDefault();
                window.location.href = "{{ route('treasury') }}";
            }
            // Escape: Close modals (Bootstrap 4 handles this by default, but let's be sure for custom ones)
            if (e.key === 'Escape') {
                $('.modal').modal('hide');
            }
        });
    </script>

    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}" type="text/javascript"></script>

    <!-- END PAGE LEVEL JS-->
    
    <!-- AI Advisor Chat Widget -->
    @include('components.ai-chat-widget')

    @stack('modals')
    @stack('scripts')
</body>

</html>