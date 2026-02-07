@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">تعديل فئة منتج</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">الفئات</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row justify-content-center">
            <div class="col-md-6 col-12">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-2">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name" class="text-bold-600">اسم الفئة <span class="danger">*</span></label>
                                    <input type="text" class="form-control round border-primary" id="name" name="name"
                                        value="{{ old('name', $category->name) }}" required>
                                </div>

                                <div class="form-actions text-center mt-4">
                                    <button type="submit" class="btn btn-warning round px-4 shadow text-white">
                                        <i class="la la-save"></i> تحديث الفئة
                                    </button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-light round px-4 ml-1">
                                        إلغاء
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection