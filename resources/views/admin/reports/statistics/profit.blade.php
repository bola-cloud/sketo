@extends('layouts.admin')

@section('title', __('app.reports.profit_title'))

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.reports.profit_title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.dashboard.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('app.reports.profit_title') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Filter Card -->
        <div class="card pull-up border-0 shadow-sm mb-4"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-content">
                <div class="card-body">
                    <form action="{{ route('reports.statistics.profit') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">{{ __('app.reports.start_date') }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">{{ __('app.reports.end_date') }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="la la-filter"></i> {{ __('app.reports.filter_btn') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('app.reports.transaction_date') }}</th>
                                    <th>{{ __('app.cashier.product') }}</th>
                                    <th>{{ __('app.cashier.quantity') }}</th>
                                    <th>{{ __('app.reports.unit_price') }}</th>
                                    <th>{{ __('app.reports.total_sales') }}</th>
                                    <th>{{ __('app.reports.net_profit') }}</th>
                                    <th>{{ __('app.reports.invoice_code') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="text-bold-600">{{ $sale->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $sale->product->name }}</td>
                                        <td><span class="badge badge-soft-info">{{ $sale->quantity }}</span></td>
                                        <td>{{ number_format($sale->unit_price, 2) }} {{ __('app.common.currency') }}</td>
                                        <td>{{ number_format($sale->total_price, 2) }} {{ __('app.common.currency') }}</td>
                                        <td class="text-bold-700 success">+ {{ number_format($sale->profit, 2) }}
                                            {{ __('app.common.currency') }}</td>
                                        <td>
                                            <a href="{{ route('invoices.show', $sale->invoice_id) }}"
                                                class="badge badge-soft-secondary">
                                                #{{ $sale->invoice->invoice_code ?? $sale->invoice_id }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">{{ __('app.reports.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $sales->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .badge-soft-info {
            color: #0891b2;
            background: rgba(6, 182, 212, 0.1);
            border: none;
        }

        .badge-soft-secondary {
            color: #64748b;
            background: rgba(100, 116, 139, 0.1);
            border: none;
        }

        .success {
            color: #16a34a !important;
        }

        .table-premium th {
            font-weight: 700;
            border-top: none;
        }

        .table-premium td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endsection