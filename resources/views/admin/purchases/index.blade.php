@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="content-header row align-items-center mb-4">
            <div class="col-12 text-center">
                <h1 class="text-bold-700">كل الفواتير</h1>
                <p class="text-muted">إدارة فواتير المشتريات والنفقات</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="table-premium-wrapper mb-4">
            <h5 class="mb-4 text-bold-600"><i class="ft-filter primary mr-1"></i> تصفية الفواتير</h5>
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small mb-1">بحث (رقم الفاتورة أو الوصف):</label>
                        <input type="text" name="search" class="form-control border-0 bg-light"
                            value="{{ request('search') }}" placeholder="رقم الفاتورة, مورد, وصف...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small mb-1">المورد:</label>
                        <select name="supplier_id" class="form-control border-0 bg-light">
                            <option value="">كل الموردين</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small mb-1">نوع الفاتورة:</label>
                        <select name="type" class="form-control border-0 bg-light">
                            <option value="">الكل</option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>شراء منتجات</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>نفقات</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm py-2"><i class="ft-search mr-1"></i> بحث</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="table-premium-wrapper">
            <div class="table-responsive">
                <table class="table table-premium text-center">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>اسم المورد</th>
                            <th>نوع الفاتورة</th>
                            <th>المدفوع</th>
                            <th>الباقي</th>
                            <th>الإجمالي</th>
                            <th style="width: 200px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>
                                    <span class="badge badge-soft-primary">
                                        <i class="ft-hash mr-1"></i>{{ $purchase->invoice_number }}
                                    </span>
                                </td>
                                <td class="text-bold-600">
                                    {{ $purchase->supplier ? $purchase->supplier->name : 'لا يوجد مورد' }}
                                </td>
                                <td>
                                    <span class="badge {{ $purchase->type == 'product' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                                        {{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}
                                    </span>
                                </td>
                                <td class="text-success text-bold-700">
                                    {{ number_format($purchase->paid_amount, 2) }} ج.م
                                </td>
                                <td class="text-danger text-bold-700">
                                    {{ number_format($purchase->change, 2) }} ج.م
                                </td>
                                <td class="text-bold-700">
                                    {{ number_format($purchase->total_amount, 2) }} ج.م
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('purchases.show', $purchase->id) }}"
                                            class="btn btn-sm btn-outline-secondary mr-2" title="عرض التفاصيل">
                                            <i class="ft-eye"></i>
                                        </a>
                                        @if ($purchase->total_amount != $purchase->paid_amount)
                                            <a href="{{ route('purchases.installments.create', ['purchase' => $purchase->id]) }}"
                                                class="btn btn-sm btn-outline-info mr-2" title="اضافة قسط">
                                                <i class="ft-plus"></i> قسط
                                            </a>
                                        @endif
                                        @if($purchase->type == 'expense')
                                            <form action="{{ route('purchases.destroy', $purchase->id) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه الفاتورة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="حذف">
                                                    <i class="ft-trash-2"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-5">
                                    <i class="ft-info font-large-2 d-block mb-3"></i>
                                    لا توجد نتائج مطابقة لتفضيلات البحث الخاصة بك.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4 mb-5">
            <div class="col-md-12 d-flex justify-content-center">
                {{ $purchases->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection