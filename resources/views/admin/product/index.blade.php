@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">إدارة المنتجات والمخزون</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">المنتجات</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                <a href="{{ route('products.export') }}" class="btn btn-success round px-2 shadow mr-1">
                    <i class="la la-file-excel-o"></i> تصدير إكسل
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-primary round px-2 shadow">
                    <i class="la la-plus"></i> إضافة منتج جديد
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        @php
            $user = auth()->user();
            $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
        @endphp

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>تم بنجاح!</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>خطأ!</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Filters Section -->
        <div class="card pull-up border-0 shadow-sm mb-4"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-content">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="text-muted small">البحث عن المنتج</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent border-primary border-right-0"
                                            id="basic-addon1">
                                            <i class="la la-search primary"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control round border-primary border-left-0"
                                        id="search-barcode" value="{{ request('search') }}"
                                        placeholder="الباركود أو اسم المنتج...">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="text-muted small">الفئة</label>
                                <select class="form-control round border-primary" id="category-filter">
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
                            <div class="form-group mb-0">
                                <label class="text-muted small">الماركة</label>
                                <select class="form-control round border-primary" id="brand-filter">
                                    <option value="">كل الماركات</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="button" id="search-button" class="btn btn-primary btn-block round shadow">
                                <i class="la la-filter"></i> تصفية
                            </button>
                        </div>
                    </div>

                    @if(request('search') || request('category_id') || request('brand_id'))
                        <div class="mt-2">
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-soft-danger round">
                                <i class="la la-times"></i> مسح التصفية
                            </a>
                            <span class="text-muted small ml-1">نتائج البحث: {{ $products->total() }} منتج</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form id="filter-form" method="GET" action="{{ route('products.index') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
        </form>

        <!-- Products Table -->
        <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0" id="products-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th> المنتج</th>
                                    <th>الفئة/الماركة</th>
                                    @if($user->hasRole('admin'))
                                        <th>سعر التكلفة</th>
                                    @endif
                                    <th>سعر البيع</th>
                                    <th>الكمية</th>
                                    <th>الباركود</th>
                                    <th class="text-right">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td><span class="badge badge-soft-secondary">{{ $product->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="avatar-lg mr-2"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                                                @else
                                                    <div class="avatar bg-soft-primary mr-2"
                                                        style="width: 50px; height: 50px; border-radius: 10px;">
                                                        <i class="la la-image primary" style="font-size: 24px;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-bold-700 block">{{ $product->name }}</span>
                                                    @if($product->quantity <= ($product->threshold ?? 5))
                                                        <span class="text-danger small"><i class="la la-exclamation-triangle"></i>
                                                            مخزون منخفض</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <span class="text-bold-600">الفئة:</span>
                                                {{ $product->category->name ?? 'غير محدد' }}<br>
                                                <span class="text-bold-600">الماركة:</span>
                                                {{ $product->brand->name ?? 'غير محدد' }}
                                            </div>
                                        </td>
                                        @if($user->hasRole('admin'))
                                            <td class="text-bold-600">{{ number_format($product->cost_price, 2) }} ج.م</td>
                                        @endif
                                        <td class="text-bold-700 primary">{{ number_format($product->selling_price, 2) }} ج.م
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $qty = $product->total_available_quantity;
                                                $badgeClass = $qty > ($product->threshold ?? 5) ? 'badge-soft-success' : ($qty > 0 ? 'badge-soft-warning' : 'badge-soft-danger');
                                            @endphp
                                            <span class="badge {{ $badgeClass }}"
                                                style="font-size: 1rem; padding: 0.5rem 0.8rem;">{{ $qty }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($product->barcode_path)
                                                <div class="barcode-container py-1">
                                                    <img src="{{ asset('storage/' . $product->barcode_path) }}"
                                                        alt="{{ $product->barcode }}"
                                                        class="d-block mx-auto mb-1" 
                                                        style="max-height: 30px; max-width: 140px; filter: grayscale(1); opacity: 0.8;">
                                                    <span class="badge badge-light text-muted" style="font-family: monospace; letter-spacing: 1px;">{{ $product->barcode }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted small">لا يوجد</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if(Auth::user()->hasRole('admin') || $permissions->contains('عرض المنتجات'))
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    class="btn btn-sm btn-soft-warning mr-1">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a href="{{ route('products.printBarcodes', $product->id) }}"
                                                    class="btn btn-sm btn-soft-info mr-1">
                                                    <i class="la la-print"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <i class="la la-lock text-muted"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="la la-box font-large-3 text-muted mb-2"></i>
                                            <h5 class="text-muted">لم يتم العثور على منتجات</h5>
                                            <a href="{{ route('products.create') }}" class="btn btn-primary round mt-1">إضافة
                                                أول منتج</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background: rgba(59, 130, 246, 0.1);
        }

        .badge-soft-success {
            color: #16a34a;
            background: rgba(22, 163, 74, 0.1);
            border: none;
        }

        .badge-soft-warning {
            color: #d97706;
            background: rgba(217, 119, 6, 0.1);
            border: none;
        }

        .badge-soft-danger {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
        }

        .badge-soft-secondary {
            color: #64748b;
            background: rgba(100, 116, 139, 0.1);
            border: none;
        }

        .btn-soft-warning {
            color: #d97706;
            background: rgba(217, 119, 6, 0.1);
            border: none;
        }

        .btn-soft-warning:hover {
            background: #d97706;
            color: #fff;
        }

        .btn-soft-danger {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
        }

        .btn-soft-danger:hover {
            background: #ef4444;
            color: #fff;
        }

        .btn-soft-info {
            color: #0891b2;
            background: rgba(6, 182, 212, 0.1);
            border: none;
        }

        .btn-soft-info:hover {
            background: #0891b2;
            color: #fff;
        }

        .table-premium th {
            font-weight: 700;
            color: #1e293b;
            border-top: none;
            padding: 1.25rem 1rem;
        }

        .table-premium td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem;
        }
    </style>

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#search-button').on('click', function () {
                $('#filter-form').submit();
            });

            $('#search-barcode').on('input', function () {
                $('input[name="search"]').val($(this).val());
            });

            $('#category-filter').on('change', function () {
                $('input[name="category_id"]').val($(this).val());
                $('#filter-form').submit();
            });

            $('#brand-filter').on('change', function () {
                $('input[name="brand_id"]').val($(this).val());
                $('#filter-form').submit();
            });

            $('#search-barcode').on('keypress', function (e) {
                if (e.which === 13) {
                    $('#filter-form').submit();
                }
            });
        });
    </script>
@endsection