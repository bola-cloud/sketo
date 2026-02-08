@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.shifts.open_new_shift') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('shifts.index') }}">{{ __('app.shifts.all_shifts') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.shifts.open_shift') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('shifts.store') }}" method="POST">
                                @csrf
                                <div class="text-center mb-3">
                                    <div class="avatar avatar-xl bg-light-primary mb-1">
                                        <i class="la la-unlock text-primary font-large-2"></i>
                                    </div>
                                    <h4 class="card-title">{{ __('app.shifts.start_work_shift') }}</h4>
                                    <p class="text-muted">{{ __('app.shifts.enter_cash_drawer') }}</p>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="starting_cash"
                                        class="text-bold-600">{{ __('app.shifts.starting_cash_input') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="number" step="0.01" name="starting_cash" id="starting_cash"
                                            class="form-control round border-primary" placeholder="0.00" required autofocus>
                                        <div class="form-control-position">
                                            <i class="la la-money"></i>
                                        </div>
                                    </div>
                                    @error('starting_cash')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-actions text-center mt-3">
                                    <button type="submit" class="btn btn-primary round px-4 shadow-lg btn-block">
                                        <i class="la la-check"></i> {{ __('app.shifts.open_shift_now') }}
                                    </button>
                                    <a href="{{ route('shifts.index') }}"
                                        class="btn btn-light round mt-1 btn-block">{{ __('app.shifts.cancel') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection