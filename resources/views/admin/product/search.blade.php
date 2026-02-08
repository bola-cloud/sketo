@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.products.search_barcode_title') }}</h1>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form id="barcode-search-form">
            @csrf
            <div class="form-group">
                <label for="barcode">{{ __('app.products.barcode') }}</label>
                <input type="text" class="form-control" id="barcode" name="barcode" required>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.common.search') }}</button>
        </form>

        <div id="product-details" class="mt-4">
            <!-- سيتم عرض تفاصيل المنتج هنا -->
        </div>
    </div>

    <script src="{{asset('assets/js/jquery.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#barcode-search-form').on('submit', function (e) {
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
                    success: function (data) {
                        $('#product-details').html(`
                            <h2>{{ __('app.products.product_details') }}</h2>
                            <p><strong>{{ __('app.products.name') }}:</strong> ${data.name}</p>
                            <p><strong>{{ __('app.products.category') }}:</strong> ${data.category}</p>
                            <p><strong>{{ __('app.products.cost_price') }}:</strong> ${data.cost_price}</p>
                            <p><strong>{{ __('app.products.selling_price') }}:</strong> ${data.selling_price}</p>
                            <p><strong>{{ __('app.products.quantity') }}:</strong> ${data.quantity}</p>
                            <p><strong>{{ __('app.products.barcode') }}:</strong> ${data.barcode}</p>
                            <img src="${data.barcode_image}" alt="barcode" />
                        `);
                    },
                    error: function (xhr) {
                        $('#product-details').html('<p class="text-danger">{{ __('app.products.not_found') }}</p>');
                    }
                });
            });
        });
    </script>
@endsection