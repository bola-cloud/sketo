@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="text-center">الكاشير - عربة التسوق</h1>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Barcode Scanning -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="form-group">
                <label for="barcode">مسح الباركود</label>
                <input type="text" class="form-control" id="barcode" name="barcode" placeholder="مسح الباركود هنا..." autofocus required>
            </div>
        </div>
    </div>

    <!-- Product Name Search -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="form-group">
                <label for="product_name">البحث عن المنتج</label>
                <input type="text" class="form-control" id="product_name" name="product_name" placeholder="أدخل اسم المنتج...">
                <ul id="productList" class="list-group mt-2"></ul> <!-- Container for showing search results -->
            </div>
        </div>
    </div>

    @if(!empty($cart))
    <!-- Cart Table -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <h2 class="text-center">عربة التسوق</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="table-primary">
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $barcode => $details)
                        <tr>
                            <td>{{ $details['name'] }}</td>
                            <td>
                                <div class="input-group">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="updateCartQuantity('{{ $barcode }}', -1)">-</button>
                                    <input type="text" class="form-control text-center" value="{{ $details['quantity'] }}" readonly style="max-width: 50px; max-height: 33px;">
                                    <button type="button" class="btn btn-success btn-sm" onclick="updateCartQuantity('{{ $barcode }}', 1)">+</button>
                                </div>
                            </td>
                            <td>{{ number_format($details['price'], 2) }}</td>
                            <td>{{ number_format($details['price'] * $details['quantity'], 2) }}</td>
                            <td>
                                <form action="{{ route('cashier.removeFromCart') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="barcode" value="{{ $barcode }}">
                                    <button type="submit" class="btn btn-danger btn-sm">إزالة</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <th colspan="3" class="text-right">الإجمالي قبل الخصم</th>
                        <th>{{ number_format($subtotal, 2) }}</th>
                        <th></th>
                    </tr>
                    <tr class="table-warning">
                        <th colspan="3" class="text-right">الخصم</th>
                        <th>
                            <input type="text" id="discount" name="discount" class="form-control" value="0" placeholder="أدخل الخصم" />
                        </th>
                        <th></th>
                    </tr>

                    <tr class="table-success">
                        <th colspan="3" class="text-right">الإجمالي بعد الخصم</th>
                        <th id="total_after_discount">{{ number_format($subtotal - ($discount ?? 0), 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Checkout Form -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <h3 class="text-center">تفاصيل الفاتورة</h3>
            <form action="{{ route('cashier.checkout') }}" method="POST">
                @csrf
        
                <!-- client Selection -->
                <div class="form-group">
                    <label for="client_id">اختر العميل (اختياري)</label>
                    <select class="form-control select2-client" id="client_id" name="client_id">
                        <option value="" selected>اختر العميل (اختياري)</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <!-- Paid Amount -->
                <div class="form-group">
                    <label for="paid_amount">المبلغ المدفوع</label>
                    <input type="text" 
                           class="form-control @error('paid_amount') is-invalid @enderror" 
                           id="paid_amount" 
                           name="paid_amount" 
                           placeholder="أدخل المبلغ المدفوع" 
                           required 
                           value="{{ old('paid_amount') }}">
                    @error('paid_amount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
        
                <!-- Hidden Fields for Discount Handling (if needed) -->
                <input type="hidden" name="apply_discount_hidden" id="apply_discount_hidden" value="0" />
        
                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-success mt-4">إتمام الدفع و طباعة الفاتورة</button>
                </div>
            </form>
        </div>
    </div>
    @else
        <div class="text-center">
            <p class="mt-4">عربة التسوق فارغة.</p>
        </div>
    @endif
</div>

<!-- jQuery -->
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script>
    // Function to update cart quantity via AJAX
    function updateCartQuantity(barcode, quantityChange) {
        $.ajax({
            url: "{{ route('cashier.updateCartQuantity') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode,
                quantity_change: quantityChange
            },
            success: function(response) {
                $('body').html(response); // Replace the entire body with the updated content
            },
            error: function(xhr) {
                alert('فشل في تحديث الكمية.');
            }
        });
    }

    $(document).ready(function() {
        // Barcode input handling
        let barcodeTimer;
        $('#barcode').on('input', function() {
            clearTimeout(barcodeTimer);  // Clear the previous timer if there is one

            let barcode = $(this).val().trim();

            if (barcode.length) {
                // Set a new timer to delay the execution of the AJAX request by 1 second (1000 ms)
                barcodeTimer = setTimeout(function() {
                    $('#barcode').prop('disabled', true);
                    $.ajax({
                        url: "{{ route('cashier.addToCart') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            barcode: barcode
                        },
                        success: function(response) {
                            $('#barcode').val('');  // Clear the barcode input
                            $('#barcode').prop('disabled', false);  // Enable the input again
                            $('body').html(response);  // Replace the entire body with the updated content
                        },
                        error: function(xhr) {
                            alert('المنتج غير موجود أو فشل في الإضافة إلى العربة.');
                            $('#barcode').prop('disabled', false);
                        }
                    });
                }, 1000);  // Delay the execution by 1 second (1000 ms)
            }
        });


        // Product name search handling
        $('#product_name').on('input', function() {
            var query = $(this).val().trim();

            if (query.length >= 2) { // Ensure the query is at least 2 characters long
                $.ajax({
                    url: "{{ route('cashier.searchProductByName') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#productList').empty(); // Clear previous results
                        if (data.length === 0) {
                            $('#productList').append('<li class="list-group-item">لا توجد نتائج</li>');
                        } else {
                            data.forEach(function(product) {
                                $('#productList').append(
                                    '<li class="list-group-item" onclick="selectProduct(\'' + product.barcode + '\', \'' + product.name + '\')">' + product.name + '</li>'
                                );
                            });
                        }
                    },
                    error: function() {
                        $('#productList').empty();
                        $('#productList').append('<li class="list-group-item">حدث خطأ ما، حاول مرة أخرى.');
                    }
                });
            } else {
                $('#productList').empty(); // Clear list if query is too short
            }
        });

        // Discount input handling and updating total
        $('#discount').on('input', function() {
            var discount = parseFloat($(this).val()) || 0; // Get discount value or default to 0
            var subtotal = {{ $subtotal }}; // Assuming subtotal is passed from the server

            // Calculate total after discount
            var totalAfterDiscount = subtotal - discount;

            // Update the total displayed
            $('#total_after_discount').text(totalAfterDiscount.toFixed(2));

            // Update the hidden discount field to submit the discount value
            $('#apply_discount_hidden').val(discount);
        });
    });

    // Function to select a product from search results
    function selectProduct(barcode, name) {
        $('#barcode').val(barcode);
        $('#product_name').val(name);
        $('#productList').empty();

        // Optionally, trigger the add to cart process immediately after selecting a product
        $.ajax({
            url: "{{ route('cashier.addToCart') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode
            },
            success: function(response) {
                $('#barcode').val('');
                $('body').html(response); // Refresh the page content with the updated cart
            },
            error: function(xhr) {
                alert('فشل في إضافة المنتج إلى الفاتورة. تأكد من صحة الباركود.');
            }
        });
    }
</script>
@endsection
@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

<script>
    $(document).ready(function() {
        // Initialize Select2 for supplier dropdown
        $('.select2-client').select2({
            placeholder: "اختر المورد (اختياري)",
            allowClear: true,
            width: '100%' // Ensure the Select2 dropdowns are 100% width
        });
    });
</script>
@endpush