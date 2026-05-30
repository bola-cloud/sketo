@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.roles.create_new') }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{ __('app.roles.name') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.add') }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 d-flex justify-content-end align-items-center">
        <a href="{{ route('roles.index') }}" class="btn btn-secondary round px-3 shadow-sm">
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
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 form-group mb-3">
                                    <label for="name" class="text-bold-600">{{ __('app.roles.name') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" name="name" class="form-control round border-primary" id="name" required>
                                        <div class="form-control-position"><i class="la la-tag primary"></i></div>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group mb-3">
                                    <label for="permissions" class="text-bold-600">{{ __('app.roles.assign_permissions') }}</label>
                                    <select name="permissions[]" id="permissions" class="form-control select2 w-100" multiple>
                                        @foreach($permissions as $permission)
                                            <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions text-center mt-3">
                                <button type="submit" class="btn btn-primary round btn-lg shadow-sm w-100" style="background: linear-gradient(135deg, var(--p-indigo), var(--p-purple)); border: none;">
                                    <i class="la la-check-square-o"></i> {{ __('app.roles.create_btn') }}
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

@push('scripts')
    <!-- Include Select2 CSS and JS -->
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('js/select2.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 on the select element
            $('.select2').select2({
                placeholder: '{{ __('app.roles.select_permissions') }}',
                allowClear: true
            });
        });
    </script>
@endpush