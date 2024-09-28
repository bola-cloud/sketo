@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>تعديل الصلاحية</h1>

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">اسم الصلاحية</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $permission->name }}" required>
        </div>

        <button type="submit" class="btn btn-success">تحديث الصلاحية</button>
    </form>
</div>
@endsection
