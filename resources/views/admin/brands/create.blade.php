@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.brands.add_new') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('brands.index') }}">{{ __('app.brands.all_brands') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.common.add') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row justify-content-center">
            <div class="col-md-6 col-12">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-2">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('brands.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <label for="name" class="text-bold-600">{{ __('app.brands.name') }} <span
                                            class="danger">*</span></label>
                                    <input type="text" class="form-control round border-primary" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="{{ __('app.brands.enter_name') }}" required>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="description" class="text-bold-600">{{ __('app.brands.description') }}
                                        ({{ __('app.common.optional') }})</label>
                                    <textarea class="form-control round border-primary" id="description" name="description"
                                        rows="3"
                                        placeholder="{{ __('app.brands.enter_description') }}">{{ old('description') }}</textarea>
                                </div>

                                <div class="form-actions text-center mt-4">
                                    <button type="submit" class="btn btn-primary round px-4 shadow">
                                        <i class="la la-check"></i> {{ __('app.brands.save') }}
                                    </button>
                                    <a href="{{ route('brands.index') }}" class="btn btn-light round px-4 ml-1">
                                        {{ __('app.brands.cancel') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection