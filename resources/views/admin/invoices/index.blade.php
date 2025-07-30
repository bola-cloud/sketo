@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="text-center mb-3">جميع الفواتير</h1>

    <form id="search-form" class="mb-4" action="{{ route('invoices.search') }}" method="GET">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="search-query" name="query" placeholder="البحث عن طريق اسم المشتري، الهاتف أو كود الفاتورة" value="{{ request('query') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group mb-3">
                    <input type="date" class="form-control" id="date_from" name="date_from" placeholder="من تاريخ" value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group mb-3">
                    <input type="date" class="form-control" id="date_to" name="date_to" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">بحث</button>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered" id="invoice-table">
        <thead>
            <tr>
                <th>كود الفاتورة</th>
                <th>اسم المشتري</th>
                <th>اسم البائع</th>
                <th>تاريخ الإنشاء</th>
                <th>الحالة</th>
                <th>الاقساط</th>
                <th>إجراءات</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                @php
                    $returnsCount = $invoice->returns()->count();
                    $hasUnpaidAmount = $invoice->total_amount > $invoice->paid_amount;
                @endphp
                <tr>
                    <td>
                        {{ $invoice->invoice_code }}
                        @if($returnsCount > 0)
                            <span class="badge badge-warning" title="يحتوي على {{ $returnsCount }} عملية إرجاع">
                                <i class="fa fa-undo"></i> {{ $returnsCount }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $invoice->client ? $invoice->client->name : 'لا يوجد عميل' }}</td>
                    <td>{{ $invoice->user->name }}</td>
                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if($hasUnpaidAmount)
                            <span class="badge badge-danger">غير مكتمل السداد</span>
                        @else
                            <span class="badge badge-success">مدفوع</span>
                        @endif

                        @if($returnsCount > 0)
                            <br><small class="text-warning">
                                <i class="fa fa-exclamation-triangle"></i> يحتوي على مرتجعات
                            </small>
                        @endif
                    </td>
                    <td>
                        <!-- Installment button -->
                        @if($hasUnpaidAmount)
                            <a href="{{ route('sales.installments.index', $invoice->id) }}" class="btn btn-info btn-sm">عرض الأقساط</a>
                        @else
                            <span class="text-muted">مكتمل</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('invoices.show', ['invoice' => $invoice->id]) }}" class="btn btn-secondary btn-sm">عرض التفاصيل</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('cashier.printInvoice', $invoice->id) }}">طباعة الفاتورة</a>
                        @if($returnsCount > 0)
                            <br><a href="{{ route('customer-returns.index') }}?invoice_code={{ $invoice->invoice_code }}" class="btn btn-warning btn-sm mt-1">
                                <i class="fa fa-eye"></i> المرتجعات ({{ $returnsCount }})
                            </a>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف الفاتورة؟ سيتم إرجاع الكميات إلى المخزون.')">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            {{ $invoices->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery.js') }}"></script>

@endsection
