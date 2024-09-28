@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow p-4 mb-4">
        <h1 class="text-center mb-4">إجمالي المشتريات اليوم</h1>

        <div class="row">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="card text-white bg-success mb-4">
                    <div class="card-body text-center">
                        <h2 class="card-title">إجمالي المشتريات اليوم</h2>
                        <p class="display-4">{{ $totalPurchases ?? 0 }} ج . م</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-center mb-4">قائمة المشتريات اليوم</h2>

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>النوع</th>
                        <th>المبلغ الإجمالي</th>
                        <th>تاريخ الشراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->invoice_number }}</td>
                            <td>{{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}</td>
                            <td>{{ $purchase->total_amount }} ج . م</td>
                            <td>{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">لا توجد مشتريات لهذا اليوم.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
