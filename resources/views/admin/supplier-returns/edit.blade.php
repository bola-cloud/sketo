@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header">
            <h1>{{ __('app.supplier_returns.edit_title') }}{{ $supplierReturn->id }}</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('supplier-returns.update', $supplierReturn) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">{{ __('app.supplier_returns.supplier') }} <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            <option value="">{{ __('app.supplier_returns.select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                        {{ (old('supplier_id', $supplierReturn->supplier_id) == $supplier->id) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="product_id">{{ __('app.supplier_returns.product') }} <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">{{ __('app.supplier_returns.select_product') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-quantity="{{ $product->quantity }}"
                                        {{ (old('product_id', $supplierReturn->product_id) == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ __('app.supplier_returns.available_quantity') }} {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('app.supplier_returns.available_quantity') }} <span id="available-quantity">{{ $supplierReturn->product->quantity }}</span></small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchase_id">{{ __('app.supplier_returns.purchase_invoice') }}</label>
                        <select name="purchase_id" id="purchase_id" class="form-control">
                            <option value="">{{ __('app.supplier_returns.select_purchase') }}</option>
                            @foreach($purchases as $purchase)
                                <option value="{{ $purchase->id }}"
                                        {{ (old('purchase_id', $supplierReturn->purchase_id) == $purchase->id) ? 'selected' : '' }}>
                                    {{ $purchase->invoice_number }} - {{ $purchase->supplier->name ?? __('app.common.not_specified') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="quantity_returned">{{ __('app.supplier_returns.quantity_returned') }} <span class="text-danger">*</span></label>
                        <input type="number" name="quantity_returned" id="quantity_returned"
                               class="form-control" value="{{ old('quantity_returned', $supplierReturn->quantity_returned) }}"
                               min="1" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cost_price">{{ __('app.supplier_returns.cost_price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="cost_price" id="cost_price"
                               class="form-control" value="{{ old('cost_price', $supplierReturn->cost_price) }}"
                               min="0" step="0.01" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reason">{{ __('app.supplier_returns.reason') }}</label>
                        <input type="text" name="reason" id="reason"
                               class="form-control" value="{{ old('reason', $supplierReturn->reason) }}"
                               placeholder="{{ __('app.supplier_returns.reason_placeholder') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="notes">{{ __('app.supplier_returns.notes') }}</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                  placeholder="{{ __('app.supplier_returns.notes_placeholder') }}">{{ old('notes', $supplierReturn->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('app.supplier_returns.submit') }}</button>
                <a href="{{ route('supplier-returns.show', $supplierReturn) }}" class="btn btn-secondary">{{ __('app.common.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('supplier_id');
    const productSelect = document.getElementById('product_id');
    const purchaseSelect = document.getElementById('purchase_id');
    const availableQuantitySpan = document.getElementById('available-quantity');
    const quantityInput = document.getElementById('quantity_returned');

    // Update available quantity when product changes
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const quantity = selectedOption.getAttribute('data-quantity') || 0;
        availableQuantitySpan.textContent = quantity;
        quantityInput.max = quantity;
    });

    // Filter products and purchases by supplier
    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;

        if (supplierId) {
            // Filter purchases by supplier
            fetch(`/supplier-returns/purchases-by-supplier/${supplierId}`)
                .then(response => response.json())
                .then(purchases => {
                    purchaseSelect.innerHTML = '<option value="">{{ __('app.supplier_returns.select_purchase') }}</option>';
                    purchases.forEach(purchase => {
                        const option = document.createElement('option');
                        option.value = purchase.id;
                        option.textContent = `${purchase.invoice_number} - ${purchase.supplier?.name || '{{ __('app.common.not_specified') }}'}`;
                        if (purchase.id == {{ $supplierReturn->purchase_id ?? 'null' }}) {
                            option.selected = true;
                        }
                        purchaseSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching purchases:', error);
                });
        } else {
            purchaseSelect.innerHTML = '<option value="">{{ __('app.supplier_returns.select_purchase') }}</option>';
        }
    });

    // Initialize available quantity if product is pre-selected
    if (productSelect.value) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = selectedOption.getAttribute('data-quantity') || 0;
        availableQuantitySpan.textContent = quantity;
        quantityInput.max = quantity;
    }
});
</script>
@endsection
