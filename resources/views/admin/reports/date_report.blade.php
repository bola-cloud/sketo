@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow p-4">
        <h1 class="text-center mb-4">تقارير المبيعات حسب الفترة الزمنية</h1>

        <!-- Date Range Filter -->
        <form action="{{ route('reports.dateRange') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                </div>
                <div class="col-md-5">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">بحث</button>
                </div>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">إجمالي الكمية المباعة</h5>
                        <p class="card-text">{{ $totalQuantity }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">إجمالي الإيرادات</h5>
                        <p class="card-text">{{ number_format($totalRevenue, 2) }} ج . م</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">إجمالي الربح</h5>
                        <p class="card-text">{{ number_format($totalProfit, 2) }} ج . م</p>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>كود الفاتورة</th>
                    <th>المبلغ المدفوع</th>
                    <th>الإجمالي بعد الخصم</th>
                    <th>التغيير (المبلغ المتبقي/الزائد)</th>
                    <th>تفاصيل المبيعات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_code }}</td>
                        <td>{{ number_format($invoice->paid_amount, 2) }} ج . م</td>
                        <td>{{ number_format($invoice->total_amount, 2) }} ج . م</td>
                        <td>{{ number_format($invoice->change, 2) }} ج . م</td>
                        <td>
                            <ul>
                                @foreach($invoice->sales as $sale)
                                    <li>{{ $sale->product->name }} - الكمية: {{ $sale->quantity }} - سعر البيع: {{ number_format($sale->product->selling_price, 2) }} ج.م</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
