@extends('layouts.admin')

@section('content')
<div class="container-fluid card p-5">
    <h1 class="text-center mb-4">تفاصيل الفاتورة</h1>

    <h3>رقم الفاتورة: {{ $purchase->invoice_number }}</h3>
    <h4>النوع: {{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}</h4>
    <h4>المبلغ المدفوع: {{ $purchase->paid_amount }} ج.م</h4>
    <h4>التغيير (الباقي): {{ $purchase->change }} ج.م</h4>
    <h4>الإجمالي: {{ $purchase->total_amount }} ج.م</h4>

    @if($purchase->type == 'product')
        <h4>المنتجات</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية المشتراة</th>
                        <th>الكمية المتبقية</th>
                        <th>سعر الشراء</th>
                        <th>سعر البيع</th>
                        <th>إجمالي الربح من المبيعات</th>
                        <th>إجمالي المبيعات لهذا المنتج</th> <!-- Updated column header -->
                        <th>الاجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalSalesProfit = 0;
                        $totalSalesAmount = 0; // Variable to accumulate the total sales amount
                    @endphp
                    @foreach($purchase->products as $purchaseProduct)
                        @php
                            // Calculate the total sales quantity for this product
                            $totalSoldQuantity = $purchaseProduct->sales->sum('quantity');
                            
                            // Calculate the profit from sales for this product
                            $salesProfit = ($purchaseProduct->selling_price - $purchaseProduct->pivot->cost_price) * $totalSoldQuantity;
                            
                            // Calculate the total sales for this product
                            $totalSalesForProduct = $totalSoldQuantity * $purchaseProduct->selling_price;
                            
                            // Add to total profit
                            $totalSalesProfit += $salesProfit;

                            // Add to the total sales amount
                            $totalSalesAmount += $totalSalesForProduct;

                            // Calculate the remaining quantity
                            $remainingQuantity = $purchaseProduct->pivot->quantity - $totalSoldQuantity;
                        @endphp
                        <tr>
                            <td>{{ $purchaseProduct->name }}</td>
                            <td>{{ $purchaseProduct->pivot->quantity }}</td>
                            <td>{{ $remainingQuantity }}</td> <!-- Remaining quantity -->
                            <td>{{ $purchaseProduct->pivot->cost_price }}</td>
                            <td>{{ $purchaseProduct->selling_price }}</td>
                            <td>{{ $salesProfit }}</td> <!-- Profit from sales of this product -->
                            <td>{{ $totalSalesForProduct }}</td> <!-- Total sales for this product -->
                            <td>
                                <!-- Add a button to initiate the transfer -->
                                <a href="{{ route('purchases.transferProduct', ['purchase' => $purchase->id, 'product' => $purchaseProduct->id]) }}" class="btn btn-warning">نقل الكمية المتبقية</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">إجمالي الربح من مبيعات هذه الفاتورة حتي الان :</th>
                        <th>{{ $totalSalesProfit }} ج . م</th>
                    </tr>
                    <tr>
                        <th colspan="6">إجمالي المبيعات في هذه الفاتورة حتي الان :</th>
                        <th>{{ $totalSalesAmount }} ج . م</th> <!-- Display the total sales amount -->
                    </tr>
                </tfoot>
            </table>
        </div>

    @else
        <h4>الوصف: {{ $purchase->description }}</h4>
    @endif
    <a href="{{ route('purchases.index') }}" class="btn btn-primary">العودة إلى الفواتير</a>
</div>
@endsection
