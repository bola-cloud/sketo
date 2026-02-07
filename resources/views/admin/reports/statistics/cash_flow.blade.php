@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">تقرير الخزينة (التدفق النقدي)</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">التدفق النقدي</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Filter Card -->
        <div class="card pull-up border-0 shadow-sm mb-4"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-content">
                <div class="card-body">
                    <form action="{{ route('reports.statistics.cash_flow') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">تاريخ البداية</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">تاريخ النهاية</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="la la-filter"></i> تصفية
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Income Table -->
            <div class="col-md-6">
                <div class="card pull-up border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="card-title success"><i class="la la-plus-circle"></i> التحصيلات (المقبوضات)</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>الفاتورة</th>
                                            <th>المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesInstallments as $inst)
                                            <tr>
                                                <td>{{ $inst->date_paid }}</td>
                                                <td>#{{ $inst->invoice->invoice_code ?? $inst->invoice_id }}</td>
                                                <td class="success text-bold-700">+ {{ number_format($inst->amount_paid, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">لا توجد تحصيلات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outgoing Table -->
            <div class="col-md-6">
                <div class="card pull-up border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0">
                        <h4 class="card-title danger"><i class="la la-minus-circle"></i> المدفوعات (المصروفات)</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>الشراء</th>
                                            <th>المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchaseInstallments as $inst)
                                            <tr>
                                                <td>{{ $inst->date_paid }}</td>
                                                <td>#{{ $inst->purchase->purchase_code ?? $inst->purchase_id }}</td>
                                                <td class="danger text-bold-700">- {{ number_format($inst->amount_paid, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">لا توجد مدفوعات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection