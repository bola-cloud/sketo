@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow p-4">
            <h1 class="text-center mb-4">{{ __('app.quantity_updates.title') }}</h1>

            <!-- Add Select2 search dropdown for product filter -->
            <div class="mb-4">
                <label for="product_search">{{ __('app.products.search_product') }}</label>
                <select id="product_search" class="form-control">
                    <option value="">{{ __('app.common.select_product') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>{{ __('app.products.product_name') }}</th>
                            <th>{{ __('app.quantity_updates.old_quantity') }}</th>
                            <th>{{ __('app.quantity_updates.new_quantity') }}</th>
                            <th>{{ __('app.quantity_updates.action') }}</th>
                            <th>{{ __('app.users.user') }}</th>
                            <th>{{ __('app.common.date') }}</th>
                        </tr>
                    </thead>
                    <tbody id="update_table_body">
                        @foreach($quantityUpdates as $update)
                            <tr class="product-row" data-product-id="{{ $update->product_id }}">
                                <td>{{ $update->product_name }}</td>
                                <td>{{ $update->old_quantity ?? '------' }}</td>
                                <td>{{ $update->new_quantity }}</td>
                                <td>{{ $update->action == 'added' ? __('app.quantity_updates.added') : __('app.quantity_updates.updated') }}
                                </td>
                                <td>{{ $update->user_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($update->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Select2 on the search input
            $('#product_search').select2({
                placeholder: "{{ __('app.products.search_product') }}",
                allowClear: true
            });

            // Event listener for when a product is selected
            $('#product_search').on('change', function () {
                var selectedProductId = $(this).val();

                // Show all rows if no product is selected
                if (!selectedProductId) {
                    $('.product-row').show();
                } else {
                    // Hide all rows first
                    $('.product-row').hide();

                    // Show only rows that match the selected product
                    $('.product-row').each(function () {
                        if ($(this).data('product-id') == selectedProductId) {
                            $(this).show();
                        }
                    });
                }
            });
        });
    </script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery (needed for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

@endpush