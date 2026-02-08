@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.reports.revenue_title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.dashboard.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('app.reports.revenue_title') }}</li>
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
                    <form action="{{ route('reports.statistics.revenue') }}" method="GET">
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
                                <button type="submit" class="btn btn-warning btn-block text-white">
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
                                    <th>{{ __('app.common.date') }}</th>
                                    <th>{{ __('app.reports.invoice_code') }}</th>
                                    <th>{{ __('app.clients.client_name') }}</th>
                                    <th>{{ __('app.reports.total_amount') }}</th>
                                    <th>{{ __('app.reports.paid_amount') }}</th>
                                    <th>{{ __('app.cashier.discount') }}</th>
                                    <th>{{ __('app.common.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td class="text-bold-600">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-bold-700">
                                                #{{ $invoice->invoice_code }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->client->name ?? __('app.reports.client_cash') }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                        <td class="text-bold-700 success">{{ number_format($invoice->paid_amount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                        <td class="danger">{{ number_format($invoice->discount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                        <td>
                                            @if($invoice->status == 'paid')
                                                <span class="badge badge-soft-success">{{ __('app.reports.status_paid') }}</span>
                                            @else
                                                <span class="badge badge-soft-warning">{{ __('app.reports.status_partial') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">{{ __('app.reports.no_revenue') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $invoices->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .badge-soft-success {
            color: #16a34a;
            background: rgba(22, 163, 74, 0.1);
            border: none;
        }

        .badge-soft-warning {
            color: #d97706;
            background: rgba(217, 119, 6, 0.1);
            border: none;
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