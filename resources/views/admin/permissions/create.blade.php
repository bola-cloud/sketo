@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إنشاء صلاحية جديدة</h1>

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">اسم الصلاحية</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <button type="submit" class="btn btn-success">إنشاء الصلاحية</button>
    </form>
</div>
@endsection
