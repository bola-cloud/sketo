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
        <div class="mb-3">
            <a href="{{ route('purchases.recalculateTotal', $purchase->id) }}" class="btn btn-info btn-sm">
                <i class="fa fa-calculator"></i> إعادة حساب الإجمالي
            </a>
        </div>
    @endif

    @if($purchase->type == 'product')
        <h4>المنتجات</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية المشتراة</th>
                        <th>الكمية المباعة</th>
                        <th>سعر الشراء</th>
                        <th>سعر البيع</th>
                        <th>إجمالي الربح من المبيعات لهذا المنتج</th>
                        <th>إجمالي المبيعات لهذا المنتج من هذه الفاتورة</th>
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
                            // Use the calculated values from the controller
                            $soldQuantityFromThisBatch = $purchaseProduct->sold_from_this_batch ?? 0;
                            $salesAmountFromThisBatch = $purchaseProduct->sales_amount_from_this_batch ?? 0;
                            $profitFromThisBatch = $purchaseProduct->profit_from_this_batch ?? 0;
                            $remainingQuantity = $purchaseProduct->remaining_quantity ?? $purchaseProduct->pivot->quantity;

                            // Add to totals
                            $totalSalesProfit += $profitFromThisBatch;
                            $totalSalesAmount += $salesAmountFromThisBatch;
                        @endphp
                        <tr>
                            <td>
                                {{ $purchaseProduct->name }}
                                @if($purchaseProduct->has_transfers)
                                    <br><small class="text-info">
                                        <i class="fa fa-exchange-alt"></i> تم نقل جزء من هذا المنتج
                                    </small>
                                    <br><button type="button" class="btn btn-sm btn-info mt-1"
                                               onclick="showTransferHistory({{ $purchase->id }}, {{ $purchaseProduct->id }})">
                                        <i class="fa fa-history"></i> عرض تاريخ النقل
                                    </button>
                                @endif
                            </td>
                            <td>{{ $purchaseProduct->original_purchase_quantity }}</td>
                            <td>{{ $purchaseProduct->sold_from_this_batch }}</td>
                            <td>{{ $purchaseProduct->pivot->cost_price }}</td>
                            <td>{{ $purchaseProduct->selling_price }}</td>
                            <td>{{ number_format($profitFromThisBatch, 2) }}</td> <!-- Profit from sales of this product from this batch -->
                            <td>{{ number_format($salesAmountFromThisBatch, 2) }}</td> <!-- Total sales for this product from this batch -->
                            <td>
                                @if($remainingQuantity > 0)
                                    <!-- Add a button to initiate the transfer -->
                                    <a href="{{ route('purchases.transferProduct', ['purchase' => $purchase->id, 'product' => $purchaseProduct->id]) }}" class="btn btn-warning">نقل الكمية المتبقية</a>
                                    <small class="text-muted d-block">متبقي: {{ $remainingQuantity }}</small>
                                @elseif($purchaseProduct->has_transfers)
                                    <span class="text-info">
                                        <i class="fa fa-exchange-alt"></i> تم نقل الكمية بالكامل
                                    </span>
                                    <br><small class="text-success">الكمية المباعة قبل النقل: {{ $purchaseProduct->sold_from_this_batch }}</small>
                                    <br><small class="text-muted">الكمية المنقولة: {{ $purchaseProduct->transferred_quantity }}</small>
                                    <br><small class="text-muted">الكمية المشتراة: {{ $purchaseProduct->original_purchase_quantity }}</small>
                                @else
                                    <span class="text-success">
                                        <i class="fa fa-check-circle"></i> تم بيع الكمية بالكامل
                                    </span>
                                    <br><small class="text-muted">الكمية المباعة: {{ $purchaseProduct->sold_from_this_batch }}</small>
                                    <br><small class="text-muted">الكمية المشتراة: {{ $purchaseProduct->original_purchase_quantity }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7">إجمالي الربح من مبيعات هذه الفاتورة حتي الان :</th>
                        <th>{{ $totalSalesProfit }} ج . م</th>
                    </tr>
                    <tr>
                        <th colspan="7">إجمالي المبيعات في هذه الفاتورة حتي الان :</th>
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

<!-- Transfer History Modal -->
<div class="modal fade" id="transferHistoryModal" tabindex="-1" aria-labelledby="transferHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferHistoryModalLabel">تاريخ نقل المنتج</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transferHistoryContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
function showTransferHistory(purchaseId, productId) {
    fetch(`/purchases/${purchaseId}/transfer-history/${productId}`)
        .then(response => response.json())
        .then(data => {
            let content = '<div class="table-responsive"><table class="table table-bordered">';
            content += '<thead><tr><th>التاريخ</th><th>المنتج الجديد</th><th>الكمية المنقولة</th><th>سعر الشراء الجديد</th><th>سعر البيع الجديد</th><th>الفاتورة الجديدة</th></tr></thead>';
            content += '<tbody>';

            data.forEach(transfer => {
                content += `<tr>
                    <td>${transfer.formatted_created_at}</td>
                    <td>${transfer.new_product_name}</td>
                    <td>${transfer.transferred_quantity}</td>
                    <td>${transfer.new_cost_price} ج.م</td>
                    <td>${transfer.new_selling_price} ج.م</td>
                    <td>${transfer.new_invoice_number}</td>
                </tr>`;
            });

            content += '</tbody></table></div>';

            document.getElementById('transferHistoryContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('transferHistoryModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('transferHistoryContent').innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل تاريخ النقل</div>';
            new bootstrap.Modal(document.getElementById('transferHistoryModal')).show();
        });
}
</script>
@endsection
