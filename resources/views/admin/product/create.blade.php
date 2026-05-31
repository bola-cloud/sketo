@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.products.add_new') }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('app.products.all_products') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.add') }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 d-flex justify-content-end align-items-center">
        <a href="{{ route('products.index') }}" class="btn btn-secondary round px-3 shadow-sm">
            <i class="la la-arrow-left"></i> {{ __('app.common.back') ?? 'Back to List' }}
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
            <div class="card premium-card border-0 shadow-lg mb-4 animate-fade-in-up" style="border-radius: 28px;">
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-2">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success mb-2"><strong>{{ __('app.common.success') }}!</strong> {{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mb-2"><strong>{{ __('app.common.error') }}!</strong> {{ session('error') }}</div>
                        @endif

                        <!-- Selection between existing and new product -->
                        <div class="form-group mb-4">
                            <label class="text-bold-700 text-muted small">{{ __('app.products.operation_type') }}</label>
                            <div class="row m-0 p-1 bg-glass-light border-white-10" style="border-radius: 18px;">
                                <div class="col-12 col-sm-6 p-1">
                                    <div class="text-center m-0 p-0 w-100">
                                        <input type="radio" id="choice_new" name="product_choice_radio" value="new" class="d-none" checked>
                                        <label class="w-100 py-2 m-0 round-lg cursor-pointer transition-all operation-label text-bold-600" for="choice_new">{{ __('app.products.new_product') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 p-1">
                                    <div class="text-center m-0 p-0 w-100">
                                        <input type="radio" id="choice_existing" name="product_choice_radio" value="existing" class="d-none">
                                        <label class="w-100 py-2 m-0 round-lg cursor-pointer transition-all operation-label text-bold-600" for="choice_existing">{{ __('app.products.existing_product') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Product Form -->
                        <div id="existing_product_form" style="display:none;">
                            <h4 class="text-bold-700 mb-2 primary"><i class="la la-plus-square"></i> {{ __('app.products.add_quantity_existing') }}</h4>
                            <form action="{{ route('products.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="existing_product" class="text-bold-600">{{ __('app.products.select_product') }}</label>
                                            <select class="form-control select2-single border-primary" id="existing_product" name="existing_product">
                                                <option value="" disabled selected>{{ __('app.products.search_product_placeholder') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->barcode }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="purchase_id_existing" class="text-bold-600">{{ __('app.products.purchase_invoice') }}</label>
                                            <select class="form-control select2-single border-primary" id="purchase_id_existing" name="purchase_id_existing">
                                                <option value="" disabled selected>{{ __('app.products.select_invoice') }}</option>
                                                @foreach($purchases as $purchase)
                                                    @if($purchase->type == 'product')
                                                        <option value="{{ $purchase->id }}">{{ $purchase->invoice_number }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="quantity_existing" class="text-bold-600">{{ __('app.products.added_quantity') }}</label>
                                            <input type="number" class="form-control round border-primary" id="quantity_existing" name="quantity_existing" placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions mt-4 text-center">
                                    <button type="submit" class="btn btn-primary round px-4 shadow py-1">
                                        <i class="la la-check"></i> {{ __('app.products.save_added_quantity') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- New Product Form -->
                        <div id="new_product_form">
                            <h4 class="text-bold-700 mb-2 primary"><i class="la la-cube"></i> {{ __('app.products.create_new_product') }}</h4>
                            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="product">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="name" class="text-bold-600">{{ __('app.products.name') }} <span class="danger">*</span></label>
                                            <input type="text" class="form-control round border-primary" id="name" name="name" 
                                                value="{{ old('name') }}" placeholder="{{ __('app.products.enter_name') }}" required>
                                                
                                            <div class="custom-control custom-checkbox mt-1 weight-checkbox-container">
                                                <input type="checkbox" class="custom-control-input" id="is_weighted" name="is_weighted" value="1">
                                                <label class="custom-control-label text-bold-600 warning" for="is_weighted">{{ __('app.products.sell_by_weight') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="category_id" class="text-bold-600">{{ __('app.products.category') }} <span class="danger">*</span></label>
                                            <div class="input-group flex-nowrap">
                                                <select class="form-control select2-single border-primary" id="category_id" name="category_id" required>
                                                    <option value="" disabled selected>{{ __('app.products.select_category') }}</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#quickCategoryModal"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="brand_id" class="text-bold-600">{{ __('app.products.brand') }}</label>
                                            <div class="input-group flex-nowrap">
                                                <select class="form-control select2-single border-primary" id="brand_id" name="brand_id">
                                                    <option value="" disabled selected>{{ __('app.products.select_brand') }}</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#quickBrandModal"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="purchase_id" class="text-bold-600">{{ __('app.products.purchase_invoice') }} <span class="danger">*</span></label>
                                            <div class="input-group flex-nowrap">
                                                <select class="form-control select2-single border-primary" id="purchase_id" name="purchase_id" required>
                                                    <option value="" disabled selected>{{ __('app.products.select_invoice') }}</option>
                                                    @foreach($purchases as $purchase)
                                                        @if($purchase->type == 'product')
                                                            <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                                                {{ $purchase->invoice_number }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#quickPurchaseModal"><i class="la la-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="cost_price" class="text-bold-600">{{ __('app.products.cost_price') }} <span class="danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control round border-primary" id="cost_price" name="cost_price" 
                                                    value="{{ old('cost_price') }}" placeholder="0.00" required>
                                                <div class="input-group-append"><span class="input-group-text bg-transparent border-0">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="selling_price" class="text-bold-600">{{ __('app.products.selling_price') }} <span class="danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control round border-primary" id="selling_price" name="selling_price" 
                                                    value="{{ old('selling_price') }}" placeholder="0.00" required>
                                                <div class="input-group-append"><span class="input-group-text bg-transparent border-0">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="color" class="text-bold-600">{{ __('app.products.barcode') }} <span class="danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text bg-transparent border-0"><i class="la la-barcode"></i></span></div>
                                                <input type="text" class="form-control round border-primary" id="color" name="color" 
                                                    value="{{ old('color') }}" placeholder="{{ __('app.products.search_placeholder') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="quantity" class="text-bold-600">{{ __('app.products.initial_quantity') }} <span class="danger">*</span></label>
                                            <input type="number" class="form-control round border-primary" id="quantity" name="quantity" 
                                                value="{{ old('quantity') }}" placeholder="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="threshold" class="text-bold-600">{{ __('app.products.alert_quantity') }} <span class="danger">*</span></label>
                                            <input type="number" class="form-control round border-primary" id="threshold" name="threshold" 
                                                value="{{ old('threshold', 5) }}" placeholder="5" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="expiry_date" class="text-bold-600">{{ __('app.products.expiry_date') }}</label>
                                            <input type="date" class="form-control round border-primary" id="expiry_date" name="expiry_date" 
                                                value="{{ old('expiry_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 physical-only-fields">
                                        <div class="form-group mb-2">
                                            <label for="expiry_alert_days" class="text-bold-600">{{ __('app.products.expiry_alert_days') }} <span class="danger">*</span></label>
                                            <input type="number" class="form-control round border-primary" id="expiry_alert_days" name="expiry_alert_days" 
                                                value="{{ old('expiry_alert_days', 30) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="image" class="text-bold-600">{{ __('app.products.image') }}</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                                <label class="custom-file-label round-lg" for="image">{{ __('app.products.choose_image') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub-units Configuration Section -->
                                    <div class="col-md-12 mt-3 physical-only-fields">
                                        <div class="card bg-light border-primary p-3" style="border-radius: 18px; border-style: dashed !important;">
                                            <h5 class="text-bold-700 primary mb-2"><i class="la la-sitemap"></i> {{ __('app.products.sub_units_settings') }}</h5>
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="has_sub_units" name="has_sub_units">
                                                <label class="custom-control-label text-bold-600" style="line-height: 1.8;" for="has_sub_units">{{ __('app.products.has_smaller_unit') }}</label>
                                            </div>
                                            
                                            <div id="sub_units_fields" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="sub_product_id" class="text-bold-600">{{ __('app.products.select_sub_product') }}</label>
                                                            <select class="form-control select2-single border-primary" id="sub_product_id" name="sub_product_id">
                                                                <option value="" selected disabled>{{ __('app.products.choose_sub_product') }}</option>
                                                                @foreach($products as $p)
                                                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="conversion_factor" class="text-bold-600">{{ __('app.products.conversion_factor') }}</label>
                                                            <input type="number" step="0.001" class="form-control round border-primary" id="conversion_factor" name="conversion_factor" placeholder="{{ __('app.products.example_conversion') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-info py-1 mb-0 mt-2">
                                                    <small><i class="la la-info-circle"></i> {{ __('app.products.sub_units_info') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions mt-4 text-center">
                                    <button type="submit" class="btn btn-primary round px-4 shadow py-1">
                                        <i class="la la-check"></i> {{ __('app.products.save') }}
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-light round px-4 ml-1">{{ __('app.products.cancel') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals Stack -->
@push('modals')
<!-- Quick Category Modal -->
<div class="modal fade text-left" id="quickCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> {{ __('app.products.quick_category_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('app.products.quick_category_name') }} <span class="danger">*</span></label>
                    <input type="text" id="quickCategoryName" class="form-control round border-primary" placeholder="{{ __('app.products.quick_category_placeholder') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">{{ __('app.products.cancel') }}</button>
                <button type="button" id="btnSaveCategory" class="btn btn-primary round">{{ __('app.products.save_and_add') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Brand Modal -->
<div class="modal fade text-left" id="quickBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> {{ __('app.products.quick_brand_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('app.products.quick_brand_name') }} <span class="danger">*</span></label>
                    <input type="text" id="quickBrandName" class="form-control round border-primary" placeholder="{{ __('app.products.quick_brand_placeholder') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">{{ __('app.products.cancel') }}</button>
                <button type="button" id="btnSaveBrand" class="btn btn-primary round">{{ __('app.products.save_and_add') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Purchase Modal -->
<div class="modal fade text-left" id="quickPurchaseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> {{ __('app.products.quick_purchase_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('app.products.quick_purchase_number') }} <span class="danger">*</span></label>
                    <input type="text" id="quickPurchaseNumber" class="form-control round border-primary" placeholder="{{ __('app.products.quick_purchase_placeholder') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('app.products.quick_purchase_supplier') }}</label>
                    <div class="input-group flex-nowrap">
                        <select id="quickPurchaseSupplier" class="form-control round border-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                            <option value="">{{ __('app.products.quick_purchase_supplier_placeholder') }}</option>
                            @foreach($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#quickSupplierModal" style="border-top-left-radius: 14px; border-bottom-left-radius: 14px;"><i class="la la-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">{{ __('app.products.cancel') }}</button>
                <button type="button" id="btnSavePurchase" class="btn btn-primary round">{{ __('app.products.save_and_add') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Supplier Modal -->
<div class="modal fade text-left" id="quickSupplierModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> {{ __('app.products.quick_supplier_title') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{{ __('app.products.quick_supplier_name') }} <span class="danger">*</span></label>
                    <input type="text" id="quickSupplierName" class="form-control round border-primary" placeholder="{{ __('app.products.quick_supplier_name_placeholder') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('app.products.quick_supplier_phone') }} <span class="danger">*</span></label>
                    <input type="text" id="quickSupplierPhone" class="form-control round border-primary" placeholder="{{ __('app.products.quick_supplier_phone_placeholder') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">{{ __('app.products.cancel') }}</button>
                <button type="button" id="btnSaveSupplier" class="btn btn-primary round">{{ __('app.products.save_and_add') }}</button>
            </div>
        </div>
    </div>
</div>

@endpush

<style>
    .round-lg { border-radius: 15px !important; }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.3s ease; }
    .bg-glass-light { background: rgba(255, 255, 255, 0.05); }
    .border-white-10 { border: 1px solid rgba(255, 255, 255, 0.1); }
    
    input[name="product_choice_radio"]:checked + label {
        background: linear-gradient(135deg, var(--p-indigo), var(--p-purple)) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    .input-group > .select2-container--default {
        width: auto !important;
        flex: 1 1 auto;
    }
    
    html[data-textdirection="rtl"] .input-group > .select2-container--default .select2-selection--single {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    
    html[data-textdirection="ltr"] .input-group > .select2-container--default .select2-selection--single {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .form-control.round {
        border-radius: 14px;
    }

    .custom-file-label.round-lg {
        border-radius: 14px !important;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.6);
    }

    .card-body {
        padding: 2rem !important;
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }
    }
</style>
@endsection

@push('scripts')
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2-single').select2({
            width: '100%'
        });

        $('#quickPurchaseSupplier').select2({
            width: '100%',
            dropdownParent: $('#quickPurchaseModal')
        });

        $('input[name="product_choice_radio"]').on('change', function() {
            var selectedOption = $(this).val();
            if (selectedOption === 'existing') {
                $('#existing_product_form').show();
                $('#new_product_form').hide();
            } else {
                $('#existing_product_form').hide();
                $('#new_product_form').show();
            }
        });

        // Toggle sub-units fields
        $('#has_sub_units').on('change', function() {
            if ($(this).is(':checked')) {
                $('#sub_units_fields').slideDown();
            } else {
                $('#sub_units_fields').slideUp();
                $('#sub_product_id').val(null).trigger('change');
                $('#conversion_factor').val('');
            }
        });

        // Trigger change to set initial state if needed
        $('input[name="product_choice_radio"]:checked').trigger('change');

        function handleAjaxError(xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMsg = Object.values(errors).flat().join('\n');
                alert("{{ __('app.products.validation_error') }}" + errorMsg);
            } else {
                alert("{{ __('app.products.unexpected_error') }}");
            }
        }

        // Quick Add Category
        $('#btnSaveCategory').click(function() {
            var name = $('#quickCategoryName').val();
            if(!name) return alert("{{ __('app.products.enter_category_name_error') }}");
            var btn = $(this);
            btn.prop('disabled', true).text("{{ __('app.products.saving') }}");
            $.ajax({
                url: "{{ route('categories.store') }}",
                type: "POST",
                dataType: "json",
                headers: { "Accept": "application/json" },
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function(response) {
                    if(response.success) {
                        var newOption = new Option(response.category.name, response.category.id, true, true);
                        $('#category_id').append(newOption).trigger('change');
                        $('#quickCategoryModal').modal('hide');
                        $('#quickCategoryName').val('');
                        alert("{{ __('app.products.category_add_success') }}");
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                }
            });
        });

        // Quick Add Brand
        $('#btnSaveBrand').click(function() {
            var name = $('#quickBrandName').val();
            if(!name) return alert("{{ __('app.products.enter_brand_name_error') }}");
            var btn = $(this);
            btn.prop('disabled', true).text("{{ __('app.products.saving') }}");
            $.ajax({
                url: "{{ route('brands.store') }}",
                type: "POST",
                dataType: "json",
                headers: { "Accept": "application/json" },
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function(response) {
                    if(response.success) {
                        var newOption = new Option(response.brand.name, response.brand.id, true, true);
                        $('#brand_id').append(newOption).trigger('change');
                        $('#quickBrandModal').modal('hide');
                        $('#quickBrandName').val('');
                        alert("{{ __('app.products.brand_add_success') }}");
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                }
            });
        });

        // Quick Add Purchase Invoice
        $('#btnSavePurchase').click(function() {
            var number = $('#quickPurchaseNumber').val();
            var supplier = $('#quickPurchaseSupplier').val();
            if(!number) return alert("{{ __('app.products.enter_purchase_number_error') }}");
            var btn = $(this);
            btn.prop('disabled', true).text("{{ __('app.products.saving') }}");
            $.ajax({
                url: "{{ route('purchases.store') }}",
                type: "POST",
                dataType: "json",
                headers: { "Accept": "application/json" },
                data: {
                    _token: "{{ csrf_token() }}",
                    invoice_number: number,
                    supplier_id: supplier,
                    type: 'product',
                    status: 'completed',
                    date: new Date().toISOString().slice(0,10),
                    paid_amount: 0
                },
                success: function(response) {
                    if(response.success) {
                        var newOption = new Option(response.purchase.invoice_number, response.purchase.id, true, true);
                        $('#purchase_id').append(newOption).trigger('change');
                        $('#quickPurchaseModal').modal('hide');
                        $('#quickPurchaseNumber').val('');
                        alert("{{ __('app.products.purchase_add_success') }}");
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                }
            });
        });

        // Quick Add Supplier
        $('#btnSaveSupplier').click(function() {
            var name = $('#quickSupplierName').val();
            var phone = $('#quickSupplierPhone').val();
            if(!name || !phone) return alert("{{ __('app.products.enter_supplier_info_error') }}");
            var btn = $(this);
            btn.prop('disabled', true).text("{{ __('app.products.saving') }}");
            $.ajax({
                url: "{{ route('suppliers.store') }}",
                type: "POST",
                dataType: "json",
                headers: { "Accept": "application/json" },
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    phone: phone
                },
                success: function(response) {
                    if(response.success) {
                        var newOption = new Option(response.supplier.name, response.supplier.id, true, true);
                        $('#quickPurchaseSupplier').append(newOption).trigger('change');
                        $('#quickSupplierModal').modal('hide');
                        $('#quickSupplierName').val('');
                        $('#quickSupplierPhone').val('');
                        alert("{{ __('app.products.supplier_add_success') }}");
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                }
            });
        });

    });
</script>
@endpush
