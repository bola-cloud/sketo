@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fa fa-cube text-primary"></i>
                قائمة المنتجات
                <small class="text-muted">إدارة المنتجات والمخزون</small>
            </h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-lg">
                <i class="fa fa-plus"></i> إضافة منتج جديد
            </a>
            {{-- <a href="{{ route('products.recalculateQuantities') }}" class="btn btn-warning btn-lg ml-2">
                <i class="fa fa-calculator"></i> إعادة حساب الكميات
            </a> --}}
            <!-- <button id="print-selected-barcodes" class="btn btn-secondary">طباعة الباركودات المختارة</button> -->
        </div>
    </div>

    @php
        $user = auth()->user();
        $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
    @endphp

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-filter"></i> البحث والتصفية
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search-barcode" class="font-weight-bold">
                            <i class="fa fa-search"></i> البحث عن المنتج
                        </label>
                        <input type="text" class="form-control" id="search-barcode" name="search"
                               value="{{ request('search') }}"
                               placeholder="أدخل الباركود أو اسم المنتج...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category-filter" class="font-weight-bold">
                            <i class="fa fa-tags"></i> فلترة حسب الفئة
                        </label>
                        <select class="form-control" id="category-filter" name="category_id">
                            <option value="">كل الفئات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="brand-filter" class="font-weight-bold">
                            <i class="fa fa-star"></i> فلترة حسب الماركة
                        </label>
                        <select class="form-control" id="brand-filter" name="brand_id">
                            <option value="">كل الماركات</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="button" id="search-button" class="btn btn-primary btn-block">
                            <i class="fa fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </div>

            <!-- Clear Filters Button -->
            @if(request('search') || request('category_id') || request('brand_id'))
                <div class="row mt-2">
                    <div class="col-12">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-times"></i> مسح التصفية
                        </a>
                        <small class="text-muted ml-2">
                            النتائج الحالية: {{ $products->total() }} منتج
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <form id="filter-form" method="GET" action="{{ route('products.index') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
    </form>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-list"></i> المنتجات
                <span class="badge badge-primary ml-2">{{ $products->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="products-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="40"><input type="checkbox" id="select-all"></th>
                            <th width="60">ID</th>
                            <th>اسم المنتج</th>
                            <th width="80">الصورة</th>
                            <th>الفئة</th>
                            <th>الماركة</th>
                            @if($user->hasRole('admin'))
                                <th>سعر التكلفة</th>
                            @endif
                            <th>سعر البيع</th>
                            <th width="80">الكمية</th>
                            <th>رقم الباركود</th>
                            <th width="120">صورة الباركود</th>
                            <th width="200">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <input type="radio" name="selected_product" value="{{ $product->id }}"
                                           data-barcode="{{ asset('storage/' . $product->barcode_path) }}"
                                           data-name="{{ $product->name }}">
                                </td>
                                <td><span class="badge badge-light">{{ $product->id }}</span></td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->is_transferred)
                                        <br><small class="text-info">
                                            <i class="fa fa-exchange-alt"></i> منتج منقول
                                        </small>
                                    @endif
                                    @if($product->quantity <= ($product->threshold ?? 5))
                                        <br><small class="text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> مخزون منخفض
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="img-thumbnail" width="50" height="50">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fa fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge badge-info">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">لا يوجد فئة</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->brand)
                                        <span class="badge badge-success">{{ $product->brand->name }}</span>
                                    @else
                                        <span class="text-muted">لا توجد ماركة</span>
                                    @endif
                                </td>
                                @if($user->hasRole('admin'))
                                    <td><strong>{{ number_format($product->cost_price, 2) }} ج.م</strong></td>
                                @endif
                                <td><strong class="text-success">{{ number_format($product->selling_price, 2) }} ج.م</strong></td>
                                <td class="text-center">
                                                                    @if($product->total_available_quantity > ($product->threshold ?? 5))
                                    <span class="badge badge-success">{{ $product->total_available_quantity }}</span>
                                @elseif($product->total_available_quantity > 0)
                                    <span class="badge badge-warning">{{ $product->total_available_quantity }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $product->total_available_quantity }}</span>
                                @endif
                                </td>
                                <td>
                                    @if($product->color)
                                        <span class="badge" style=" color: black !important;">
                                            {{ $product->color }}
                                        </span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($product->barcode_path)
                                        <img src="{{ asset('storage/' . $product->barcode_path) }}"
                                             alt="barcode" class="img-fluid" style="max-width: 80px;" />
                                        <br><small class="text-muted">{{ $product->barcode }}</small>
                                    @else
                                        <span class="text-muted">لا يوجد باركود</span>
                                    @endif
                                </td>
                                @if(Auth::user()->hasRole('admin') || $permissions->contains('عرض المنتجات'))
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('products.printBarcodes', $product->id) }}"
                                           class="btn btn-info btn-sm" title="طباعة الباركود">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}"
                                              method="POST" style="display:inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @else
                                <td class="text-center text-muted">
                                    <i class="fa fa-lock"></i>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <h5>لا توجد منتجات</h5>
                                        <p>لم يتم العثور على منتجات تطابق معايير البحث.</p>
                                        @if(request('search') || request('category_id') || request('brand_id'))
                                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                                <i class="fa fa-refresh"></i> عرض كل المنتجات
                                            </a>
                                        @else
                                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> إضافة أول منتج
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Section -->
    @if($products->hasPages())
        <div class="card mt-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            عرض {{ $products->firstItem() ?? 0 }} إلى {{ $products->lastItem() ?? 0 }}
                            من أصل {{ $products->total() }} منتج
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="{{ asset('assets/js/jquery.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#search-button').on('click', function() {
            $('#filter-form').submit();
        });

        $('#search-barcode').on('input', function() {
            $('input[name="search"]').val($(this).val());
        });

        $('#category-filter').on('change', function() {
            $('input[name="category_id"]').val($(this).val());
        });

        $('#brand-filter').on('change', function() {
            $('input[name="brand_id"]').val($(this).val());
        });

        // Auto-submit on filter change for better UX
        $('#category-filter, #brand-filter').on('change', function() {
            $('#filter-form').submit();
        });

        // Allow Enter key to trigger search
        $('#search-barcode').on('keypress', function(e) {
            if (e.which === 13) {
                $('#filter-form').submit();
            }
        });
    });
</script>

@endsection
