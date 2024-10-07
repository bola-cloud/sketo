@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>إضافة منتج</h1>

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

    <!-- Selection between existing and new product -->
    <div class="form-group">
        <label for="product_choice">اختر نوع المنتج</label>
        <select class="form-control" id="product_choice">
            <option value="" disabled selected>اختر نوع المنتج</option>
            <option value="existing">منتج موجود</option>
            <option value="new">منتج جديد</option>
        </select>
    </div>

    <!-- Existing Product Form -->
    <div id="existing_product_form" style="display:none;">
        <h3>إضافة كمية إلى منتج موجود</h3>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="existing_product">اختر منتج موجود</label>
                <select class="form-control select2-single" id="existing_product" name="existing_product">
                    <option value="" disabled selected>ابحث عن منتج...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - Barcode: {{ $product->barcode }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="purchase_id_existing">اختر فاتورة الشراء</label>
                <select class="form-control select2-single" id="purchase_id_existing" name="purchase_id_existing">
                    <option value="" disabled selected>اختر فاتورة الشراء</option>
                    @foreach($purchases as $purchase)
                        @if($purchase->type == 'product')
                            <option value="{{ $purchase->id }}">{{ $purchase->invoice_number }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantity_existing">الكمية المضافة</label>
                <input type="number" class="form-control" id="quantity_existing" name="quantity_existing" value="{{ old('quantity_existing') }}">
            </div>

            <button type="submit" class="btn btn-primary">إضافة الكمية إلى المنتج الموجود</button>
        </form>
    </div>

    <!-- New Product Form -->
    <div id="new_product_form" style="display:none;">
        <h3>إضافة منتج جديد</h3>
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">اسم المنتج</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="category_id">الفئة</label>
                <select class="form-control select2-single" id="category_id" name="category_id" required>
                    <option value="" disabled selected>اختر الفئة</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="purchase_id">فاتورة الشراء</label>
                <select class="form-control select2-single" id="purchase_id" name="purchase_id" required>
                    <option value="" disabled selected>اختر فاتورة الشراء</option>
                    @foreach($purchases as $purchase)
                        @if($purchase->type == 'product')
                            <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->invoice_number }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="cost_price">سعر التكلفة</label>
                <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" required>
            </div>

            <div class="form-group">
                <label for="selling_price">سعر البيع</label>
                <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" required>
            </div>

            <div class="form-group">
                <label for="color">الباركود</label>
                <input type="text" class="form-control" id="color" name="color" value="{{ old('color') }}" required>
            </div>

            <div class="form-group">
                <label for="quantity">الكمية</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
            </div>

            <div class="form-group">
                <label for="threshold">الحد الأدنى للكمية</label>
                <input type="number" class="form-control" id="threshold" name="threshold" value="{{ old('threshold') }}" required>
            </div>

            <div class="form-group">
                <label for="image">صورة المنتج</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>        

            <button type="submit" class="btn btn-primary">إضافة المنتج</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Include Select2 CSS -->
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="{{asset('js/select2.min.js')}}"></script>

<!-- Initialize Select2 and toggle forms based on product choice -->
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2 with custom width
        $('.select2-single').select2({
            placeholder: "ابحث عن منتج...",
            allowClear: true,
            width: '100%' // Ensure the Select2 dropdowns are 100% width
        });

        // Show/hide forms based on product choice
        $('#product_choice').on('change', function() {
            var selectedOption = $(this).val();
            if (selectedOption === 'existing') {
                $('#existing_product_form').show();  // Show existing product form
                $('#new_product_form').hide();       // Hide new product form
            } else if (selectedOption === 'new') {
                $('#existing_product_form').hide();  // Hide existing product form
                $('#new_product_form').show();       // Show new product form
            }
        });
    });
</script>
@endpush
