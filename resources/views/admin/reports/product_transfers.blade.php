@extends('layouts.admin')

@section('content')
<div class="container-fluid card p-5">
    <h1 class="text-center mb-4">تقرير نقل المنتجات</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>رقم الفاتورة القديمة</th>
                    <th>رقم الفاتورة الجديدة</th>
                    <th>المنتج المنقول</th>
                    <th>الكمية المنقولة</th>
                    <th>الكمية المباعة من القديمة</th>
                    <th>سعر الشراء قبل النقل</th>
                    <th>سعر البيع قبل النقل</th>
                    <th>سعر الشراء بعد النقل</th>
                    <th>سعر البيع بعد النقل</th>
                    <th>تاريخ النقل</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->old_invoice_number }}</td>
                        <td>{{ $transfer->new_invoice_number }}</td>
                        <td>{{ $transfer->old_product_name }}</td>
                        <td>{{ $transfer->transferred_quantity }}</td>
                        <td>{{ $transfer->sold_quantity_old_purchase }}</td>
                        <td>{{ $transfer->old_cost_price }}</td>
                        <td>{{ $transfer->old_selling_price }}</td>
                        <td>{{ $transfer->new_cost_price }}</td>
                        <td>{{ $transfer->new_selling_price }}</td>
                        <td>{{ $transfer->formatted_created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Add pagination links -->
    <div class="d-flex justify-content-center">
        {{ $transfers->links() }}
    </div>
</div>
@endsection
