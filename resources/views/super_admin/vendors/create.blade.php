@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-primary text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 font-weight-bold"><i class="la la-store mr-2"></i>
                                    {{ __('app.platform.add_vendor') }}</h4>
                                <p class="mb-0 opacity-75 small text-white-50">{{ __('app.platform.manage_stores') }}</p>
                            </div>
                            <a href="{{ route('super-admin.vendors.index') }}" class="btn btn-light btn-sm shadow-sm"
                                style="border-radius: 10px;">
                                <i class="la la-arrow-left"></i> {{ __('app.platform.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-5 bg-white">
                        <form action="{{ route('super-admin.vendors.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-4 text-center">
                                    <div class="bg-light p-3 rounded-circle d-inline-block mb-3"
                                        style="width: 80px; height: 80px; line-height: 50px;">
                                        <i class="la la-store-alt text-primary font-large-2"></i>
                                    </div>
                                    <h5 class="font-weight-bold">{{ __('app.platform.store_name') }}</h5>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.store_name') }}</label>
                                        <input type="text" name="business_name"
                                            class="form-control @error('business_name') is-invalid @enderror"
                                            placeholder="{{ __('app.platform.store_name') }}" required
                                            value="{{ old('business_name') }}"
                                            style="border-radius: 12px; height: 50px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 mt-2">
                                    <hr class="opacity-25">
                                    <h5 class="font-weight-bold text-primary mb-4 mt-2"><i class="la la-user-tie mr-2"></i>
                                        {{ __('app.platform.owner_details') }}</h5>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.owner_name') }}</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="{{ __('app.platform.owner_name') }}" required
                                            value="{{ old('name') }}"
                                            style="border-radius: 12px; height: 50px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.owner_email') }}</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="{{ __('app.platform.owner_email') }}" required
                                            value="{{ old('email') }}"
                                            style="border-radius: 12px; height: 50px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.owner_password') }}</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="••••••••" required
                                            style="border-radius: 12px; height: 50px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm"
                                    style="border-radius: 15px; height: 60px; font-weight: 700;">
                                    <i class="la la-check-circle mr-2"></i> {{ __('app.platform.create_vendor') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            background: #fff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }

        .btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3) !important;
        }

        .font-large-2 {
            font-size: 2.5rem !important;
        }
    </style>
@endsection