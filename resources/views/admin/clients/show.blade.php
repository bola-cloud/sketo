@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>عرض تفاصيل العميل</h1>

    <p><strong>الاسم:</strong> {{ $client->name }}</p>
    <p><strong>الهاتف:</strong> {{ $client->phone }}</p>

    <h3>فواتير العميل</h3>

    <p><strong>إجمالي الفواتير:</strong> {{ $totalInvoices }} جنيه</p>
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
            @foreach($client->invoices as $invoice)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $invoice->invoice_code }}</td>
                    <td>{{ $invoice->total_amount }} جنيه</td>
                    <td>{{ $invoice->paid_amount }} جنيه</td>
                    <td>{{ $invoice->change }} جنيه</td>
                    <td>
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info">عرض التفاصيل</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('clients.index') }}" class="btn btn-secondary">عودة إلى قائمة العملاء</a>
</div>
@endsection
