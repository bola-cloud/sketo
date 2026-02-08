@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4"
                    style="border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h2 class="mb-0 font-weight-bold" style="color: #1e293b;">{{ __('app.sidebar.vendors') }}</h2>
                            <p class="text-muted mb-0">{{ __('Manage all stores on the platform') }}</p>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="vertical-align: middle;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th class="px-4 py-3 border-0" style="color: #64748b; font-weight: 600;">
                                            {{ __('Store Name') }}</th>
                                        <th class="px-4 py-3 border-0" style="color: #64748b; font-weight: 600;">
                                            {{ __('Owner') }}</th>
                                        <th class="px-4 py-3 border-0" style="color: #64748b; font-weight: 600;">
                                            {{ __('Status') }}</th>
                                        <th class="px-4 py-3 border-0" style="color: #64748b; font-weight: 600;">
                                            {{ __('Subscription Ends') }}</th>
                                        <th class="px-4 py-3 border-0 text-center"
                                            style="color: #64748b; font-weight: 600;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendors as $vendor)
                                        <tr>
                                            <td class="px-4 py-4">
                                                <div class="font-weight-bold" style="color: #334155;">
                                                    {{ $vendor->business_name }}</div>
                                                <small class="text-muted">{{ $vendor->domain ?? 'Standard Subdomain' }}</small>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div style="color: #475569;">{{ $vendor->owner->name }}</div>
                                                <small class="text-muted">{{ $vendor->owner->email }}</small>
                                            </td>
                                            <td class="px-4 py-4">
                                                @if($vendor->status === 'active')
                                                    <span class="badge badge-pill badge-light-success p-2 px-3"
                                                        style="font-weight: 600;">Active</span>
                                                @else
                                                    <span class="badge badge-pill badge-light-danger p-2 px-3"
                                                        style="font-weight: 600;">{{ ucfirst($vendor->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <span
                                                    class="{{ $vendor->subscription_ends_at && $vendor->subscription_ends_at->isPast() ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ $vendor->subscription_ends_at ? $vendor->subscription_ends_at->format('Y-m-d') : 'No Limit' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="{{ route('super-admin.vendors.toggleStatus', $vendor) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $vendor->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                            style="border-radius: 8px;">
                                                            <i
                                                                class="la {{ $vendor->status === 'active' ? 'la-pause' : 'la-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('super-admin.vendors.show', $vendor) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        style="margin-right: 5px; border-radius: 8px;">
                                                        <i class="la la-edit"></i>
                                                        </button>
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
        .badge-light-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-light-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@endsection