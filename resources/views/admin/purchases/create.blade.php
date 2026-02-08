@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">{{ __('app.purchases.create_title') }}</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="invoice_number">{{ __('app.purchases.invoice_number') }}</label>
                <input type="text" name="invoice_number" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type">{{ __('app.purchases.type') }}</label>
                <select name="type" id="type" class="form-control" required>
                    <option selected disabled>{{ __('app.purchases.select_type') }}</option>
                    <option value="product">{{ __('app.purchases.type_product') }}</option>
                    <option value="expense">{{ __('app.purchases.type_expense') }}</option>
                </select>
            </div>

            <!-- Supplier selection for "شراء منتجات" type only -->
            <div class="form-group w-100" id="supplier-section" style="display:none;">
                <label for="supplier_id">{{ __('app.purchases.supplier') }}</label>
                <select name="supplier_id" id="supplier_id" class="form-control select2-single">
                    <option value="">{{ __('app.purchases.select_supplier') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }} - {{ $supplier->phone }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="description-section">
                <label for="description">{{ __('app.purchases.description') }}</label>
                <input type="text" name="description" class="form-control">
            </div>

            <div class="form-group">
                <label for="paid_amount">{{ __('app.invoices.paid_amount') }}</label>
                <input type="number" name="paid_amount" class="form-control" required>
            </div>

            <!-- Only visible for expense type -->
            <div class="form-group" id="total-amount-section" style="display:none;">
                <label for="total_amount">{{ __('app.invoices.total') }}</label>
                <input type="number" name="total_amount" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary btn-block">{{ __('app.purchases.save') }}</button>
        </form>
    </div>


@endsection
@push('scripts')
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="{{asset('js/select2.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 for supplier dropdown
            $('.select2-single').select2({
                placeholder: "اختر المورد",
                allowClear: true,
                width: '100%' // Ensure the Select2 dropdowns are 100% width
            });

            // Show/Hide supplier section and total amount section based on invoice type
            $('#type').on('change', function () {
                var type = $(this).val();
                if (type === 'product') {
                    $('#supplier-section').show();  // Show supplier dropdown for "product"
                    $('#total-amount-section').hide();  // Hide total amount section for "product"
                } else if (type === 'expense') {
                    $('#supplier-section').hide();  // Hide supplier dropdown for "expense"
                    $('#total-amount-section').show();  // Show total amount section for "expense"
                } else {
                    $('#supplier-section').hide();
                    $('#total-amount-section').hide();
                }
            });
        });
    </script>
@endpush