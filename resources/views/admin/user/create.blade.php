@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.users.add_new') }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('role_user.index') }}">{{ __('app.sidebar.manage_users') ?? 'Users' }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.add') }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 d-flex justify-content-end align-items-center">
        <a href="{{ route('role_user.index') }}" class="btn btn-secondary round px-3 shadow-sm">
            <i class="la la-arrow-left"></i> {{ __('app.common.back') ?? 'Back to List' }}
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-12">
            <div class="card premium-card border-0 shadow-lg mb-4 animate-fade-in-up" style="border-radius: 28px;">
                <div class="card-content">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 form-group mb-3">
                                    <label for="name" class="text-bold-600">{{ __('app.users.name') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" name="name" id="name" class="form-control round border-primary" required>
                                        <div class="form-control-position"><i class="la la-user primary"></i></div>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-3">
                                    <label for="email" class="text-bold-600">{{ __('app.users.email') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="email" name="email" id="email" class="form-control round border-primary" required>
                                        <div class="form-control-position"><i class="la la-envelope primary"></i></div>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-3">
                                    <label for="password" class="text-bold-600">{{ __('app.users.password') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="password" name="password" id="password" class="form-control round border-primary" required>
                                        <div class="form-control-position"><i class="la la-lock primary"></i></div>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-3">
                                    <label for="password_confirmation" class="text-bold-600">{{ __('app.users.password_confirmation') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control round border-primary" required>
                                        <div class="form-control-position"><i class="la la-key primary"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions text-center mt-3">
                                <button type="submit" class="btn btn-primary round btn-lg shadow-sm w-100" style="background: linear-gradient(135deg, var(--p-indigo), var(--p-purple)); border: none;">
                                    <i class="la la-check-square-o"></i> {{ __('app.users.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection