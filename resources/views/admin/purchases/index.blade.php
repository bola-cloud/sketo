@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">كل الفواتير</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>نوع الفاتورة</th>
                <th>المدفوع </th>
                <th>الباقي</th>
                <th>الإجمالي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}</td>
                    <td>{{ $purchase->paid_amount }}</td>
                    <td>{{ $purchase->change }}</td>
                    <td>{{ $purchase->total_amount }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">عرض التفاصيل</a>
                        <a href="{{ route('purchases.installments.create', ['purchase' => $purchase->id] ) }}" class="btn btn-info"> اضافة قسط </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
