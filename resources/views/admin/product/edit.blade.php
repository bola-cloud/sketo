@extends('layouts.admin')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title">تعديل المنتج: {{ $product->name }}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </div>
        </div>
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
                                    <div class="form-group mb-2">
                                        <label for="name" class="text-bold-600">اسم المنتج <span class="danger">*</span></label>
                                        <input type="text" class="form-control round border-primary" id="name" name="name" 
                                            value="{{ $product->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="category_id" class="text-bold-600">الفئة <span class="danger">*</span></label>
                                        <select class="form-control round border-primary" id="category_id" name="category_id" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="brand_id" class="text-bold-600">الماركة</label>
                                        <select class="form-control round border-primary" id="brand_id" name="brand_id">
                                            <option value="">بدون ماركة</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="cost_price" class="text-bold-600">سعر التكلفة <span class="danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control round border-primary" id="cost_price" name="cost_price" 
                                            value="{{ $product->cost_price }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="selling_price" class="text-bold-600">سعر البيع <span class="danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control round border-primary" id="selling_price" name="selling_price" 
                                            value="{{ $product->selling_price }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="color" class="text-bold-600">الباركود <span class="danger">*</span></label>
                                        <input type="text" class="form-control round border-primary" id="color" name="color" 
                                            value="{{ $product->color }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label for="threshold" class="text-bold-600">حد التنبيه <span class="danger">*</span></label>
                                        <input type="number" class="form-control round border-primary" id="threshold" name="threshold" 
                                            value="{{ $product->threshold }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="image" class="text-bold-600">صورة المنتج</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                            <label class="custom-file-label round-lg" for="image">تحديث الصورة...</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4 text-center">
                                <button type="submit" class="btn btn-warning round px-4 shadow text-white py-1">
                                    <i class="la la-save"></i> تحديث المنتج
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-light round px-4 ml-1">إلغاء</a>
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
                    <h4 class="card-title text-bold-700 primary"><i class="la la-info-circle"></i> تفاصيل المخزون</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="text-muted d-block small mb-1">إجمالي الكمية المتاحة</span>
                            <span class="badge badge-soft-info" style="font-size: 2rem; padding: 1rem 2rem; border-radius: 20px;">{{ $totalQuantity }}</span>
                        </div>
                        
                        <h5 class="text-bold-600 mt-2 mb-1 small text-uppercase text-muted">الكميات لكل فاتورة</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-premium mb-0">
                                <thead>
                                    <tr>
                                        <th>الفاتورة</th>
                                        <th class="text-center">الكمية</th>
                                        <th class="text-right">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td><span class="text-bold-600">{{ $purchase->invoice_number }}</span></td>
                                            <td class="text-center">
                                                <span class="badge badge-soft-primary px-1">{{ $purchase->pivot->quantity }}</span>
                                            </td>
                                            <td class="text-right small">{{ number_format($purchase->total_amount, 2) }} ج.م</td>
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
                        <h5 class="text-bold-600 mb-2 small text-uppercase text-muted">الصورة الحالية</h5>
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px; border-radius: 15px;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .round-lg { border-radius: 15px !important; }
    .badge-soft-info { color: #0891b2; background: rgba(6, 182, 212, 0.1); border: none; }
    .badge-soft-primary { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border: none; }
    .table-premium th { font-weight: 700; color: #1e293b; border-top: none; }
    .table-premium td { vertical-align: middle; border-bottom: 1px solid #f1f5f9; padding: 0.8rem 0.5rem; }
</style>
@endsection
