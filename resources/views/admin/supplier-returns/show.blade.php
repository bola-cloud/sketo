@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>تفاصيل مرتجع المورد #{{ $supplierReturn->id }}</h1>
            <div>
                @if($supplierReturn->status !== 'completed')
                    <a href="{{ route('supplier-returns.edit', $supplierReturn) }}" class="btn btn-warning">تعديل</a>
                @endif
                <a href="{{ route('supplier-returns.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>معلومات المنتج</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>اسم المنتج:</th>
                                <td>{{ $supplierReturn->product->name }}</td>
                            </tr>
                            <tr>
                                <th>الباركود:</th>
                                <td>{{ $supplierReturn->product->barcode ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>الفئة:</th>
                                <td>{{ $supplierReturn->product->category->name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>الماركة:</th>
                                <td>{{ $supplierReturn->product->brand->name ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <th>الكمية الحالية:</th>
                                <td>{{ $supplierReturn->product->quantity }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>معلومات المورد</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>اسم المورد:</th>
                                <td>{{ $supplierReturn->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>رقم الهاتف:</th>
                                <td>{{ $supplierReturn->supplier->phone ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>تفاصيل المرتجع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>الكمية المرتجعة:</th>
                                        <td>{{ $supplierReturn->quantity_returned }}</td>
                                    </tr>
                                    <tr>
                                        <th>سعر التكلفة:</th>
                                        <td>{{ number_format($supplierReturn->cost_price, 2) }} ج.م</td>
                                    </tr>
                                    <tr>
                                        <th>القيمة الإجمالية:</th>
                                        <td><strong>{{ number_format($supplierReturn->total_value, 2) }} ج.م</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>الحالة:</th>
                                        <td>
                                            @switch($supplierReturn->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">في الانتظار</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-success">مكتمل</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">ملغي</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الإنشاء:</th>
                                        <td>{{ $supplierReturn->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الإرجاع:</th>
                                        <td>{{ $supplierReturn->returned_at ? $supplierReturn->returned_at->format('Y-m-d H:i:s') : 'غير محدد' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>السبب:</th>
                                        <td>{{ $supplierReturn->reason ?? 'غير محدد' }}</td>
                                    </tr>
                                    @if($supplierReturn->purchase)
                                        <tr>
                                            <th>فاتورة الشراء:</th>
                                            <td>
                                                <a href="{{ route('purchases.show', $supplierReturn->purchase) }}" class="text-primary">
                                                    {{ $supplierReturn->purchase->invoice_number }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($supplierReturn->notes)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h6>ملاحظات:</h6>
                                    <p class="bg-light p-3 rounded">{{ $supplierReturn->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($supplierReturn->status !== 'completed')
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>ملاحظة:</strong> هذا المرتجع لم يتم تنفيذه بعد. يمكنك تعديله أو حذفه.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
