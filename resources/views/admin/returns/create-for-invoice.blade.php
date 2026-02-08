@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('app.returns.return_for_invoice') }} {{ $invoice->invoice_code }}</h4>
                <div class="heading-elements">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> {{ __('app.common.back_to_invoice') }}
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
                                    <h5>{{ __('app.returns.invoice_details') }}</h5>
                                    <p><strong>{{ __('app.reports.invoice_code') }}:</strong> {{ $invoice->invoice_code }}</p>
                                    <p><strong>{{ __('app.clients.name') }}:</strong> {{ $invoice->buyer_name }}</p>
                                    <p><strong>{{ __('app.clients.phone') }}:</strong> {{ $invoice->buyer_phone }}</p>
                                    <p><strong>{{ __('app.returns.invoice_date') }}:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                                    <p><strong>{{ __('app.returns.invoice_total') }}:</strong> {{ number_format($invoice->total_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
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
                                            <th>{{ __('app.products.product') }}</th>
                                            <th>{{ __('app.returns.qty_sold') }}</th>
                                            <th>{{ __('app.returns.available_for_return') }}</th>
                                            <th>{{ __('app.returns.return_qty') }}</th>
                                            <th>{{ __('app.returns.return_reason') }}</th>
                                            <th>{{ __('app.returns.unit_price') }}</th>
                                            <th>{{ __('app.returns.return_amount') }}</th>
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
                                                        <option value="">{{ __('app.returns.select_reason') }}</option>
                                                        <option value="عيب في المنتج">{{ __('app.returns.reason_defect') }}</option>
                                                        <option value="منتج غير مطابق للمواصفات">{{ __('app.returns.reason_not_match') }}</option>
                                                        <option value="منتج منتهي الصلاحية">{{ __('app.returns.reason_expired') }}</option>
                                                        <option value="طلب العميل">{{ __('app.returns.reason_customer_request') }}</option>
                                                        <option value="خطأ في الفاتورة">{{ __('app.returns.reason_invoice_error') }}</option>
                                                        <option value="أخرى">{{ __('app.returns.reason_other') }}</option>
                                                    </select>
                                                </td>
                                                <td>{{ number_format($item->total_price / $item->quantity, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                                <td class="return-amount">0.00 {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6">{{ __('app.returns.total_return_amount') }}</th>
                                            <th id="total-return-amount">0.00 {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
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
                                    <i class="fa fa-check"></i> {{ __('app.returns.confirm_return') }}
                                </button>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> {{ __('app.common.cancel') }}
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <h4>{{ __('app.returns.no_items_available') }}</h4>
                            <p>{{ __('app.returns.no_items_msg') }}</p>
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
    row.querySelector('.return-amount').textContent = returnAmount.toFixed(2) + ' {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}';

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

    document.getElementById('total-return-amount').textContent = total.toFixed(2) + ' {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}';
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
