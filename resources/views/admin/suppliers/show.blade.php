@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>عرض تفاصيل المورد</h1>

    <p><strong>الاسم:</strong> {{ $supplier->name }}</p>
    <p><strong>الهاتف:</strong> {{ $supplier->phone }}</p>

    <h3>فواتير المشتريات</h3>

    <p><strong>إجمالي المشتريات:</strong> {{ $totalPurchases }} جنيه</p>
    <p><strong>إجمالي المدفوعات:</strong> {{ $totalPaidAmount }} جنيه</p>
    <p><strong>إجمالي الباقي:</strong> {{ $totalChange }} جنيه</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الفاتورة</th>
                <th>إجمالي الفاتورة</th>
                <th>المدفوع</th>
                <th>الباقي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplier->purchases as $purchase)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->total_amount }} جنيه</td>
                    <td>{{ $purchase->paid_amount }} جنيه</td>
                    <td>{{ $purchase->change }} جنيه</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-info">عرض التفاصيل</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">عودة إلى قائمة الموردين</a>
</div>
@endsection
