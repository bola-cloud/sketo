@extends('layouts.admin')

@section('content')
<div class="container-fluid card p-5">
    <h1 class="text-center mb-4">تفاصيل الفاتورة</h1>

    <h3>رقم الفاتورة: {{ $purchase->invoice_number }}</h3>
    <h4>النوع: {{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}</h4>
    <h4>الإجمالي: {{ $purchase->total_amount }} ج.م</h4>
    <h4>المبلغ المدفوع حتي الآن: {{ $purchase->total_paid }} ج.م</h4>
    <h4>الباقي: {{ $purchase->change }} ج.م</h4>

    @if($purchase->type == 'product')
        <!-- Product Table (same as before) -->
    @endif

    <!-- Installments Table -->
    <h4>الدفعات (الأقساط)</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>المبلغ المدفوع</th>
                    <th>تاريخ الدفع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->installments as $installment)
                    <tr>
                        <td>{{ $installment->amount_paid }} ج.م</td>
                        <td>{{ $installment->date_paid }}</td>
                        <td>
                            <!-- Option to delete or update installment if needed -->
                            <form action="{{ route('purchases.installments.destroy', $installment->id) }}" method="POST">
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

    <div class="row mt-5">
        <div class="col-md-8">
            <!-- Add Installment Form -->
            <h4>إضافة دفعة جديدة</h4>
            <form action="{{ route('purchases.installments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                <div class="form-group">
                    <label for="amount_paid">المبلغ المدفوع</label>
                    <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="date_paid">تاريخ الدفع</label>
                    <input type="date" name="date_paid" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">إضافة الدفعة</button>
            </form>
        </div>
    </div>

</div>
@endsection
