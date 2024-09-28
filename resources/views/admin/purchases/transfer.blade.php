@extends('layouts.admin')

@section('content')
<div class="container-fluid card p-5">
    <h1 class="text-center mb-4">نقل الكمية المتبقية للمنتج</h1>
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

    <form action="{{ route('purchases.transferProduct.store', ['purchase' => $purchase->id, 'product' => $product->id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_name">اسم المنتج الجديد</label>
            <input type="text" id="product_name" name="product_name" class="form-control" value="{{ old('product_name', $product->name) }}" required>
            @error('product_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_purchase_id">رقم الفاتورة الجديدة</label>
            <select id="new_purchase_id" name="new_purchase_id" class="form-control select2" required>
                <option value="">اختر الفاتورة</option>
                @foreach($purchases as $purchaseOption)
                    <option value="{{ $purchaseOption->id }}">{{ $purchaseOption->invoice_number }} - {{ $purchaseOption->total_amount }} ج.م</option>
                @endforeach
            </select>
            @error('new_purchase_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>        

        <div class="form-group">
            <label for="new_cost_price">سعر الشراء الجديد</label>
            <input type="number" step="0.01" id="new_cost_price" name="new_cost_price" class="form-control" required>
            @error('new_cost_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_selling_price">سعر البيع الجديد</label>
            <input type="number" step="0.01" id="new_selling_price" name="new_selling_price" class="form-control" required>
            @error('new_selling_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>الكمية المتبقية:</label>
            <p>{{ $remainingQuantity }}</p>
        </div>

        <button type="submit" class="btn btn-success">نقل الكمية المتبقية</button>
        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection

@push('scripts')
<!-- Include Select2 CSS and JS -->
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#new_purchase_id').select2({
            placeholder: 'اختر الفاتورة',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
