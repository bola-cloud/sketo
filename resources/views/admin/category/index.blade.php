@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>قائمة الفئات</h1>
    @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">إضافة فئة جديدة</a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم الفئة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $key=>$category)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        تعديل الفئات
                        @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">تعديل</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                        </form>
                        @endif

                    </td>           
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
