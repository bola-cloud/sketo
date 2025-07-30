@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إضافة ماركة جديدة</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('brands.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">اسم الماركة</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="description">الوصف (اختياري)</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">إضافة الماركة</button>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
