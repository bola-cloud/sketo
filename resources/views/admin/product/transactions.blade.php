@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow p-4">
            <h1 class="text-center mb-4">{{ __('app.products.transactions_report') }}</h1>

            <!-- Select2 Search Dropdown for Product Filtering -->
            <div class="mb-4">
                <label for="product_search">{{ __('app.products.search_product') }}</label>
                <select id="product_search" class="form-control">
                    <option value="">{{ __('app.products.select_product') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>{{ __('app.products.name') }}</th>
                            <th>{{ __('app.common.type') }}</th>
                            <th>{{ __('app.products.quantity') }}</th>
                            <th>{{ __('app.common.user') }}</th>
                            <th>{{ __('app.products.invoice_purchase_number') }}</th>
                            <th>{{ __('app.common.date') }}</th>
                        </tr>
                    </thead>
                    <tbody id="transactions_table_body">
                        <!-- Display Added Quantities (Purchases) -->
                        @foreach($addedQuantities as $transaction)
                            <tr class="product-row" data-product-id="{{ $transaction->product_id }}">
                                <td>{{ $transaction->product_name }}</td>
                                <td><span class="badge bg-success">{{ __('app.common.add') }}</span></td>
                                <td>{{ $transaction->new_quantity - ($transaction->old_quantity ?? 0) }}</td>
                                <td>{{ $transaction->user_name }}</td>
                                <td>{{ $transaction->purchase_invoice ?? '---' }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach

                        <!-- Display Sold Quantities (Sales) -->
                        @foreach($soldQuantities as $transaction)
                            <tr class="product-row" data-product-id="{{ $transaction->product_id }}">
                                <td>{{ $transaction->product_name }}</td>
                                <td><span class="badge bg-danger">{{ __('app.common.sale') }}</span></td>
                                <td>{{ $transaction->sold_quantity }}</td>
                                <td>{{ $transaction->user_name }}</td>
                                <td>{{ $transaction->invoice_code }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection
@push('scripts')

    <!-- Include Select2 and jQuery -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#product_search').select2({
                placeholder: "{{ __('app.products.search_product') }}",
                allowClear: true
            });

            // Filter transactions based on selected product
            $('#product_search').on('change', function () {
                var selectedProductId = $(this).val();

                if (!selectedProductId) {
                    $('.product-row').show();
                } else {
                    $('.product-row').hide();
                    $('.product-row[data-product-id="' + selectedProductId + '"]').show();
                }
            });
        });
    </script>
@endpush