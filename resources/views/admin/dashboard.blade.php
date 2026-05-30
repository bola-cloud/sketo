@extends('layouts.admin')

@section('title', __('app.dashboard.dashboard'))

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            z-index: 10000;
            width: auto;
            max-width: 300px;
        }

        /* Premium Glassmorphism Design System */
        .premium-card {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }

        .premium-card:hover {
            transform: translateY(-5px);
            background: rgba(30, 41, 59, 0.6);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4);
        }

        .premium-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
            transition: 0.5s;
        }

        .premium-card:hover::after {
            left: 100%;
        }

        .card-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.2);
        }

        .card-icon-wrapper i {
            font-size: 1.5rem !important;
            display: block !important;
        }

        .premium-card:hover .card-icon-wrapper {
            transform: scale(1.1) rotate(-5deg);
        }

        /* Light Mode Overrides */
        body.light-mode .premium-card {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        body.light-mode .premium-card:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .text-white { color: #1e293b !important; }
        body.light-mode .text-muted { color: #64748b !important; }

        .bg-gradient-x-indigo-blue {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
        }

        .bg-white-10 {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Alert Action Buttons */
        .alert-action-btn {
            background: rgba(255,255,255,0.15) !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 12px;
            padding: 8px 16px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .alert-action-btn:hover {
            background: rgba(255,255,255,0.25) !important;
            color: #fff !important;
            transform: scale(1.05);
        }

        /* Keyboard Shortcuts Layout */
        .shortcuts-items-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .shortcut-badge-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .shortcut-text {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .shortcut-divider {
            opacity: 0.5;
            margin: 0 0.5rem;
        }

        /* Mobile Responsiveness Optimizations */
        @media (max-width: 991px) {
            .shortcuts-flex-container {
                flex-direction: column;
                text-align: center;
            }
            .shortcuts-icon {
                margin: 0 auto 1.25rem auto !important;
            }
            .shortcuts-items-container {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .display-5 {
                font-size: 1.8rem !important;
                line-height: 1.3 !important;
            }
            .welcome-banner {
                padding: 1.5rem 1rem !important;
                text-align: center;
            }
            .premium-card {
                padding: 1.25rem !important;
                border-radius: 18px !important;
            }
            .card-icon-wrapper {
                width: 48px;
                height: 48px;
                margin-bottom: 1rem;
            }
            .card-icon-wrapper i {
                font-size: 1.25rem !important;
            }
            .premium-card h2 {
                font-size: 1.65rem !important;
            }
        }

        @media (max-width: 576px) {
            .alert-stock-card .d-flex, .alert-expiry-card .d-flex {
                flex-direction: column;
                align-items: center !important;
                text-align: center;
            }
            .alert-stock-card .card-icon-wrapper, .alert-expiry-card .card-icon-wrapper {
                margin: 0 auto 0.75rem auto !important;
            }
            .alert-stock-card .alert-action-btn, .alert-expiry-card .alert-action-btn {
                margin: 0.75rem auto 0 auto !important;
                width: 100%;
                display: block;
            }
            .shortcuts-items-container {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                gap: 0.75rem;
            }
            .shortcut-badge-item {
                justify-content: space-between;
                background: rgba(255, 255, 255, 0.08);
                padding: 0.6rem 1rem;
                border-radius: 12px;
                width: 100%;
            }
            body.light-mode .shortcut-badge-item {
                background: rgba(0, 0, 0, 0.04);
            }
            .shortcut-divider {
                display: none;
            }
        }
    </style>

    @php
        $user = auth()->user();
    @endphp

    <div class="container-fluid py-4">
        {{-- Welcome Header --}}
        <div class="row mb-5 animate-fade-in-up">
            <div class="col-12">
                <div class="premium-card welcome-banner border-0"
                    style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.15));">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h1 class="display-5 font-weight-bold mb-2 text-gradient-premium">
                                <span>{{ __('app.common.welcome') }},</span> <span dir="auto" class="d-inline-block">{{ Auth::user()->name }}!</span>
                            </h1>
                            <p class="h5 text-muted opacity-75 mb-0">
                                {{ __('app.dashboard.store_health_msg') }}
                            </p>
                        </div>
                        <div class="col-md-5 text-right d-none d-md-block">
                            <div class="d-inline-flex align-items-center p-3 rounded-24 bg-glass shadow-lg border border-white-10">
                                <div class="bg-emerald p-3 rounded-xl mr-3 shadow-glow-emerald">
                                    <i class="fas fa-check text-white h4 mb-0"></i>
                                </div>
                                <div class="text-left">
                                    <small class="text-muted d-block uppercase font-weight-bold">{{ __('app.dashboard.store_health') }}</small>
                                    <span class="text-emerald font-weight-bold h5 mb-0">{{ __('app.dashboard.optimized') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts Section --}}
        <div class="row mb-4">
            @if($lowStockProducts->count() > 0)
                <div class="col-md-6 mb-4 animate-fade-in-up">
                    <div class="premium-card border-0 alert-stock-card h-100" style="background: rgba(225, 29, 72, 0.12);">
                        <div class="d-flex align-items-center">
                            <div class="card-icon-wrapper mr-3 mb-0" style="background: rgba(225, 29, 72, 0.2); color: #fb7185;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-1 text-white">{{ __('app.dashboard.low_stock_alert') }}</h5>
                                <p class="mb-0 small text-muted">
                                    {{ trans_choice('app.dashboard.product_reached_min_choice', $lowStockProducts->count(), ['count' => $lowStockProducts->count()]) }}
                                </p>
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm alert-action-btn ml-auto">{{ __('app.dashboard.restock_now') }}</a>
                        </div>
                    </div>
                </div>
            @endif

            @if($expiringProducts->count() > 0)
                <div class="col-md-6 mb-4 animate-fade-in-up">
                    <div class="premium-card border-0 alert-expiry-card h-100" style="background: rgba(245, 158, 11, 0.12);">
                        <div class="d-flex align-items-center">
                            <div class="card-icon-wrapper mr-3 mb-0" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-1 text-white">{{ __('app.dashboard.expiry_alert') }}</h5>
                                <p class="mb-0 small text-muted">
                                    {{ trans_choice('app.dashboard.product_expiring_soon_choice', $expiringProducts->count(), ['count' => $expiringProducts->count()]) }}
                                </p>
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm alert-action-btn ml-auto">{{ __('app.sidebar.view_products') }}</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Top KPI Row --}}
        <div class="row mb-4 animate-fade-in-up">
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.products_sold') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #34d399; box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.products_sold') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="productsSold">{{ number_format($productsSold) }}</h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.revenue') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_revenue') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalRevenue">{{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.profit') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(168, 85, 247, 0.15); color: #a78bfa; box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_profit') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalProfit">{{ number_format($totalProfit, 2) }}</h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_products') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalUnsoldProducts">{{ number_format($totalUnsoldProducts) }}</h2>
                    </div>
                </a>
            </div>
        </div>

        {{-- Bottom KPI Row --}}
        <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.purchases') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(244, 63, 94, 0.15); color: #fb7185; box-shadow: 0 0 20px rgba(244, 63, 94, 0.2);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_purchases') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalPurchases">{{ number_format($totalPurchases, 2) }}</h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.cash_flow') }}" class="text-decoration-none">
                    <div class="premium-card h-100">
                        <div class="card-icon-wrapper" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; box-shadow: 0 0 20px rgba(14, 165, 233, 0.2);">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.available_money') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white" id="availableMoney">{{ number_format($availableMoney, 2) }}</h2>
                    </div>
                </a>
            </div>

            <!-- Date Filter Card -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="premium-card h-100 p-4">
                    <h5 class="mb-4 font-weight-bold text-white">
                        <i class="fas fa-calendar-alt mr-2" style="color: #818cf8;"></i>
                        {{ __('app.dashboard.filter_by_date') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small font-weight-bold uppercase mb-2 d-block">{{ __('app.dashboard.start_date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white-10 border-0 text-muted" style="border-radius: 12px 0 0 12px;">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <input type="text" id="start_date" name="start_date" class="form-control datepicker bg-white-10 border-0 text-white" 
                                    style="border-radius: 0 12px 12px 0;" placeholder="{{ __('app.common.select_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small font-weight-bold uppercase mb-2 d-block">{{ __('app.dashboard.end_date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white-10 border-0 text-muted" style="border-radius: 12px 0 0 12px;">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <input type="text" id="end_date" name="end_date" class="form-control datepicker bg-white-10 border-0 text-white" 
                                    style="border-radius: 0 12px 12px 0;" placeholder="{{ __('app.common.select_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keyboard Shortcuts Note -->
        <div class="row mb-5 animate-fade-in-up">
            <div class="col-12">
                <div class="premium-card bg-gradient-x-indigo-blue border-0 text-white shadow-lg shadow-glow-indigo">
                    <div class="d-flex align-items-center shortcuts-flex-container">
                        <div class="card-icon-wrapper mb-0 mr-4 shortcuts-icon" style="background: rgba(255, 255, 255, 0.2); color: #fff;">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold mb-2">{{ __('app.dashboard.keyboard_shortcuts') }}</h4>
                            <div class="shortcuts-items-container">
                                <div class="shortcut-badge-item">
                                    <span class="badge badge-pill badge-light text-dark px-3 py-2">F1</span>
                                    <span class="shortcut-text">{{ __('app.dashboard.cashier') }}</span>
                                </div>
                                <span class="shortcut-divider">|</span>
                                <div class="shortcut-badge-item">
                                    <span class="badge badge-pill badge-light text-dark px-3 py-2">F2</span>
                                    <span class="shortcut-text">{{ __('app.dashboard.products') }}</span>
                                </div>
                                <span class="shortcut-divider">|</span>
                                <div class="shortcut-badge-item">
                                    <span class="badge badge-pill badge-light text-dark px-3 py-2">F4</span>
                                    <span class="shortcut-text">{{ __('app.dashboard.treasury') }}</span>
                                </div>
                                <span class="shortcut-divider">|</span>
                                <div class="shortcut-badge-item">
                                    <span class="badge badge-pill badge-light text-dark px-3 py-2">F10</span>
                                    <span class="shortcut-text">{{ __('app.dashboard.save_and_print_invoice') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-xl-6 col-12 mb-4 animate-fade-in-up">
                <div class="premium-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-icon-wrapper mb-0 mr-3" style="width: 45px; height: 45px; background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="font-weight-bold text-white mb-0">
                            {{ __('app.dashboard.monthly_sales_chart') ?? 'Monthly Sales Performance' }}
                        </h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="productsSoldChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12 mb-4 animate-fade-in-up">
                <div class="premium-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-icon-wrapper mb-0 mr-3" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.2); color: #34d399;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="font-weight-bold text-white mb-0">
                            {{ __('app.dashboard.monthly_revenue_chart') ?? 'Monthly Revenue Analysis' }}
                        </h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-12 mb-4 animate-fade-in-up">
                <div class="premium-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-icon-wrapper mb-0 mr-3" style="width: 45px; height: 45px; background: rgba(168, 85, 247, 0.2); color: #a78bfa;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h5 class="font-weight-bold text-white mb-0">{{ __('app.dashboard.top_5_selling') }}</h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="topSellingChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 col-12 mb-4 animate-fade-in-up">
                <div class="premium-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="card-icon-wrapper mb-0 mr-3" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h5 class="font-weight-bold text-white mb-0">{{ __('app.dashboard.top_5_profitable') }}</h5>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="topProfitableChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{asset('assets/js/flatpickr.js')}}"></script>
        <script src="{{asset('assets/js/jquery.js')}}"></script>
        <script>
            $(document).ready(function () {
                flatpickr('.datepicker', {
                    dateFormat: "Y-m-d",
                    onChange: function (selectedDates, dateStr, instance) {
                        fetchFilteredData();
                    }
                });

                function fetchFilteredData() {
                    var startDate = $('#start_date').val();
                    var endDate = $('#end_date').val();

                    // Update detail links
                    updateDetailLinks(startDate, endDate);

                    $.ajax({
                        url: "{{ route('dashboard') }}",
                        type: "GET",
                        data: {
                            start_date: startDate,
                            end_date: endDate,
                        },
                        success: function (data) {
                            updateDashboard(data);
                        },
                        error: function (xhr, status, error) {
                            console.error("Error fetching data: " + error);
                            console.error("Response Text: " + xhr.responseText);
                        }
                    });
                }

                function updateDetailLinks(start, end) {
                    var queryParams = '';
                    if (start && end) {
                        queryParams = '?start_date=' + start + '&end_date=' + end;
                    }

                    $('#linkProductsSold').attr('href', "{{ route('reports.statistics.products_sold') }}" + queryParams);
                    $('#linkRevenue').attr('href', "{{ route('reports.statistics.revenue') }}" + queryParams);
                    // Inventory doesn't strictly need date filter but we can pass it for consistency
                    $('#linkInventory').attr('href', "{{ route('reports.statistics.inventory') }}" + queryParams);
                    $('#linkPurchases').attr('href', "{{ route('reports.statistics.purchases') }}" + queryParams);
                    $('#linkProfit').attr('href', "{{ route('reports.statistics.profit') }}" + queryParams);
                    $('#linkCashFlow').attr('href', "{{ route('reports.statistics.cash_flow') }}" + queryParams);
                }

                function updateDashboard(data) {
                    $('#productsSold').text(data.productsSold);
                    $('#totalRevenue').text(data.totalRevenue);
                    $('#totalUnsoldProducts').text(data.totalUnsoldProducts);
                    $('#totalPurchases').text(data.totalPurchases);
                    $('#totalProfit').text(data.totalProfit);
                    $('#availableMoney').text(data.availableMoney);
                }
            });

            var chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)', borderDash: [5, 5] },
                        ticks: { color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    }
                },
                elements: {
                    line: { tension: 0.4 },
                    point: { radius: 0, hoverRadius: 6, backgroundColor: '#fff', borderWidth: 2 }
                }
            };

            var productsSoldCtx = document.getElementById('productsSoldChart').getContext('2d');
            var salesGradient = productsSoldCtx.createLinearGradient(0, 0, 0, 300);
            salesGradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
            salesGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            new Chart(productsSoldCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData->keys()) !!},
                    datasets: [{
                        label: "{{ __('app.dashboard.products_sold') }}",
                        data: {!! json_encode($monthlyData->pluck('total_sold')) !!},
                        borderColor: '#6366f1',
                        borderWidth: 3,
                        fill: true,
                        backgroundColor: salesGradient,
                        pointBackgroundColor: '#6366f1',
                        pointHoverRadius: 6,
                        pointRadius: 0
                    }]
                },
                options: chartOptions
            });

            var profitCtx = document.getElementById('profitChart').getContext('2d');
            var revenueGradient = profitCtx.createLinearGradient(0, 0, 0, 300);
            revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(profitCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyData->keys()) !!},
                    datasets: [{
                        label: "{{ __('app.dashboard.total_revenue') }}",
                        data: {!! json_encode($monthlyData->pluck('total_revenue')) !!},
                        borderColor: '#10b981',
                        borderWidth: 3,
                        fill: true,
                        backgroundColor: revenueGradient,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6,
                        pointRadius: 0
                    }]
                },
                options: chartOptions
            });

            // Top Selling Chart
            var topSellingCtx = document.getElementById('topSellingChart').getContext('2d');
            new Chart(topSellingCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topSellingProducts->map(fn($s) => $s->product->name ?? 'Unknown')->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($topSellingProducts->pluck('total_quantity')->toArray()) !!},
                        backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });

            // Top Profitable Chart
            var topProfitableCtx = document.getElementById('topProfitableChart').getContext('2d');
            new Chart(topProfitableCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topProfitableProducts->map(fn($p) => $p->product->name ?? 'Unknown')->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($topProfitableProducts->pluck('total_profit')->toArray()) !!},
                        backgroundColor: ['#10b981', '#059669', '#047857', '#065f46', '#064e3b'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        </script>
    @endpush