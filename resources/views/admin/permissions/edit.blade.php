@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.permissions.edit_title') }}</h1>

        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">{{ __('app.permissions.name') }}</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $permission->name }}" required>
            </div>

            <button type="submit" class="btn btn-success">{{ __('app.permissions.update_btn') }}</button>
        </form>
    </div>
@endsection