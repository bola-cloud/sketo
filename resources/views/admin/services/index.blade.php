@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.sidebar.services') ?? 'الخدمات' }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.sidebar.services') ?? 'الخدمات' }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12 text-right">
            <a href="{{ route('services.create') }}" class="btn btn-primary round px-2 shadow">
                <i class="la la-plus"></i> {{ __('app.sidebar.add_service') ?? 'إضافة خدمة' }}
            </a>
        </div>
    </div>

    <div class="content-body">
        @php
            $user = auth()->user();
            $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
        @endphp

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>{{ __('app.common.success') }}!</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>{{ __('app.common.error') }}!</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Filters Section -->
        <div class="card pull-up border-0 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-content">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="form-group mb-0">
                                <label class="text-muted small">{{ __('app.products.search') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent border-primary border-right-0">
                                            <i class="la la-search primary"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control round border-primary"
                                        id="search-barcode" value="{{ request('search') }}"
                                        placeholder="{{ __('app.products.search_placeholder') }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group mb-0">
                                <label class="text-muted small">{{ __('app.products.category') }}</label>
                                <select class="form-control round border-primary" id="category-filter">
                                    <option value="">{{ __('app.products.all_categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="button" id="search-button" class="btn btn-primary btn-block round shadow">
                                <i class="la la-filter"></i> {{ __('app.products.filter') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="filter-form" method="GET" action="{{ route('services.index') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        </form>

        <!-- Services Table -->
        <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th>{{ __('app.products.name') }}</th>
                                    <th>{{ __('app.products.category') }}</th>
                                    @if($user->hasRole('admin'))
                                        <th>{{ __('app.products.cost_price') }}</th>
                                    @endif
                                    <th>{{ __('app.products.selling_price') }}</th>
                                    <th>{{ __('app.products.barcode') }}</th>
                                    <th class="text-right">{{ __('app.products.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td><span class="badge badge-soft-secondary">{{ $service->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($service->image)
                                                    <img src="{{ asset('storage/' . $service->image) }}" class="avatar-lg mr-2"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                                                @else
                                                    <div class="avatar bg-soft-primary mr-2" style="width: 50px; height: 50px; border-radius: 10px;">
                                                        <i class="la la-image primary" style="font-size: 24px;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-bold-700 block">{{ $service->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-bold-600">{{ $service->category->name ?? __('app.products.undefined') }}</span>
                                        </td>
                                        @if($user->hasRole('admin'))
                                            <td class="text-bold-600">{{ number_format($service->cost_price, 2) }} {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</td>
                                        @endif
                                        <td class="text-bold-700 primary">{{ number_format($service->selling_price, 2) }} {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</td>
                                        <td class="text-center">
                                            @if($service->barcode_path)
                                                <div class="barcode-container py-1">
                                                    <img src="{{ asset('storage/' . $service->barcode_path) }}"
                                                        alt="{{ $service->barcode }}"
                                                        class="d-block mx-auto mb-1" 
                                                        style="max-height: 30px; max-width: 140px; filter: grayscale(1) brightness(1.5) opacity(0.8);">
                                                    <span class="badge badge-light" style="font-family: monospace; letter-spacing: 1px;">{{ $service->barcode }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted small">{{ __('app.products.no_barcode') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($isAdmin || $permissions->contains('edit-products'))
                                                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-soft-warning mr-1">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                @if($isAdmin || $permissions->contains('delete-products'))
                                                    <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline;"
                                                        onsubmit="return confirm('{{ __('app.products.delete_confirm') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-soft-danger">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <i class="la la-lock text-muted"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="la la-server font-large-3 text-muted mb-2"></i>
                                            <h5 class="text-muted">{{ __('app.products.not_found') }}</h5>
                                            <a href="{{ route('services.create') }}" class="btn btn-primary round mt-1">{{ __('app.sidebar.add_service') ?? 'إضافة خدمة جديدة' }}</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    {{ $services->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary { background: rgba(59, 130, 246, 0.1); }
        .badge-soft-secondary { color: #64748b; background: rgba(100, 116, 139, 0.1); border: none; }
        .btn-soft-warning { color: #d97706; background: rgba(217, 119, 6, 0.1); border: none; }
        .btn-soft-warning:hover { background: #d97706; color: #fff; }
        .btn-soft-danger { color: #ef4444; background: rgba(239, 68, 68, 0.1); border: none; }
        .btn-soft-danger:hover { background: #ef4444; color: #fff; }
        .table-premium th { font-weight: 700; color: #1e293b; border-top: none; padding: 1.25rem 1rem; }
        .table-premium td { vertical-align: middle; border-bottom: 1px solid #f1f5f9; padding: 1rem; }
    </style>

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#search-button').on('click', function () {
                $('#filter-form').submit();
            });

            $('#search-barcode').on('input', function () {
                $('input[name="search"]').val($(this).val());
            });

            $('#category-filter').on('change', function () {
                $('input[name="category_id"]').val($(this).val());
                $('#filter-form').submit();
            });



            $('#search-barcode').on('keypress', function (e) {
                if (e.which === 13) {
                    $('#filter-form').submit();
                }
            });
        });
    </script>
@endsection
