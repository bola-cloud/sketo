@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">إضافة فاتورة شراء</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="invoice_number">رقم الفاتورة</label>
            <input type="text" name="invoice_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="type">نوع الفاتورة</label>
            <select name="type" id="type" class="form-control" required>
                <option selected>اختر نوع الفاتورة</option>
                <option value="product">شراء منتجات</option>
                <option value="expense">نفقات</option>
            </select>
        </div>

        <div class="form-group" id="description-section">
            <label for="description">الوصف</label>
            <input type="text" name="description" class="form-control">
        </div>

        <div class="form-group">
            <label for="paid_amount">المبلغ المدفوع</label>
            <input type="number" name="paid_amount" class="form-control" required>
        </div>

        <!-- Only visible for expense type -->
        <div class="form-group" id="total-amount-section" style="display:none;">
            <label for="total_amount">الإجمالي</label>
            <input type="number" name="total_amount" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">حفظ الفاتورة</button>
    </form>
</div>

<script src="{{asset('assets/js/jquery.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#type').on('change', function() {
            var type = $(this).val();
            if (type === 'expense') {
                $('#total-amount-section').show();
            } else {
                $('#total-amount-section').hide();
            }
        });
    });
</script>
@endsection
