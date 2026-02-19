@extends('layouts.admin')

@section('title', __('app.reports.inventory_title'))

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.reports.inventory_title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('dashboard') }}">{{ __('app.dashboard.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('app.reports.inventory_title') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Summary Header -->
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card pull-up border-0 shadow-sm bg-primary bg-lighten-4">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="primary">{{ number_format($products->total()) }}</h3>
                                <h6 class="text-muted">{{ __('app.reports.total_items') }}</h6>
                            </div>
                            <div>
                                <i class="la la-cubes primary font-large-2"></i>
                            </div>
                        </div>
                    </div>
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
                                    <th> {{ __('app.cashier.product') }}</th>
                                    <th>{{ __('app.products.category') }}</th>
                                    <th>{{ __('app.products.brand') }}</th>
                                    <th>{{ __('app.reports.quantity_available') }}</th>
                                    <th>{{ __('app.products.cost_price') }}</th>
                                    <th>{{ __('app.cashier.price') }}</th>
                                    <th>{{ __('app.reports.min_threshold') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr @if($product->quantity <= $product->threshold) class="bg-soft-danger" @endif>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-soft-primary mr-2">
                                                    <i class="la la-cube primary"></i>
                                                </div>
                                                <span class="text-bold-600">{{ $product->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ $product->brand->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $product->quantity <= $product->threshold ? 'badge-danger' : 'badge-soft-success' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($product->cost_price, 2) }} {{ __('app.common.currency') }}</td>
                                        <td class="text-bold-700 primary">{{ number_format($product->selling_price, 2) }}
                                            {{ __('app.common.currency') }}</td>
                                        <td>{{ $product->threshold }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">{{ __('app.reports.no_stock') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.05);
        }

        .badge-soft-success {
            color: #16a34a;
            background: rgba(22, 163, 74, 0.1);
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