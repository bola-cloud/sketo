@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إدارة الأدوار</h1>
    <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">إنشاء دور جديد</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الصلاحيات</th>
                <th>الإجراءات</th>
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
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
