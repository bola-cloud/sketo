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

        .bg-gradient-x-indigo-blue {
            background: linear-gradient(135deg, var(--p-indigo-dark), var(--p-indigo));
        }

        .bg-white-10 {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>

    @php
        $user = auth()->user();
    @endphp

    <div class="container-fluid py-4">
        {{-- Welcome Header --}}
        <div class="row mb-5 animate-fade-in-up">
            <div class="col-12">
                <div class="premium-card welcome-banner"
                    style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.1)); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h1 class="display-4 font-weight-bold mb-2 text-gradient-premium">
                                {{ __('app.common.welcome') }}, {{ Auth::user()->name }}!
                            </h1>
                            <p class="h5 text-muted opacity-75 mb-0">
                                {{ __('app.dashboard.store_health_msg') }}
                            </p>
                        </div>
                        <div class="col-md-5 text-right d-none d-md-block">
                            <div class="d-inline-flex align-items-center p-3 rounded-20 bg-glass shadow-sm">
                                <div class="bg-emerald p-3 rounded-lg mr-3 shadow-glow">
                                    <i class="la la-check text-white h4 mb-0"></i>
                                </div>
                                <div class="text-left">
                                    <small
                                        class="text-muted d-block uppercase font-weight-bold">{{ __('app.dashboard.store_health') }}</small>
                                    <span
                                        class="text-emerald font-weight-bold h5 mb-0">{{ __('app.dashboard.optimized') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($lowStockProducts->count() > 0)
            <div class="row animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="col-12 mb-4">
                    <div class="premium-card border-0" style="background: #fff1f2; --card-accent: var(--p-rose);">
                        <div class="d-flex align-items-center">
                            <div class="bg-rose-100 p-3 rounded-xl mr-3" style="background: #ffe4e6;">
                                <i class="la la-exclamation-triangle text-rose h4 mb-0" style="color: var(--p-rose-dark);"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-1" style="color: #9f1239;">
                                    {{ __('app.dashboard.low_stock_alert') }}
                                </h5>
                                <p class="text-rose-700 mb-0 small">
                                    @foreach($lowStockProducts->take(2) as $p)
                                        {{ $p->name }} ({{ $p->quantity }}){{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                    @if($lowStockProducts->count() > 2) +{{ $lowStockProducts->count() - 2 }} more @endif
                                </p>
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-white ml-auto shadow-sm"
                                style="border-radius: 8px;">{{ __('app.dashboard.restock_now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($expiringProducts->count() > 0)
            <div class="row animate-fade-in-up" style="animation-delay: 0.15s;">
                <div class="col-12 mb-4">
                    <div class="premium-card border-0" style="background: #fffbeb; --card-accent: #f59e0b;">
                        <div class="d-flex align-items-center">
                            <div class="bg-amber-100 p-3 rounded-xl mr-3" style="background: #fef3c7;">
                                <i class="la la-clock h4 mb-0" style="color: #d97706;"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold mb-1" style="color: #92400e;">
                                    {{ __('app.dashboard.expiry_alert') }}
                                </h5>
                                <p class="mb-0 small" style="color: #b45309;">
                                    @foreach($expiringProducts->take(2) as $p)
                                        {{ $p->name }} ({{ $p->expiry_date }}){{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                    @if($expiringProducts->count() > 2) +{{ $expiringProducts->count() - 2 }} more @endif
                                </p>
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-white ml-auto shadow-sm"
                                style="border-radius: 8px;">{{ __('app.sidebar.view_products') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Top KPI Row --}}
        <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.products_sold') }}" id="linkProductsSold"
                    class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-emerald);">
                        <div class="card-icon-wrapper"
                            style="background: rgba(16, 185, 129, 0.1); color: var(--p-emerald);">
                            <i class="la la-shopping-cart"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.products_sold') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="productsSold">{{ number_format($productsSold) }}
                        </h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.revenue') }}" id="linkRevenue" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-indigo);">
                        <div class="card-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--p-indigo);">
                            <i class="la la-wallet"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_revenue') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalRevenue">{{ number_format($totalRevenue, 2) }}
                        </h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('reports.statistics.profit') }}" id="linkProfit" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-purple);">
                        <div class="card-icon-wrapper" style="background: rgba(168, 85, 247, 0.1); color: var(--p-purple);">
                            <i class="la la-chart-pie"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_profit') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalProfit">{{ number_format($totalProfit, 2) }}
                        </h2>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: #f59e0b;">
                        <div class="card-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="la la-box"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.dashboard.total_products') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalUnsoldProducts">
                            {{ number_format($totalUnsoldProducts) }}
                        </h2>
                    </div>
                </a>
            </div>
        </div>

        {{-- Bottom KPI and Filter Row --}}
        <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.2s;">
            <!-- Total Purchases -->
            <div class="col-xl-3 col-lg-6 col-12 mb-4">
                <a href="{{ route('reports.statistics.purchases') }}" id="linkPurchases" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-rose); --bg-accent: #fff1f2;">
                        <div class="card-icon-wrapper">
                            <i class="la la-wallet"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">
                            {{ __('app.dashboard.total_purchases') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="totalPurchases">
                            {{ number_format($totalPurchases, 2) }}
                        </h2>
                    </div>
                </a>
            </div>

            <!-- Available Money -->
            <div class="col-xl-3 col-lg-6 col-12 mb-4">
                <a href="{{ route('reports.statistics.cash_flow') }}" id="linkCashFlow" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-indigo); --bg-accent: #eef2ff;">
                        <div class="card-icon-wrapper">
                            <i class="la la-money-bill-wave"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">
                            {{ __('app.dashboard.available_money') }}
                        </p>
                        <h2 class="font-weight-bold mb-0 text-white" id="availableMoney">
                            {{ number_format($availableMoney, 2) }}
                        </h2>
                    </div>
                </a>
            </div>

            <!-- Date Filter Card -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="premium-card h-100 p-4">
                    <h5 class="mb-4 font-weight-bold text-white"><i class="la la-calendar-alt text-indigo mr-2"></i>
                        {{ __('app.dashboard.filter_by_date') }}</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date"
                                class="text-muted small font-weight-bold uppercase mb-2 d-block">{{ __('app.dashboard.start_date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-slate-50 border-right-0"
                                        style="border-radius: 12px 0 0 12px; border-color: var(--p-slate-200);"><i
                                            class="la la-calendar"></i></span>
                                </div>
                                <input type="text" id="start_date" name="start_date"
                                    class="form-control datepicker bg-slate-50"
                                    style="border-radius: 0 12px 12px 0; border-left: none; border-color: var(--p-slate-200);"
                                    placeholder="{{ __('app.common.select_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date"
                                class="text-muted small font-weight-bold uppercase mb-2 d-block">{{ __('app.dashboard.end_date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-slate-50 border-right-0"
                                        style="border-radius: 12px 0 0 12px; border-color: var(--p-slate-200);"><i
                                            class="la la-calendar"></i></span>
                                </div>
                                <input type="text" id="end_date" name="end_date" class="form-control datepicker bg-slate-50"
                                    style="border-radius: 0 12px 12px 0; border-left: none; border-color: var(--p-slate-200);"
                                    placeholder="{{ __('app.common.select_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Line Chart for Monthly Products Sold -->
    <div class="row animate-fade-in-up" style="animation-delay: 0.4s;">
        <div class="col-xl-6 col-12 mb-4">
            <div class="premium-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-indigo-gradient p-2 rounded-xl mr-3"
                        style="background: linear-gradient(135deg, var(--p-indigo), var(--p-blue)); line-height: 1;">
                        <i class="la la-chart-bar text-white"></i>
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

        <!-- Line Chart for Monthly Profit -->
        <div class="col-xl-6 col-12 mb-4">
            <div class="premium-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-emerald-gradient p-2 rounded-xl mr-3"
                        style="background: linear-gradient(135deg, var(--p-emerald), var(--p-teal)); line-height: 1;">
                        <i class="la la-chart-line text-white"></i>
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
                    $('#availableMoney').text(data.availableMoney); // Update available money
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
        </script>
    @endpush