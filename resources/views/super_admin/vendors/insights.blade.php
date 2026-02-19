@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0 font-weight-bold text-gradient-premium">{{ $vendor->business_name }}</h2>
                        <p class="text-muted opacity-75 mb-0">{{ __('app.platform.vendor_insights_for') }} {{ $vendor->owner->name }}
                        </p>
                    </div>
                    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary border-0 h6 shadow-sm"
                        style="border-radius: 12px;">
                        <i class="la la-arrow-left"></i> {{ __('app.platform.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="premium-card mb-4 animate-fade-in-up">
            <div class="card-body p-0">
                <form action="{{ route('super-admin.vendors.insights', $vendor) }}" method="GET"
                    class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold small text-muted mb-1">{{ __('app.sidebar.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold small text-muted mb-1">{{ __('app.sidebar.end_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block px-4">
                            <i class="la la-filter"></i> {{ __('app.platform.filter_results') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Insight Stats -->
        <div class="row animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="col-md-6 mb-4">
                <div class="premium-card h-100" style="--card-accent: var(--p-emerald); --bg-accent: #ecfdf5;">
                    <div class="card-body p-0 text-center">
                        <div class="card-icon-wrapper mx-auto mb-3">
                            <i class="la la-chart-bar"></i>
                        </div>
                        <h5 class="text-muted small uppercase font-weight-bold mb-1">
                            {{ __('app.platform.total_sales_period') }}
                        </h5>
                        <h2 class="font-weight-bold text-white mb-0">{{ number_format($totalSales, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="premium-card h-100" style="--card-accent: var(--p-indigo); --bg-accent: #eef2ff;">
                    <div class="card-body p-0 text-center">
                        <div class="card-icon-wrapper mx-auto mb-3">
                            <i class="la la-box-open"></i>
                        </div>
                        <h5 class="text-muted small uppercase font-weight-bold mb-1">{{ __('app.platform.inventory_size') }}
                        </h5>
                        <h2 class="font-weight-bold text-white mb-0">{{ $totalProducts }} {{ __('app.sidebar.products') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row animate-fade-in-up" style="animation-delay: 0.2s;">
            <!-- Recent Sales -->
            <div class="col-lg-8">
                <div class="premium-card p-0 overflow-hidden mb-4">
                    <div class="p-4 bg-white border-bottom border-light">
                        <h5 class="font-weight-bold mb-0 text-indigo">{{ __('app.platform.recent_sales') }}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="border-0 px-4 py-3">{{ __('app.platform.invoice_no') }}</th>
                                    <th class="border-0">{{ __('app.sidebar.clients') }}</th>
                                    <th class="border-0 text-right px-4">{{ __('app.platform.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr class="transition-all hover-bg-light">
                                        <td class="px-4 py-3 font-weight-bold text-white">#{{ $sale->id }}</td>
                                        <td class="py-3 text-white opacity-75">{{ $sale->client->name ?? 'Guest' }}</td>
                                        <td class="text-right px-4 py-3 font-weight-bold text-white">
                                            {{ number_format($sale->paid_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            {{ __('app.platform.no_sales_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-lg-4">
                <div class="premium-card border-0 mb-4" style="--card-accent: var(--p-indigo);">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light-primary p-2 rounded-lg mr-3">
                            <i class="la la-star text-primary h4 mb-0"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0 text-indigo">{{ __('app.platform.top_products') }}</h5>
                    </div>
                    <div class="mt-2">
                        @forelse($topProducts as $product)
                            <div class="d-flex align-items-center mb-4 p-2 rounded-lg transition-all hover-bg-light">
                                <div class="bg-slate-100 p-2 rounded-xl mr-3" style="width: 45px; text-align: center;">
                                    <i class="la la-box text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 font-weight-bold small text-white">{{ $product->name }}</h6>
                                    <small class="text-muted font-weight-bold">{{ $product->sales_sum_quantity ?? 0 }}
                                        {{ __('app.platform.units_sold') }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">{{ __('app.platform.no_products_found') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-indigo {
            color: var(--p-indigo-dark) !important;
        }

        .text-emerald {
            color: var(--p-emerald-dark);
        }
    </style>
@endsection