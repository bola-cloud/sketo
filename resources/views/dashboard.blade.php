@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('app.sidebar.dashboard') }}</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <p class="card-text">{{ __('app.common.welcome') }} {{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection