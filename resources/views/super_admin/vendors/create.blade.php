@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="premium-card shadow-lg border-0 animate-fade-in-up">
                    <div class="p-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 font-weight-bold text-gradient-premium"><i class="la la-store mr-2"></i>
                                    {{ __('app.platform.add_vendor') }}</h4>
                                <p class="mb-0 opacity-75 small text-muted">{{ __('app.platform.manage_stores') }}</p>
                            </div>
                            <a href="{{ route('super-admin.vendors.index') }}" class="btn btn-white btn-sm shadow-sm">
                                <i class="la la-arrow-left"></i> {{ __('app.platform.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ route('super-admin.vendors.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-4 text-center">
                                    <div class="bg-glass p-3 rounded-circle d-inline-block mb-3 shadow-glow"
                                        style="width: 80px; height: 80px; line-height: 50px;">
                                        <i class="la la-store-alt text-gradient-premium font-large-2"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-white">{{ __('app.platform.store_name') }}</h5>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.store_name') }}</label>
                                        <input type="text" name="business_name"
                                            class="form-control @error('business_name') is-invalid @enderror"
                                            placeholder="{{ __('app.platform.store_name') }}" required
                                            value="{{ old('business_name') }}">
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small mb-1">{{ __('app.platform.currency') ?? 'العملة الأساسية' }}</label>
                                        <select name="currency" class="form-control" required>
                                            <option value="{{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}">الجنيه المصري ({{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }})</option>
                                            <option value="ريال">الريال السعودي (ريال)</option>
                                            <option value="د.ك">الدينار الكويتي (د.ك)</option>
                                            <option value="د.إ">الدرهم الإماراتي (د.إ)</option>
                                            <option value="ر.ع.">الريال العماني (ر.ع.)</option>
                                            <option value="د.ب.">الدينار البحريني (د.ب.)</option>
                                            <option value="ر.ق.">الريال القطري (ر.ق.)</option>
                                            <option value="$">الدولار الأمريكي ($)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3 mt-2">
                                    <hr class="opacity-25" style="border-color: rgba(255,255,255,0.1);">
                                    <h5 class="font-weight-bold text-gradient-premium mb-4 mt-2"><i class="la la-user-tie mr-2"></i>
                                        {{ __('app.platform.owner_details') }}</h5>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label
                                            class="font-weight-bold text-muted small mb-1">{{ __('app.platform.owner_name') }}</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="{{ __('app.platform.owner_name') }}" required
                                            value="{{ old('name') }}">
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
                                            value="{{ old('email') }}">
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
                                            placeholder="••••••••" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                                <button type="submit" class="btn btn-primary btn-block btn-lg shadow-glow">
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
        .font-large-2 {
            font-size: 2.5rem !important;
        }
        .premium-card .form-group label {
            color: #94a3b8 !important;
        }
        body.light-mode .premium-card .form-group label {
            color: #475569 !important;
        }
        body.light-mode .text-white {
            color: #0f172a !important;
        }
    </style>
@endsection