@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إدارة الصلاحيات</h1>
    <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-3">إنشاء صلاحية جديدة</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;">
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
