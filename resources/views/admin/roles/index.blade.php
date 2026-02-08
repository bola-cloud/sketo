@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.roles.management') }}</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">{{ __('app.roles.create_new') }}</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('app.common.name') }}</th>
                    <th>{{ __('app.roles.permissions') }}</th>
                    <th>{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->display_name }}</td>
                        <td>
                            @foreach($role->permissions as $permission)
                                <span class="badge badge-info">{{ $permission->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}"
                                class="btn btn-warning btn-sm">{{ __('app.common.edit') }}</a>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
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