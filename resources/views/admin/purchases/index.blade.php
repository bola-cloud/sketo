@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">كل الفواتير</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label>بحث (رقم الفاتورة أو الوصف):</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="بحث...">
            </div>
            <div class="col-md-3">
                <label>المورد:</label>
                <select name="supplier_id" class="form-control">
                    <option value="">كل الموردين</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>نوع الفاتورة:</label>
                <select name="type" class="form-control">
                    <option value="">الكل</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>شراء منتجات</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>نفقات</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">بحث</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center bg-white">
        <thead class="table-light">
            <tr>
                <th>رقم الفاتورة</th>
                <th>اسم المورد</th>
                <th>نوع الفاتورة</th>
                <th>المدفوع</th>
                <th>الباقي</th>
                <th>الإجمالي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
                <tr>
                    <td><span class="badge bg-info">{{ $purchase->invoice_number }}</span></td>
                    <td>{{ $purchase->supplier ? $purchase->supplier->name : 'لا يوجد مورد' }}</td>
                    <td>
                        <span class="badge {{ $purchase->type == 'product' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}
                        </span>
                    </td>
                    <td class="text-success fw-bold">{{ number_format($purchase->paid_amount, 2) }}</td>
                    <td class="text-danger fw-bold">{{ number_format($purchase->change, 2) }}</td>
                    <td class="fw-bold">{{ number_format($purchase->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary btn-sm">عرض التفاصيل</a>
                        @if ($purchase->total_amount != $purchase->paid_amount)
                            <a href="{{ route('purchases.installments.create', ['purchase' => $purchase->id] ) }}" class="btn btn-info btn-sm"> اضافة قسط </a>
                        @endif
                        @if($purchase->type == 'expense')
                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه الفاتورة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">لا توجد نتائج مطابقة.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            {{ $purchases->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
