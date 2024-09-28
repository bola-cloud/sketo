@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إنشاء دور جديد</h1>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">اسم الدور</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="form-group">
            <label for="permissions">تعيين الصلاحيات</label>
            <select name="permissions[]" id="permissions" class="form-control select2" multiple>
                @foreach($permissions as $permission)
                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">إنشاء الدور</button>
    </form>
</div>
@endsection

@push('scripts')
    <!-- Include Select2 CSS and JS -->
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('js/select2.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 on the select element
            $('.select2').select2({
                placeholder: 'اختر الصلاحيات',
                allowClear: true
            });
        });
    </script>
@endpush
