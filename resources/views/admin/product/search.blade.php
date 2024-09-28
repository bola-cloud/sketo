@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>البحث عن المنتج بواسطة الباركود</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form id="barcode-search-form">
        @csrf
        <div class="form-group">
            <label for="barcode">الباركود</label>
            <input type="text" class="form-control" id="barcode" name="barcode" required>
        </div>

        <button type="submit" class="btn btn-primary">بحث</button>
    </form>

    <div id="product-details" class="mt-4">
        <!-- سيتم عرض تفاصيل المنتج هنا -->
    </div>
</div>

<script src="{{asset('assets/js/jquery.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#barcode-search-form').on('submit', function(e) {
            e.preventDefault();

            var barcode = $('#barcode').val();
            var token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('products.search') }}",
                type: "POST",
                data: {
                    _token: token,
                    barcode: barcode
                },
                success: function(data) {
                    $('#product-details').html(`
                        <h2>تفاصيل المنتج</h2>
                        <p><strong>الاسم:</strong> ${data.name}</p>
                        <p><strong>الفئة:</strong> ${data.category}</p>
                        <p><strong>سعر التكلفة:</strong> ${data.cost_price}</p>
                        <p><strong>سعر البيع:</strong> ${data.selling_price}</p>
                        <p><strong>الكمية:</strong> ${data.quantity}</p>
                        <p><strong>الباركود:</strong> ${data.barcode}</p>
                        <img src="${data.barcode_image}" alt="barcode" />
                    `);
                },
                error: function(xhr) {
                    $('#product-details').html('<p class="text-danger">لم يتم العثور على المنتج.</p>');
                }
            });
        });
    });
</script>
@endsection
