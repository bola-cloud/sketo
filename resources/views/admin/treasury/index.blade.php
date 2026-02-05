@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">الخزينة ليوم واحد</h1>

    <!-- Form to Select Date -->
    <form action="{{ route('treasury') }}" method="GET" class="mb-4">
        <div class="form-group row">
            <label for="date" class="col-sm-2 col-form-label">اختر التاريخ</label>
            <div class="col-sm-4">
                <input type="date" id="date" name="date" class="form-control" value="{{ $date }}" required>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-primary">عرض الخزينة</button>
            </div>
        </div>
    </form>

    <!-- Display Treasury Summary -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">الخزينة ليوم: {{ $date }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>إجمالي الأقساط من المبيعات: {{ number_format($salesInstallments, 2) }} ج.م</h4>
                </div>
                <div class="col-md-6">
                    <h4>إجمالي الأقساط من المشتريات: {{ number_format($purchaseInstallments, 2) }} ج.م</h4>
                </div>
            </div>

            <hr>

            <h3 class="text-center">
                الفرق بين المبيعات والمشتريات:
                <strong>{{ number_format($difference, 2) }} ج.م</strong>
            </h3>

            @if ($difference > 0)
                <p class="text-center text-success">هناك فائض اليوم.</p>
            @elseif ($difference < 0)
                <p class="text-center text-danger">هناك عجز اليوم.</p>
            @else
                <p class="text-center text-warning">لا يوجد فرق بين المبيعات والمشتريات اليوم.</p>
            @endif

            <hr>

            <!-- Details of Purchases (مصروفات) -->
            <h4 class="mt-4">تفاصيل المصروفات (المشتريات):</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>الوصف</th>
                        <th>المورد</th>
                        <th>المبلغ المدفوع</th>
                        <th>إجمالي الفاتورة</th>
                        <th>تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseDetails as $installment)
                        <tr>
                            <td>{{ $installment->purchase->invoice_number ?? '-' }}</td>
                            <td>{{ $installment->purchase->description ?? '-' }}</td>
                            <td>{{ $installment->purchase->supplier->name ?? '-' }}</td>
                            <td>{{ number_format($installment->amount_paid, 2) }}</td>
                            <td>{{ number_format($installment->purchase->total_amount ?? 0, 2) }}</td>
                            <td>{{ $installment->date_paid }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Details of Sales (مصادر الدخل) -->
            <h4 class="mt-4">تفاصيل مصادر الدخل (المبيعات):</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>اسم العميل</th>
                        <th>المبلغ المدفوع</th>
                        <th>إجمالي الفاتورة</th>
                        <th>تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesDetails as $installment)
                        <tr>
                            <td>{{ $installment->invoice->invoice_code ?? '-' }}</td>
                            <td>{{ $installment->invoice->client->name ?? '-' }}</td>
                            <td>{{ number_format($installment->amount_paid, 2) }}</td>
                            <td>{{ number_format($installment->invoice->total_amount ?? 0, 2) }}</td>
                            <td>{{ $installment->date_paid }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Supplier Returns (مردودات الموردين) -->
            <h4 class="mt-4">مردودات الموردين (تقلل المصروفات):</h4>
            <h5>إجمالي مردودات الموردين: {{ number_format($supplierReturnsTotal, 2) }} ج.م</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>المورد</th>
                        <th>رقم الفاتورة</th>
                        <th>المنتج</th>
                        <th>الكمية المرتجعة</th>
                        <th>سعر الوحدة</th>
                        <th>إجمالي القيمة</th>
                        <th>تاريخ الإرجاع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supplierReturns as $return)
                        <tr>
                            <td>{{ $return->supplier->name ?? '-' }}</td>
                            <td>{{ $return->purchase->invoice_number ?? '-' }}</td>
                            <td>{{ $return->product->name ?? '-' }}</td>
                            <td>{{ $return->quantity_returned }}</td>
                            <td>{{ number_format($return->cost_price, 2) }}</td>
                            <td>{{ number_format($return->getTotalValueAttribute(), 2) }}</td>
                            <td>{{ $return->returned_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Customer Returns (مردودات العملاء) -->
            <h4 class="mt-4">مردودات العملاء (تقلل الدخل):</h4>
            <h5>إجمالي مردودات العملاء: {{ number_format($customerReturnsTotal, 2) }} ج.م</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المنتج</th>
                        <th>الكمية المرتجعة</th>
                        <th>قيمة الإرجاع</th>
                        <th>تاريخ الإرجاع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerReturns as $return)
                        <tr>
                            <td>{{ $return->invoice->invoice_code ?? '-' }}</td>
                            <td>{{ $return->product->name ?? '-' }}</td>
                            <td>{{ $return->quantity_returned }}</td>
                            <td>{{ number_format($return->return_amount, 2) }}</td>
                            <td>{{ $return->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
