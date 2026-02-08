@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.permissions.management') }}</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-3">{{ __('app.permissions.create_new') }}</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('app.common.name') }}</th>
                    <th>{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>
                            <a href="{{ route('permissions.edit', $permission->id) }}"
                                class="btn btn-warning btn-sm">{{ __('app.common.edit') }}</a>
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('app.common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection