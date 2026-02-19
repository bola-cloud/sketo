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
                            <div class="d-flex gap-2 p-1 bg-glass-light border-white-10" style="border-radius: 18px;">
                                <div class="custom-control custom-radio custom-control-inline flex-grow-1 text-center m-0">
                                    <input type="radio" id="choice_new" name="product_choice_radio" value="new" class="custom-control-input" checked>
                                    <label class="custom-control-label w-100 p-1 round-lg cursor-pointer transition-all" for="choice_new">{{ __('app.products.new_product') }}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline flex-grow-1 text-center m-0">
                                    <input type="radio" id="choice_existing" name="product_choice_radio" value="existing" class="custom-control-input">
                                    <label class="custom-control-label w-100 p-1 round-lg cursor-pointer transition-all" for="choice_existing">{{ __('app.products.existing_product') }}</label>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="name" class="text-bold-600">{{ __('app.products.name') }} <span class="danger">*</span></label>
                                            <input type="text" class="form-control round border-primary" id="name" name="name" 
                                                value="{{ old('name') }}" placeholder="{{ __('app.products.enter_name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="category_id" class="text-bold-600">{{ __('app.products.category') }} <span class="danger">*</span></label>
                                            <select class="form-control select2-single border-primary" id="category_id" name="category_id" required>
                                                <option value="" disabled selected>{{ __('app.products.select_category') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="brand_id" class="text-bold-600">{{ __('app.products.brand') }}</label>
                                            <select class="form-control select2-single border-primary" id="brand_id" name="brand_id">
                                                <option value="" disabled selected>{{ __('app.products.select_brand') }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="purchase_id" class="text-bold-600">{{ __('app.products.purchase_invoice') }} <span class="danger">*</span></label>
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
                                    <div class="col-md-12">
                                        <div class="form-group mb-2">
                                            <label for="color" class="text-bold-600">{{ __('app.products.barcode') }} <span class="danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text bg-transparent border-0"><i class="la la-barcode"></i></span></div>
                                                <input type="text" class="form-control round border-primary" id="color" name="color" 
                                                    value="{{ old('color') }}" placeholder="{{ __('app.products.search_placeholder') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="quantity" class="text-bold-600">{{ __('app.products.initial_quantity') }} <span class="danger">*</span></label>
                                            <input type="number" class="form-control round border-primary" id="quantity" name="quantity" 
                                                value="{{ old('quantity') }}" placeholder="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="threshold" class="text-bold-600">{{ __('app.products.alert_quantity') }} <span class="danger">*</span></label>
                                            <input type="number" class="form-control round border-primary" id="threshold" name="threshold" 
                                                value="{{ old('threshold', 5) }}" placeholder="5" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="expiry_date" class="text-bold-600">{{ __('app.products.expiry_date') }}</label>
                                            <input type="date" class="form-control round border-primary" id="expiry_date" name="expiry_date" 
                                                value="{{ old('expiry_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
    
    .select2-container--default .select2-selection--single {
        border-radius: 14px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        height: 45px !important;
        color: white !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 45px !important;
        color: white !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important;
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

        // Trigger change to set initial state if needed
        $('input[name="product_choice_radio"]:checked').trigger('change');
    });
</script>
@endpush
