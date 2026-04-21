@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="premium-card shadow-sm border-0 mb-4 animate-fade-in-up">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 font-weight-bold text-gradient-premium">{{ $vendor->business_name }}</h2>
                            <p class="text-muted mb-0 small opacity-75">{{ __('app.platform.store_mgmt') }}</p>
                        </div>
                        <a href="{{ route('super-admin.vendors.index') }}" class="btn btn-white btn-sm">
                            <i class="la la-arrow-left"></i> {{ __('app.platform.back') }}
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold mb-3 text-white">{{ __('app.platform.owner_details') }}</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2 text-muted"><strong class="text-white">{{ __('app.platform.owner') }}:</strong>
                                        {{ $vendor->owner->name }}</li>
                                    <li class="mb-2 text-muted"><strong class="text-white">{{ __('app.login.email') }}:</strong>
                                        {{ $vendor->owner->email }}</li>
                                    <li class="mb-2 text-muted"><strong class="text-white">{{ __('app.platform.status') }}:</strong>
                                        <span
                                            class="badge {{ $vendor->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ __('app.platform.' . $vendor->status) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 border-left" style="border-color: rgba(255,255,255,0.1) !important;">
                                <h5 class="font-weight-bold mb-3 text-white">{{ __('app.platform.sub_status') }}</h5>
                                <form action="{{ route('super-admin.vendors.updateSubscription', $vendor) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="text-muted small font-weight-bold">{{ __('app.platform.ends_at') }}:</label>
                                        <input type="date" name="subscription_ends_at" id="subscription_ends_at"
                                            class="form-control"
                                            value="{{ $vendor->subscription_ends_at ? $vendor->subscription_ends_at->format('Y-m-d') : '' }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block shadow-glow">
                                        <i class="la la-save"></i> {{ __('app.platform.update_sub') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.1);">

                        <div class="mt-4">
                            <h5 class="font-weight-bold mb-3 text-white">{{ __('app.platform.quick_actions') }}</h5>
                            <form action="{{ route('super-admin.vendors.toggleStatus', $vendor) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn {{ $vendor->status === 'active' ? 'btn-danger' : 'btn-success' }} btn-sm shadow-sm"
                                    style="border-radius: 8px;">
                                    {{ $vendor->status === 'active' ? __('app.platform.suspend_store') : __('app.platform.activate_store') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        body.light-mode .text-white { color: #0f172a !important; }
        body.light-mode .text-muted { color: #475569 !important; }
    </style>
@endsection