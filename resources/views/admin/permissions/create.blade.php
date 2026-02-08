@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.permissions.create_new') }}</h1>

        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('app.permissions.name') }}</label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>

            <button type="submit" class="btn btn-success">{{ __('app.permissions.create_btn') }}</button>
        </form>
    </div>
@endsection