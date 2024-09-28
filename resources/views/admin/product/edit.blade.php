@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>تعديل المنتج</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">اسم المنتج</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label for="category_id">الفئة</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="purchase_id">فاتورة الشراء</label>
            <select class="form-control" id="purchase_id" name="purchase_id" required>
                <option>اختر الفاتورة</option>
                @foreach($purchases as $purchase)
                    <option value="{{ $purchase->id }}" 
                        @if($product->purchases->contains($purchase->id)) selected @endif>
                        {{ $purchase->invoice_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="cost_price">سعر التكلفة</label>
            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="{{ $product->cost_price }}" required>
        </div>

        <div class="form-group">
            <label for="selling_price">سعر البيع</label>
            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="{{ $product->selling_price }}" required>
        </div>

        <div class="form-group">
            <label for="color">الباركود</label>
            <input type="text" class="form-control" id="color" name="color" value="{{ $product->color }}" required>
        </div>

        <div class="form-group">
            <label for="quantity">الكمية</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $product->quantity }}" required>
        </div>

        <div class="form-group">
            <label for="threshold">الحد الأدنى للكمية</label>
            <input type="number" class="form-control" id="threshold" name="threshold" value="{{ $product->threshold }}" required>
        </div>

        <div class="form-group">
            <label for="image">صورة المنتج</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>        

        <button type="submit" class="btn btn-primary">تحديث المنتج</button>
    </form>
</div>
@endsection
