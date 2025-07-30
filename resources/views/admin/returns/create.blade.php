@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">إضافة مرتجع جديد</h4>
                <div class="heading-elements">
                    <a href="{{ route('customer-returns.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>كيفية إضافة مرتجع</h4>
                        <p>لإضافة مرتجع جديد، يمكنك:</p>
                        <ul>
                            <li>الذهاب إلى قائمة الفواتير واختيار الفاتورة المراد إرجاع منتجات منها</li>
                            <li>الضغط على زر "إرجاع منتجات" من تفاصيل الفاتورة</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_search">البحث عن فاتورة</label>
                                <input type="text" id="invoice_search" class="form-control" placeholder="أدخل رقم الفاتورة أو اسم العميل">
                            </div>
                            <div id="search_results"></div>
                        </div>
                    </div>

                    <hr>

                    <h5>الفواتير الحديثة</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>اسم العميل</th>
                                    <th>التاريخ</th>
                                    <th>الإجمالي</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentInvoices = \App\Models\Invoice::with('client')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                @foreach($recentInvoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_code }}</td>
                                        <td>{{ $invoice->buyer_name }}</td>
                                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }} ج.م</td>
                                        <td>
                                            <a href="{{ route('customer-returns.createForInvoice', $invoice) }}" class="btn btn-sm btn-primary">
                                                إرجاع منتجات
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('invoice_search').addEventListener('input', function() {
    const query = this.value;
    if (query.length > 2) {
        // Here you would typically make an AJAX call to search for invoices
        // For now, we'll just show a message
        document.getElementById('search_results').innerHTML = '<p class="text-muted">ابحث عن الفاتورة في الجدول أدناه أو استخدم قائمة الفواتير</p>';
    } else {
        document.getElementById('search_results').innerHTML = '';
    }
});
</script>
@endsection
