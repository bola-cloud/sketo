@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل المرتجع رقم: {{ $customerReturn->id }}</h4>
                <div class="heading-elements">
                    <a href="{{ route('customer-returns.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للقائمة
                    </a>
                    <a href="{{ route('customer-returns.edit', $customerReturn) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i> تعديل
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <!-- Return Details -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5>تفاصيل المرتجع</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>رقم المرتجع:</strong></td>
                                            <td>{{ $customerReturn->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>المنتج:</strong></td>
                                            <td>{{ $customerReturn->product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الكمية المرتجعة:</strong></td>
                                            <td>{{ $customerReturn->quantity_returned }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>مبلغ الإرجاع:</strong></td>
                                            <td>{{ number_format($customerReturn->return_amount, 2) }} ج.م</td>
                                        </tr>
                                        <tr>
                                            <td><strong>سبب الإرجاع:</strong></td>
                                            <td>{{ $customerReturn->reason }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>الحالة:</strong></td>
                                            <td>
                                                @if($customerReturn->status == 'pending')
                                                    <span class="badge badge-warning">في الانتظار</span>
                                                @elseif($customerReturn->status == 'completed')
                                                    <span class="badge badge-success">مكتملة</span>
                                                @else
                                                    <span class="badge badge-danger">ملغية</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>المستخدم:</strong></td>
                                            <td>{{ $customerReturn->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>تاريخ الإرجاع:</strong></td>
                                            <td>{{ $customerReturn->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5>تفاصيل الفاتورة الأصلية</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>رقم الفاتورة:</strong></td>
                                            <td>
                                                <a href="{{ route('invoices.show', $customerReturn->invoice) }}">
                                                    {{ $customerReturn->invoice->invoice_code }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>اسم العميل:</strong></td>
                                            <td>{{ $customerReturn->invoice->buyer_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>رقم الهاتف:</strong></td>
                                            <td>{{ $customerReturn->invoice->buyer_phone ?? 'غير محدد' }}</td>
                                        </tr>
                                        @if($customerReturn->invoice->client)
                                            <tr>
                                                <td><strong>بيانات العميل:</strong></td>
                                                <td>{{ $customerReturn->invoice->client->name }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>تاريخ الفاتورة:</strong></td>
                                            <td>{{ $customerReturn->invoice->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>إجمالي الفاتورة:</strong></td>
                                            <td>{{ number_format($customerReturn->invoice->total_amount, 2) }} ج.م</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5>تفاصيل المنتج</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>اسم المنتج:</strong></td>
                                                    <td>{{ $customerReturn->product->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>الفئة:</strong></td>
                                                    <td>{{ $customerReturn->product->category->name ?? 'غير محدد' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>سعر البيع:</strong></td>
                                                    <td>{{ number_format($customerReturn->product->selling_price, 2) }} ج.م</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>الكمية الحالية في المخزن:</strong></td>
                                                    <td>{{ $customerReturn->product->quantity }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>الباركود:</strong></td>
                                                    <td>{{ $customerReturn->product->barcode ?? 'غير محدد' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
