@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.purchases.create_title') }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('app.purchases.all') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.add') }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 d-flex justify-content-end align-items-center">
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary round px-3 shadow-sm">
            <i class="la la-arrow-left"></i> {{ __('app.common.back') ?? 'Back to List' }}
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-12">
            <div class="card premium-card border-0 shadow-lg mb-4 animate-fade-in-up" style="border-radius: 28px;">
                <div class="card-content">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('purchases.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="invoice_number" class="text-bold-600">{{ __('app.purchases.invoice_number') }}</label>
                                    <input type="text" name="invoice_number" class="form-control round border-primary" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="type" class="text-bold-600">{{ __('app.purchases.type') }}</label>
                                    <select name="type" id="type" class="form-control round border-primary" required>
                                        <option selected disabled>{{ __('app.purchases.select_type') }}</option>
                                        <option value="product">{{ __('app.purchases.type_product') }}</option>
                                        <option value="expense">{{ __('app.purchases.type_expense') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-12 form-group" id="supplier-section" style="display:none;">
                                    <label for="supplier_id" class="text-bold-600">{{ __('app.purchases.supplier') }}</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2-single w-100">
                                        <option value="">{{ __('app.purchases.select_supplier') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }} - {{ $supplier->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 form-group" id="description-section">
                                    <label for="description" class="text-bold-600">{{ __('app.purchases.description') }}</label>
                                    <input type="text" name="description" class="form-control round border-primary">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="paid_amount" class="text-bold-600">{{ __('app.invoices.paid_amount') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="number" name="paid_amount" class="form-control round border-primary" required>
                                        <div class="form-control-position"><i class="la la-money primary"></i></div>
                                    </div>
                                </div>

                                <div class="col-md-6 form-group" id="total-amount-section" style="display:none;">
                                    <label for="total_amount" class="text-bold-600">{{ __('app.invoices.total') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="number" name="total_amount" class="form-control round border-primary">
                                        <div class="form-control-position"><i class="la la-calculator primary"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions text-center mt-3">
                                <button type="submit" class="btn btn-primary round btn-lg shadow-sm w-100" style="background: linear-gradient(135deg, var(--p-indigo), var(--p-purple)); border: none;">
                                    <i class="la la-check-square-o"></i> {{ __('app.purchases.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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