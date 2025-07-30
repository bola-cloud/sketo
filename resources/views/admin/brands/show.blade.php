@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>تفاصيل الماركة: {{ $brand->name }}</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">معلومات الماركة</h5>
                    <p><strong>الاسم:</strong> {{ $brand->name }}</p>
                    <p><strong>الوصف:</strong> {{ $brand->description ?: 'لا يوجد وصف' }}</p>
                    <p><strong>عدد المنتجات:</strong> {{ $brand->products->count() }} منتج</p>
                    <p><strong>تاريخ الإنشاء:</strong> {{ $brand->created_at->format('Y-m-d H:i') }}</p>

                    <div class="mt-3">
                        <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-warning">تعديل الماركة</a>
                        <a href="{{ route('brands.index') }}" class="btn btn-secondary">العودة للقائمة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2>المنتجات التابعة لهذه الماركة</h2>

    @if($products->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>اسم المنتج</th>
                    <th>الفئة</th>
                    <th>سعر الشراء</th>
                    <th>سعر البيع</th>
                    <th>الكمية المتاحة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category ? $product->category->name : 'غير محدد' }}</td>
                        <td>{{ number_format($product->cost_price, 2) }} ج.م</td>
                        <td>{{ number_format($product->selling_price, 2) }} ج.م</td>
                        <td>{{ $product->quantity }}</td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm">عرض</a>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info">
            لا توجد منتجات مرتبطة بهذه الماركة حالياً.
        </div>
    @endif
</div>
@endsection
