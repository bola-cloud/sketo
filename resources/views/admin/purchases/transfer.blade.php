@extends('layouts.admin')

@section('content')
<div class="container-fluid card p-5">
    <h1 class="text-center mb-4">نقل المنتج وإنشاء منتج جديد</h1>

    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>

    <!-- Product Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات المنتج الأصلي</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>اسم المنتج:</strong> {{ $product->name }}<br>
                    <strong>الفئة:</strong> {{ $product->category->name ?? 'غير محدد' }}<br>
                    <strong>الماركة:</strong> {{ $product->brand->name ?? 'غير محدد' }}
                </div>
                <div class="col-md-6">
                    <strong>الكمية المشتراة:</strong> {{ $purchaseProduct->quantity ?? 0 }}<br>
                    <strong>الكمية المباعة:</strong> <span class="text-success">{{ $soldQuantityFromThisBatch ?? 0 }}</span><br>
                    <strong>الكمية المتبقية:</strong> <span class="badge bg-success">{{ $remainingQuantity }}</span><br>
                    <strong>سعر التكلفة الحالي:</strong> {{ number_format($product->cost_price, 2) }} ج.م<br>
                    <strong>سعر البيع الحالي:</strong> {{ number_format($product->selling_price, 2) }} ج.م
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('purchases.transferProduct.store', ['purchase' => $purchase->id, 'product' => $product->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="new_product_name" class="form-label">
                        <i class="fas fa-tag me-1"></i>اسم المنتج الجديد <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="new_product_name" name="new_product_name"
                           class="form-control @error('new_product_name') is-invalid @enderror"
                           value="{{ old('new_product_name', $product->name . ' - منقول') }}"
                           placeholder="أدخل اسم المنتج الجديد" required>
                    @error('new_product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="transfer_quantity" class="form-label">
                        <i class="fas fa-cubes me-1"></i>كمية النقل <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="transfer_quantity" name="transfer_quantity"
                           class="form-control @error('transfer_quantity') is-invalid @enderror"
                           value="{{ old('transfer_quantity', $remainingQuantity) }}"
                           min="1" max="{{ $remainingQuantity }}"
                           placeholder="أدخل كمية النقل" required>
                    <small class="form-text text-muted">الحد الأقصى: {{ $remainingQuantity }}</small>
                    @error('transfer_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="new_cost_price" class="form-label">
                        <i class="fas fa-dollar-sign me-1"></i>سعر الشراء الجديد <span class="text-danger">*</span>
                    </label>
                    <input type="number" step="0.01" id="new_cost_price" name="new_cost_price"
                           class="form-control @error('new_cost_price') is-invalid @enderror"
                           value="{{ old('new_cost_price', $product->cost_price) }}"
                           placeholder="أدخل سعر الشراء الجديد" required>
                    @error('new_cost_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="new_selling_price" class="form-label">
                        <i class="fas fa-tags me-1"></i>سعر البيع الجديد <span class="text-danger">*</span>
                    </label>
                    <input type="number" step="0.01" id="new_selling_price" name="new_selling_price"
                           class="form-control @error('new_selling_price') is-invalid @enderror"
                           value="{{ old('new_selling_price', $product->selling_price) }}"
                           placeholder="أدخل سعر البيع الجديد" required>
                    @error('new_selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="new_purchase_id" class="form-label">
                        <i class="fas fa-file-invoice me-1"></i>فاتورة الشراء الجديدة <span class="text-danger">*</span>
                    </label>
                    <select id="new_purchase_id" name="new_purchase_id"
                            class="form-control select2 @error('new_purchase_id') is-invalid @enderror" required>
                        <option value="">اختر فاتورة الشراء الجديدة</option>
                        @foreach($purchases as $purchaseOption)
                            <option value="{{ $purchaseOption->id }}"
                                    {{ old('new_purchase_id') == $purchaseOption->id ? 'selected' : '' }}>
                                {{ $purchaseOption->invoice_number }} - {{ $purchaseOption->supplier->name ?? 'غير محدد' }}
                                ({{ number_format($purchaseOption->total_amount, 2) }} ج.م)
                            </option>
                        @endforeach
                    </select>
                    @error('new_purchase_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="image" class="form-label">
                        <i class="fas fa-image me-1"></i>صورة المنتج الجديد (اختياري)
                    </label>
                    <input type="file" id="image" name="image"
                           class="form-control @error('image') is-invalid @enderror"
                           accept="image/*">
                    <small class="form-text text-muted">إذا لم تختار صورة، سيتم نسخ صورة المنتج الأصلي</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>ملخص النقل</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>المنتج الأصلي:</strong><br>
                        <span class="text-muted">{{ $product->name }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>المنتج الجديد:</strong><br>
                        <span class="text-primary" id="new-product-summary">{{ old('new_product_name', $product->name . ' - منقول') }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>الكمية المنقولة:</strong><br>
                        <span class="text-success" id="quantity-summary">{{ old('transfer_quantity', $remainingQuantity) }}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>الأسعار الجديدة:</strong><br>
                        <span class="text-info">التكلفة: <span id="cost-summary">{{ old('new_cost_price', $product->cost_price) }}</span> ج.م</span><br>
                        <span class="text-success">البيع: <span id="selling-summary">{{ old('new_selling_price', $product->selling_price) }}</span> ج.م</span>
                    </div>
                    <div class="col-md-6">
                        <strong>الربح المتوقع:</strong><br>
                        <span class="text-warning" id="profit-summary">
                            {{ number_format((old('new_selling_price', $product->selling_price) - old('new_cost_price', $product->cost_price)) * old('transfer_quantity', $remainingQuantity), 2) }} ج.م
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>إلغاء
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-exchange-alt me-1"></i>نقل المنتج وإنشاء منتج جديد
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Include Select2 CSS and JS -->
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#new_purchase_id').select2({
            placeholder: 'اختر فاتورة الشراء الجديدة',
            allowClear: true,
            width: '100%'
        });

        // Real-time summary updates
        function updateSummary() {
            const newName = $('#new_product_name').val();
            const quantity = parseInt($('#transfer_quantity').val()) || 0;
            const costPrice = parseFloat($('#new_cost_price').val()) || 0;
            const sellingPrice = parseFloat($('#new_selling_price').val()) || 0;

            // Update summary fields
            $('#new-product-summary').text(newName);
            $('#quantity-summary').text(quantity);
            $('#cost-summary').text(costPrice.toFixed(2));
            $('#selling-summary').text(sellingPrice.toFixed(2));

            // Calculate and update profit
            const profit = (sellingPrice - costPrice) * quantity;
            $('#profit-summary').text(profit.toFixed(2) + ' ج.م');

            // Color coding for profit
            if (profit > 0) {
                $('#profit-summary').removeClass('text-danger').addClass('text-success');
            } else if (profit < 0) {
                $('#profit-summary').removeClass('text-success').addClass('text-danger');
            } else {
                $('#profit-summary').removeClass('text-success text-danger').addClass('text-warning');
            }
        }

        // Bind events for real-time updates
        $('#new_product_name, #transfer_quantity, #new_cost_price, #new_selling_price').on('input', updateSummary);

        // Initial summary update
        updateSummary();
    });
</script>
@endpush
