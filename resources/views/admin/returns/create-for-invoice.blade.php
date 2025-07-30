@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">إرجاع منتجات - فاتورة رقم: {{ $invoice->invoice_code }}</h4>
                <div class="heading-elements">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للفاتورة
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="card-body">
                    <!-- Invoice Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>تفاصيل الفاتورة</h5>
                                    <p><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_code }}</p>
                                    <p><strong>اسم العميل:</strong> {{ $invoice->buyer_name }}</p>
                                    <p><strong>رقم الهاتف:</strong> {{ $invoice->buyer_phone }}</p>
                                    <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                                    <p><strong>إجمالي الفاتورة:</strong> {{ number_format($invoice->total_amount, 2) }} ج.م</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($availableItems->count() > 0)
                        <form method="POST" action="{{ route('customer-returns.store') }}">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكمية المباعة</th>
                                            <th>المتاح للإرجاع</th>
                                            <th>كمية الإرجاع</th>
                                            <th>سبب الإرجاع</th>
                                            <th>السعر الوحدة</th>
                                            <th>مبلغ الإرجاع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableItems as $index => $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->available_for_return }}</td>
                                                <td>
                                                    <input type="number"
                                                           name="returns[{{ $index }}][quantity]"
                                                           class="form-control quantity-input"
                                                           min="0"
                                                           max="{{ $item->available_for_return }}"
                                                           value="0"
                                                           data-price="{{ $item->total_price / $item->quantity }}"
                                                           onchange="calculateReturnAmount(this)">
                                                    <input type="hidden" name="returns[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                </td>
                                                <td>
                                                    <select name="returns[{{ $index }}][reason]" class="form-control reason-select" disabled>
                                                        <option value="">-- اختر السبب --</option>
                                                        <option value="عيب في المنتج">عيب في المنتج</option>
                                                        <option value="منتج غير مطابق للمواصفات">منتج غير مطابق للمواصفات</option>
                                                        <option value="منتج منتهي الصلاحية">منتج منتهي الصلاحية</option>
                                                        <option value="طلب العميل">طلب العميل</option>
                                                        <option value="خطأ في الفاتورة">خطأ في الفاتورة</option>
                                                        <option value="أخرى">أخرى</option>
                                                    </select>
                                                </td>
                                                <td>{{ number_format($item->total_price / $item->quantity, 2) }} ج.م</td>
                                                <td class="return-amount">0.00 ج.م</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6">إجمالي مبلغ الإرجاع:</th>
                                            <th id="total-return-amount">0.00 ج.م</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success" id="submit-btn" disabled>
                                    <i class="fa fa-check"></i> تأكيد الإرجاع
                                </button>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> إلغاء
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <h4>لا توجد منتجات متاحة للإرجاع</h4>
                            <p>جميع المنتجات في هذه الفاتورة تم إرجاعها بالفعل أو لا تحتوي على كميات قابلة للإرجاع.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateReturnAmount(input) {
    const row = input.closest('tr');
    const quantity = parseInt(input.value) || 0;
    const price = parseFloat(input.dataset.price);
    const returnAmount = quantity * price;

    // Update return amount for this row
    row.querySelector('.return-amount').textContent = returnAmount.toFixed(2) + ' ج.م';

    // Enable/disable reason select
    const reasonSelect = row.querySelector('.reason-select');
    if (quantity > 0) {
        reasonSelect.disabled = false;
        reasonSelect.required = true;
    } else {
        reasonSelect.disabled = true;
        reasonSelect.required = false;
        reasonSelect.value = '';
    }

    // Calculate total return amount
    calculateTotalReturnAmount();

    // Enable/disable submit button
    updateSubmitButton();
}

function calculateTotalReturnAmount() {
    let total = 0;
    document.querySelectorAll('.quantity-input').forEach(input => {
        const quantity = parseInt(input.value) || 0;
        const price = parseFloat(input.dataset.price);
        total += quantity * price;
    });

    document.getElementById('total-return-amount').textContent = total.toFixed(2) + ' ج.م';
}

function updateSubmitButton() {
    const hasQuantity = Array.from(document.querySelectorAll('.quantity-input')).some(input => parseInt(input.value) > 0);
    const hasReasons = Array.from(document.querySelectorAll('.reason-select:not([disabled])')).every(select => select.value !== '');

    document.getElementById('submit-btn').disabled = !hasQuantity || !hasReasons;
}

// Add event listeners to reason selects
document.querySelectorAll('.reason-select').forEach(select => {
    select.addEventListener('change', updateSubmitButton);
});
</script>
@endsection
