@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            z-index: 10000;
            width: auto;
            max-width: 300px;
        }

        .flatpickr-calendar .flatpickr-month {
            background-color: #fff;
            border-radius: 5px;
        }

        .flatpickr-calendar.open {
            visibility: visible;
            opacity: 1;
        }

        /* Glassmorphism KPI Cards */
        .card.pull-up {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        .card.pull-up:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.1);
            border-color: var(--sketo-primary);
        }

        .media-body h3 {
            font-weight: 700 !important;
            font-size: 1.6rem !important;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .media-body h6 {
            font-weight: 600;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .media i {
            padding: 15px;
            border-radius: 15px;
            background: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1);
        }

        /* Gradient overrides for icons */
        .icon-basket-loaded {
            color: #3b82f6 !important;
        }

        .icon-pie-chart {
            color: #f59e0b !important;
        }

        .icon-handbag {
            color: #10b981 !important;
        }

        .icon-wallet {
            color: #ef4444 !important;
        }

        .icon-graph {
            color: #6366f1 !important;
        }

        .datepicker {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            font-family: 'Inter', sans-serif;
            background: #fff;
        }

        .datepicker:focus {
            border-color: var(--sketo-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .alert-danger {
            border-radius: 15px;
            border: none;
            background: #fef2f2;
            color: #991b1b;
            box-shadow: 0 4px 12px rgba(153, 27, 27, 0.05);
        }
    </style>

    @php
        $user = auth()->user();
        $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
    @endphp
    @if($user->hasRole('admin'))
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="info" id="productsSold">{{ $productsSold }}</h3>
                                    <h6>المنتجات المباعة</h6>
                                </div>
                                <div>
                                    <i class="icon-basket-loaded info font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="warning" id="totalRevenue">{{ $totalRevenue }} ج.م</h3>
                                    <h6>إجمالي الإيرادات</h6>
                                </div>
                                <div>
                                    <i class="icon-pie-chart warning font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="success" id="totalUnsoldProducts">{{ $totalUnsoldProducts }}</h6>
                                        <h6>المنتجات غير المباعة</h6>
                                </div>
                                <div>
                                    <i class="icon-handbag success font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="danger" id="totalPurchases"> {{ $totalPurchases }} ج.م </h3>
                                    <h6>إجمالي المشتريات</h6>
                                </div>
                                <div>
                                    <i class="icon-wallet danger font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="primary" id="totalProfit"> {{ $totalProfit }} ج.م</h3>
                                    <h6>إجمالي الأرباح</h6>
                                </div>
                                <div>
                                    <i class="icon-graph primary font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card pull-up">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="primary" id="availableMoney">{{ $availableMoney }} ج.م</h3>
                                    <h6>المبلغ المتوفر</h6>
                                </div>
                                <div>
                                    <i class="icon-wallet primary font-large-2 float-left"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Date Range Picker -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
                    <h5 class="mb-3 text-bold-600"><i class="ft-calendar primary mr-1"></i> تصفية حسب التاريخ</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="start_date" class="text-muted small">تاريخ البداية</label>
                                <input type="text" id="start_date" name="start_date" class="form-control datepicker"
                                    placeholder="اختر التاريخ...">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label for="end_date" class="text-muted small">تاريخ النهاية</label>
                                <input type="text" id="end_date" name="end_date" class="form-control datepicker"
                                    placeholder="اختر التاريخ...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($lowStockProducts->count() > 0)
        <div class="alert alert-danger d-flex align-items-center p-3 mb-4 mt-3">
            <i class="ft-alert-triangle mr-3 font-large-1"></i>
            <div>
                <h5 class="alert-heading text-bold-700 mb-1">تنبيه: منتجات قاربت على النفاد</h5>
                <ul class="mb-0 list-unstyled">
                    @foreach($lowStockProducts as $product)
                        <li><i class="ft-check-circle small mr-1"></i> المنتج <strong>{{ $product->name }}</strong> وصل إلى الحد
                            الأدنى (الكمية: {{ $product->quantity }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Line Chart for Monthly Products Sold -->
    {{-- <div class="row">
        <div class="col-xl-12 col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> المنتجات المباعة شهريا </h4>
                </div>
                <div class="card-body">
                    <canvas id="productsSoldChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart for Monthly Profit -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> العائد شهريا </h4>
                </div>
                <div class="card-body">
                    <canvas id="profitChart"></canvas>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('scripts')
    {{--
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
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

            function updateDashboard(data) {
                $('#productsSold').text(data.productsSold);
                $('#totalRevenue').text('ج.م ' + data.totalRevenue);
                $('#totalUnsoldProducts').text(data.totalUnsoldProducts);
                $('#totalPurchases').text('ج.م ' + data.totalPurchases);
                $('#totalProfit').text('ج.م ' + data.totalProfit);
                $('#availableMoney').text('ج.م ' + data.availableMoney); // Update available money
            }


        });


        var productsSoldCtx = document.getElementById('productsSoldChart').getContext('2d');
        var productsSoldChart = new Chart(productsSoldCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData->keys()) !!},
                datasets: [
                    {
                        label: 'المنتجات المباعة',
                        data: {!! json_encode($monthlyData->pluck('total_sold')) !!},
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'الكمية'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'الشهر'
                        }
                    }
                }
            }
        });

        var profitCtx = document.getElementById('profitChart').getContext('2d');
        var profitChart = new Chart(profitCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData->keys()) !!},
                datasets: [
                    {
                        label: 'الإيرادات الشهرية',
                        data: {!! json_encode($monthlyData->pluck('total_revenue')) !!},
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'الإيرادات ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'الشهر'
                        }
                    }
                }
            }
        });
    </script>
@endpush