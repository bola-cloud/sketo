@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.brands.details') }}: {{ $brand->name }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('brands.index') }}">{{ __('app.brands.all_brands') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.common.details') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-warning round px-2 shadow text-white mr-1">
                    <i class="la la-edit"></i> {{ __('app.brands.edit') }}
                </a>
                <a href="{{ route('brands.index') }}" class="btn btn-light round px-2">
                    {{ __('app.brands.back_to_list') }}
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <!-- Brand Info Card -->
            <div class="col-md-4 col-12">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="text-center mb-2">
                                <div class="avatar bg-soft-info p-2 mb-1"
                                    style="width: 80px; height: 80px; font-size: 40px; border-radius: 20px;">
                                    <i class="la la-tag info"></i>
                                </div>
                                <h4 class="text-bold-700">{{ $brand->name }}</h4>
                            </div>
                            <hr class="my-2 border-primary opacity-10">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('app.brands.products_count') }}:</span>
                                <span class="badge badge-soft-info">{{ $brand->products->count() }}
                                    {{ __('app.brands.product') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('app.brands.created_at') }}:</span>
                                <span class="text-bold-600">{{ $brand->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="mt-2">
                                <label class="text-muted small">{{ __('app.brands.description') }}:</label>
                                <p class="text-bold-600">
                                    {{ $brand->description ?: __('app.brands.no_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="col-md-8 col-12">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title text-bold-700"><i class="la la-box info"></i>
                            {{ __('app.brands.related_products') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body p-0">
                            @if($products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-premium mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('app.products.name') }}</th>
                                                <th>{{ __('app.products.category') }}</th>
                                                <th>{{ __('app.products.selling_price') }}</th>
                                                <th>{{ __('app.brands.available_quantity') }}</th>
                                                <th class="text-right">{{ __('app.common.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                                <tr>
                                                    <td>
                                                        <span class="text-bold-600">{{ $product->name }}</span>
                                                    </td>
                                                    <td>{{ $product->category ? $product->category->name : __('app.products.undefined') }}
                                                    </td>
                                                    <td class="primary text-bold-700">
                                                        {{ number_format($product->selling_price, 2) }}
                                                        {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $product->quantity <= $product->threshold ? 'badge-danger' : 'badge-soft-success' }}">
                                                            {{ $product->quantity }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="{{ route('products.show', $product->id) }}"
                                                            class="btn btn-sm btn-soft-primary">
                                                            {{ __('app.common.view') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center">
                                    {{ $products->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="la la-info-circle font-large-2 text-muted mb-1"></i>
                                    <p class="text-muted">{{ __('app.brands.no_products') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-info {
            background: rgba(6, 182, 212, 0.1);
        }

        .bg-soft-primary {
            background: rgba(59, 130, 246, 0.1);
        }

        .badge-soft-success {
            color: #16a34a;
            background: rgba(22, 163, 74, 0.1);
            border: none;
        }

        .table-premium th {
            font-weight: 700;
            color: #1e293b;
            border-top: none;
            padding: 1rem;
        }

        .table-premium td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem;
        }
    </style>
@endsection