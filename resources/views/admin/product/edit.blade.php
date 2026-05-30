@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.products.edit') }}: {{ $product->name }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('app.products.all_products') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.edit') }}</li>
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
    <div class="row">
        <div class="col-lg-7 col-12">
            <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
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

                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="type" class="text-bold-600">{{ __('app.products.product_type') }}</label>
                                        <select class="form-control select2-single border-primary" id="type" disabled>
                                            <option value="product" {{ $product->type == 'product' ? 'selected' : '' }}>{{ __('app.products.physical_product') }}</option>
                                            <option value="service" {{ $product->type == 'service' ? 'selected' : '' }}>{{ __('app.products.service_product') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="name" class="text-bold-600">{{ __('app.products.name') }} <span class="danger">*</span></label>
                                        <input type="text" class="form-control round border-primary" id="name" name="name" 
                                            value="{{ $product->name }}" required>
                                            
                                        <div class="custom-control custom-checkbox mt-1 weight-checkbox-container">
                                            <input type="checkbox" class="custom-control-input" id="is_weighted" name="is_weighted" value="1" {{ $product->is_weighted ? 'checked' : '' }}>
                                            <label class="custom-control-label text-bold-600 warning" for="is_weighted">{{ __('app.products.sell_by_weight') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="category_id" class="text-bold-600">{{ __('app.products.category') }} <span class="danger">*</span></label>
                                        <div class="input-group flex-nowrap">
                                            <select class="form-control select2-single border-primary" id="category_id" name="category_id" required>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
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
                                                <option value="">{{ __('app.products.no_brand') }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
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
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="cost_price" class="text-bold-600">{{ __('app.products.cost_price') }} <span class="danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control round border-primary" id="cost_price" name="cost_price" 
                                            value="{{ $product->cost_price }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="selling_price" class="text-bold-600">{{ __('app.products.selling_price') }} <span class="danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control round border-primary" id="selling_price" name="selling_price" 
                                            value="{{ $product->selling_price }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 physical-only-fields">
                                    <div class="form-group mb-2">
                                        <label for="color" class="text-bold-600">{{ __('app.products.barcode') }} <span class="danger">*</span></label>
                                        <input type="text" class="form-control round border-primary" id="color" name="color" 
                                            value="{{ $product->color }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 physical-only-fields">
                                    <div class="form-group mb-2">
                                        <label for="threshold" class="text-bold-600">{{ __('app.products.alert_quantity') }} <span class="danger">*</span></label>
                                        <input type="number" class="form-control round border-primary" id="threshold" name="threshold" 
                                            value="{{ $product->threshold }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 physical-only-fields">
                                    <div class="form-group mb-2">
                                        <label for="expiry_date" class="text-bold-600">{{ __('app.products.expiry_date') }}</label>
                                        <input type="date" class="form-control round border-primary" id="expiry_date" name="expiry_date" 
                                            value="{{ $product->expiry_date }}">
                                    </div>
                                </div>
                                <div class="col-md-6 physical-only-fields">
                                    <div class="form-group mb-2">
                                        <label for="expiry_alert_days" class="text-bold-600">{{ __('app.products.expiry_alert_days') }} <span class="danger">*</span></label>
                                        <input type="number" class="form-control round border-primary" id="expiry_alert_days" name="expiry_alert_days" 
                                            value="{{ $product->expiry_alert_days }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="image" class="text-bold-600">{{ __('app.products.image') }}</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                            <label class="custom-file-label round-lg" for="image">{{ __('app.products.update_image') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub-units Configuration Section -->
                                @php 
                                    $subUnit = $product->subUnits->first(); 
                                @endphp
                                <div class="col-md-12 mt-3 physical-only-fields">
                                    <div class="card bg-light border-warning p-3" style="border-radius: 18px; border-style: dashed !important;">
                                        <h5 class="text-bold-700 warning mb-2"><i class="la la-sitemap"></i> {{ __('app.products.sub_units_settings') }}</h5>
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="has_sub_units" name="has_sub_units" {{ $subUnit ? 'checked' : '' }}>
                                            <label class="custom-control-label text-bold-600" style="line-height: 1.8;" for="has_sub_units">{{ __('app.products.has_smaller_unit') }}</label>
                                        </div>
                                        
                                        <div id="sub_units_fields" style="display: {{ $subUnit ? 'block' : 'none' }};">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sub_product_id" class="text-bold-600">{{ __('app.products.select_sub_product') }}</label>
                                                        <select class="form-control round border-warning select2-single" id="sub_product_id" name="sub_product_id">
                                                            <option value="" disabled>{{ __('app.products.choose_sub_product') }}</option>
                                                            @foreach($all_products as $p)
                                                                <option value="{{ $p->id }}" {{ ($subUnit && $subUnit->sub_product_id == $p->id) ? 'selected' : '' }}>
                                                                    {{ $p->name }} ({{ $p->barcode }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="conversion_factor" class="text-bold-600">{{ __('app.products.conversion_factor') }}</label>
                                                        <input type="number" step="0.001" class="form-control round border-warning" id="conversion_factor" name="conversion_factor" 
                                                            value="{{ $subUnit ? $subUnit->conversion_factor : '' }}" placeholder="{{ __('app.products.example_conversion') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-warning py-1 mb-0 mt-2">
                                                <small><i class="la la-info-circle"></i> {{ __('app.products.sub_units_info') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4 text-center">
                                <button type="submit" class="btn btn-warning round px-4 shadow text-white py-1">
                                    <i class="la la-save"></i> {{ __('app.products.update') }}
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-light round px-4 ml-1">{{ __('app.products.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-12">
            <!-- Quantity Per Purchase Info -->
            <div class="card pull-up border-0 shadow-sm mb-2" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                <div class="card-header bg-transparent border-0">
                    <h4 class="card-title text-bold-700 primary"><i class="la la-info-circle"></i> {{ __('app.products.stock_details') }}</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="text-muted d-block small mb-1">{{ __('app.products.total_available_quantity') }}</span>
                            <span class="badge badge-soft-info" style="font-size: 2rem; padding: 1rem 2rem; border-radius: 20px;">{{ $totalQuantity }}</span>
                        </div>
                        
                        <h5 class="text-bold-600 mt-2 mb-1 small text-uppercase text-muted">{{ __('app.products.quantity_per_invoice') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-premium mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.products.invoice') }}</th>
                                        <th class="text-center">{{ __('app.products.quantity') }}</th>
                                        <th class="text-right">{{ __('app.products.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td><span class="text-bold-600">{{ $purchase->invoice_number }}</span></td>
                                            <td class="text-center">
                                                <span class="badge badge-soft-primary px-1">{{ $purchase->pivot->quantity }}</span>
                                            </td>
                                            <td class="text-right small">{{ number_format($purchase->total_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Image Preview -->
            @if($product->image)
                <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
                    <div class="card-body text-center">
                        <h5 class="text-bold-600 mb-2 small text-uppercase text-muted">{{ __('app.products.current_image') }}</h5>
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px; border-radius: 15px;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .round-lg { border-radius: 15px !important; }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.3s ease; }
    .bg-glass-light { background: rgba(255, 255, 255, 0.05); }
    .border-white-10 { border: 1px solid rgba(255, 255, 255, 0.1); }
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

<!-- Modals Stack -->
@push('modals')
<!-- Quick Category Modal -->
<div class="modal fade text-left" id="quickCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> إضافة قسم سريع</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>اسم القسم <span class="danger">*</span></label>
                    <input type="text" id="quickCategoryName" class="form-control round border-primary" placeholder="أدخل اسم القسم">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">إلغاء</button>
                <button type="button" id="btnSaveCategory" class="btn btn-primary round">حفظ وإضافة</button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Brand Modal -->
<div class="modal fade text-left" id="quickBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary white" style="border-radius: 15px 15px 0 0;">
                <h4 class="modal-title"><i class="la la-plus"></i> إضافة ماركة سريعة</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>اسم الماركة <span class="danger">*</span></label>
                    <input type="text" id="quickBrandName" class="form-control round border-primary" placeholder="أدخل اسم الماركة">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary round" data-dismiss="modal">إلغاء</button>
                <button type="button" id="btnSaveBrand" class="btn btn-primary round">حفظ وإضافة</button>
            </div>
        </div>
    </div>
</div>
@endpush

<style>
    .round-lg { border-radius: 15px !important; }
    .badge-soft-info { color: #0891b2; background: rgba(6, 182, 212, 0.1); border: none; }
    .badge-soft-primary { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border: none; }
    .table-premium th { font-weight: 700; color: #1e293b; border-top: none; }
    .table-premium td { vertical-align: middle; border-bottom: 1px solid #f1f5f9; padding: 0.8rem 0.5rem; }

    /* Select2 Input Group Fix */
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

    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }
    }
</style>

@push('scripts')
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2-single').select2({
            width: '100%'
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

        // Toggle fields based on product type (Product vs Service)
        $('#type').on('change', function() {
            if ($(this).val() === 'service') {
                $('.physical-only-fields').slideUp();
                $('.weight-checkbox-container').hide();
                $('#cost_price_label').html('تكلفة التشغيل <span class="danger">*</span>');
                $('#cost_price_help').text('التكلفة المقدرة لتقديم هذه الخدمة (لحساب صافي الربح)');
            } else {
                $('.physical-only-fields').slideDown();
                $('.weight-checkbox-container').show();
                $('#cost_price_label').html('سعر التكلفة (الشراء) <span class="danger">*</span>');
                $('#cost_price_help').text('تكلفة الشراء للمنتج أو تكلفة التشغيل للخدمة');
            }
        });

        // Trigger change to set initial state
        $('#type').trigger('change');

        function handleAjaxError(xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMsg = Object.values(errors).flat().join('\n');
                alert('خطأ:\n' + errorMsg);
            } else {
                alert('حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.');
            }
        }

        // Quick Add Category
        $('#btnSaveCategory').click(function() {
            var name = $('#quickCategoryName').val();
            if(!name) return alert('يرجى إدخال اسم القسم');
            var btn = $(this);
            btn.prop('disabled', true).text('جاري الحفظ...');
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
                        alert('تم إضافة القسم بنجاح');
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text('حفظ وإضافة');
                }
            });
        });

        // Quick Add Brand
        $('#btnSaveBrand').click(function() {
            var name = $('#quickBrandName').val();
            if(!name) return alert('يرجى إدخال اسم الماركة');
            var btn = $(this);
            btn.prop('disabled', true).text('جاري الحفظ...');
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
                        alert('تم إضافة الماركة بنجاح');
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    btn.prop('disabled', false).text('حفظ وإضافة');
                }
            });
        });

    });
</script>
@endpush
