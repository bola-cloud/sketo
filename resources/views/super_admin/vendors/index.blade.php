@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="premium-card shadow-sm border-0 mb-4 animate-fade-in-up">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 font-weight-bold text-gradient-premium">{{ __('app.sidebar.vendors') }}</h2>
                            <p class="text-muted mb-0 small opacity-75">{{ __('app.platform.manage_stores') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('super-admin.vendors.create') }}" class="btn btn-primary shadow-glow">
                                <i class="la la-plus-circle"></i> {{ __('app.platform.add_vendor') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 border-0">
                                            {{ __('app.platform.store_name') }}
                                        </th>
                                        <th class="px-4 py-3 border-0">
                                            {{ __('app.platform.owner') }}
                                        </th>
                                        <th class="px-4 py-3 border-0">
                                            {{ __('app.platform.status') }}
                                        </th>
                                        <th class="px-4 py-3 border-0">
                                            {{ __('app.platform.subscription_ends') }}
                                        </th>
                                        <th class="px-4 py-3 border-0 text-center">{{ __('app.platform.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendors as $vendor)
                                        <tr class="hover-bg-glass">
                                            <td class="px-4 py-4">
                                                <div class="font-weight-bold text-white">
                                                    {{ $vendor->business_name }}
                                                </div>
                                                <small class="text-muted">{{ $vendor->domain ?? 'Standard Subdomain' }}</small>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-muted">{{ $vendor->owner->name }}</div>
                                                <small class="text-muted">{{ $vendor->owner->email }}</small>
                                            </td>
                                            <td class="px-4 py-4">
                                                @if($vendor->status === 'active')
                                                    <span class="badge badge-pill badge-success p-2 px-3">{{ __('app.platform.active') }}</span>
                                                @else
                                                    <span class="badge badge-pill badge-danger p-2 px-3">{{ __('app.platform.' . $vendor->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="text-muted {{ $vendor->subscription_ends_at && $vendor->subscription_ends_at->isPast() ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ $vendor->subscription_ends_at ? $vendor->subscription_ends_at->format('Y-m-d') : __('app.platform.no_limit') }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                                                    <form action="{{ route('super-admin.vendors.toggleStatus', $vendor) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $vendor->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                            style="border-radius: 8px;">
                                                            <i class="la {{ $vendor->status === 'active' ? 'la-pause' : 'la-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('super-admin.vendors.show', $vendor) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        style="border-radius: 8px;">
                                                        <i class="la la-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .badge { font-weight: 600; }
        body.light-mode .text-white { color: #0f172a !important; }
        body.light-mode .text-muted { color: #475569 !important; }
        .gap-2 { gap: 0.5rem; }

        /* Icon visibility in light mode action buttons */
        body.light-mode .btn-outline-primary i { color: #6366f1 !important; }
        body.light-mode .btn-outline-danger i { color: #ef4444 !important; }
        body.light-mode .btn-outline-success i { color: #10b981 !important; }

        /* Table header contrast in light mode */
        body.light-mode .table thead th {
            background: rgba(0, 0, 0, 0.05) !important;
            color: #1e293b !important;
            border-bottom: 2px solid rgba(0,0,0,0.05) !important;
        }

        body.light-mode .hover-bg-glass:hover {
            background: rgba(0, 0, 0, 0.02) !important;
        }
    </style>
@endsection