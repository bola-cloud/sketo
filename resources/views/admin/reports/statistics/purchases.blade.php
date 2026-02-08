@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.reports.purchases_title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.dashboard.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('app.reports.purchases_title') }}</li>
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
                    <form action="{{ route('reports.statistics.purchases') }}" method="GET">
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
                                <button type="submit" class="btn btn-danger btn-block">
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
                                    <th>{{ __('app.reports.purchase_code') }}</th>
                                    <th>{{ __('app.suppliers.supplier_name') }}</th>
                                    <th>{{ __('app.reports.total_amount') }}</th>
                                    <th>{{ __('app.reports.paid_amount') }}</th>
                                    <th>{{ __('app.reports.remaining_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td class="text-bold-600">{{ $purchase->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('purchases.show', $purchase->id) }}" class="text-bold-700">
                                                #{{ $purchase->purchase_code }}
                                            </a>
                                        </td>
                                        <td>{{ $purchase->supplier->name ?? __('app.common.not_specified') }}</td>
                                        <td class="text-bold-700 danger">{{ number_format($purchase->total_amount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                                        </td>
                                        <td class="success">{{ number_format($purchase->paid_amount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                        <td class="warning">{{ number_format($purchase->remaining_amount, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">{{ __('app.reports.no_purchases') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $purchases->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
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