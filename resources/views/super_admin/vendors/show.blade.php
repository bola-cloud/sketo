@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm border-0 mb-4"
                    style="border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h2 class="mb-0 font-weight-bold" style="color: #1e293b;">{{ $vendor->business_name }}</h2>
                            <p class="text-muted mb-0">{{ __('app.platform.store_mgmt') }}</p>
                        </div>
                        <a href="{{ route('super-admin.vendors.index') }}" class="btn btn-light"
                            style="border-radius: 10px;">
                            <i class="la la-arrow-left"></i> {{ __('app.platform.back') }}
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold mb-3">{{ __('app.platform.owner_details') }}</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong>{{ __('app.platform.owner') }}:</strong>
                                        {{ $vendor->owner->name }}</li>
                                    <li class="mb-2"><strong>{{ __('app.login.email') }}:</strong>
                                        {{ $vendor->owner->email }}</li>
                                    <li class="mb-2"><strong>{{ __('app.platform.status') }}:</strong>
                                        <span
                                            class="badge {{ $vendor->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ __('app.platform.' . $vendor->status) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 border-left">
                                <h5 class="font-weight-bold mb-3">{{ __('app.platform.sub_status') }}</h5>
                                <form action="{{ route('super-admin.vendors.updateSubscription', $vendor) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="subscription_ends_at">{{ __('app.platform.ends_at') }}:</label>
                                        <input type="date" name="subscription_ends_at" id="subscription_ends_at"
                                            class="form-control"
                                            value="{{ $vendor->subscription_ends_at ? $vendor->subscription_ends_at->format('Y-m-d') : '' }}"
                                            style="border-radius: 10px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" style="border-radius: 10px;">
                                        <i class="la la-save"></i> {{ __('app.platform.update_sub') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <div class="mt-4">
                            <h5 class="font-weight-bold mb-3">{{ __('app.platform.quick_actions') }}</h5>
                            <form action="{{ route('super-admin.vendors.toggleStatus', $vendor) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn {{ $vendor->status === 'active' ? 'btn-danger' : 'btn-success' }} btn-sm"
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
@endsection