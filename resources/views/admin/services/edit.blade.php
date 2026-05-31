@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">{{ __('app.sidebar.edit_service') ?? 'تعديل الخدمة' }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('app.sidebar.services') ?? 'الخدمات' }}</a></li>
                    <li class="breadcrumb-item active">{{ __('app.common.edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 d-flex justify-content-end align-items-center">
        <a href="{{ route('services.index') }}" class="btn btn-secondary round px-3 shadow-sm">
            <i class="la la-arrow-left"></i> {{ __('app.common.back') ?? 'Back to List' }}
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="card premium-card border-0 shadow-lg mb-4 animate-fade-in-up" style="border-radius: 28px; width: 100%;">
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

                    @if(session('error'))
                        <div class="alert alert-danger mb-2"><strong>{{ __('app.common.error') }}!</strong> {{ session('error') }}</div>
                    @endif

                    <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label for="name" class="text-bold-600">{{ __('app.products.name') }} <span class="danger">*</span></label>
                                    <input type="text" class="form-control round border-primary" id="name" name="name" 
                                        value="{{ old('name', $service->name) }}" placeholder="{{ __('app.products.enter_name') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label for="category_id" class="text-bold-600">{{ __('app.products.category') }} <span class="danger">*</span></label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-control select2-single border-primary" id="category_id" name="category_id" required>
                                            <option value="" disabled>{{ __('app.products.select_category') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
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

                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="cost_price" class="text-bold-600">{{ __('app.products.cost_price') }} <span class="danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control round border-primary" id="cost_price" name="cost_price" 
                                            value="{{ old('cost_price', $service->cost_price) }}" placeholder="0.00" required>
                                        <div class="input-group-append"><span class="input-group-text bg-transparent border-0">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="selling_price" class="text-bold-600">{{ __('app.products.selling_price') }} <span class="danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control round border-primary" id="selling_price" name="selling_price" 
                                            value="{{ old('selling_price', $service->selling_price) }}" placeholder="0.00" required>
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
                                            value="{{ old('color', $service->color ?? $service->barcode) }}" placeholder="{{ __('app.products.search_placeholder') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-2">
                                    <label for="image" class="text-bold-600">{{ __('app.products.image') }}</label>
                                    @if($service->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $service->image) }}" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    @endif
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
                            <a href="{{ route('services.index') }}" class="btn btn-light round px-4 ml-1">{{ __('app.products.cancel') }}</a>
                        </div>
                    </form>
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
@endpush

<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script>
    $(document).ready(function () {
        // Handle custom file input label change
        $('#image').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || "{{ __('app.products.choose_image') }}");
        });

        // Quick Save Category AJAX
        $('#btnSaveCategory').on('click', function() {
            let name = $('#quickCategoryName').val().trim();
            if(!name) {
                alert("{{ __('app.products.enter_category_name_error') }}");
                return;
            }
            
            let btn = $(this);
            btn.prop('disabled', true).text("{{ __('app.products.saving') }}");
            
            $.ajax({
                url: "{{ route('categories.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    ajax: true
                },
                success: function(response) {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                    $('#quickCategoryModal').modal('hide');
                    $('#quickCategoryName').val('');
                    
                    // Add new option and select it
                    let newOption = new Option(response.category.name, response.category.id, true, true);
                    $('#category_id').append(newOption).trigger('change');
                    alert("{{ __('app.products.category_add_success') }}");
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text("{{ __('app.products.save_and_add') }}");
                    let errorMsg = "{{ __('app.products.unexpected_error') }}";
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert(errorMsg);
                }
            });
        });
    });
</script>
@endsection
