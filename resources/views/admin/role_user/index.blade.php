@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-center mb-4">إدارة أدوار المستخدمين</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h2>إرفاق دور إلى مستخدم</h2>
            <form action="{{ route('role_user.attach') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="user_id">اختر المستخدم</label>
                    <select name="user_id" id="user_id" class="form-control">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="role_id">اختر الدور</label>
                    <select name="role_id" id="role_id" class="form-control">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">إرفاق الدور</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h2>أدوار المستخدمين</h2>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>المستخدم</th>
                        <th>الأدوار</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->display_name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($roles as $role)
                                    @if($user->roles->contains($role))
                                        <form action="{{ route('role_user.detach') }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm">فصل {{ $role->display_name }}</button>
                                        </form>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
