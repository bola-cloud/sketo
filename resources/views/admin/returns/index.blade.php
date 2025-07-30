@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">مرتجعات العملاء</h4>
                <div class="heading-elements">
                    <a href="{{ route('customer-returns.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> إضافة مرتجع جديد
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="POST" action="{{ route('customer-returns.search') }}" class="row mb-3">
                        @csrf
                        <div class="col-md-2">
                            <input type="text" name="invoice_code" class="form-control" placeholder="رقم الفاتورة" value="{{ request('invoice_code') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="product_name" class="form-control" placeholder="اسم المنتج" value="{{ request('product_name') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info">بحث</button>
                            <a href="{{ route('customer-returns.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم المرتجع</th>
                                    <th>رقم الفاتورة</th>
                                    <th>اسم العميل</th>
                                    <th>المنتج</th>
                                    <th>الكمية المرتجعة</th>
                                    <th>مبلغ الإرجاع</th>
                                    <th>السبب</th>
                                    <th>الحالة</th>
                                    <th>المستخدم</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                    <tr>
                                        <td>{{ $return->id }}</td>
                                        <td>
                                            <a href="{{ route('invoices.show', $return->invoice_id) }}">
                                                {{ $return->invoice->invoice_code }}
                                            </a>
                                        </td>
                                        <td>{{ $return->invoice->buyer_name }}</td>
                                        <td>{{ $return->product->name }}</td>
                                        <td>{{ $return->quantity_returned }}</td>
                                        <td>{{ number_format($return->return_amount, 2) }} ج.م</td>
                                        <td>{{ $return->reason }}</td>
                                        <td>
                                            @if($return->status == 'pending')
                                                <span class="badge badge-warning">في الانتظار</span>
                                            @elseif($return->status == 'completed')
                                                <span class="badge badge-success">مكتملة</span>
                                            @else
                                                <span class="badge badge-danger">ملغية</span>
                                            @endif
                                        </td>
                                        <td>{{ $return->user->name }}</td>
                                        <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('customer-returns.show', $return) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customer-returns.edit', $return) }}" class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">لا توجد مرتجعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
