@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>قائمة الماركات</h1>
    @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
        <a href="{{ route('brands.create') }}" class="btn btn-primary mb-3">إضافة ماركة جديدة</a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الرقم</th>
                <th>اسم الماركة</th>
                <th>الوصف</th>
                <th>عدد المنتجات</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brands as $key=>$brand)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->description ?: 'لا يوجد وصف' }}</td>
                    <td>{{ $brand->products_count }} منتج</td>
                    <td>
                        @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
                            <a href="{{ route('brands.show', $brand->id) }}" class="btn btn-info btn-sm">عرض</a>
                            <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $brands->links() }}
    </div>
</div>
@endsection
