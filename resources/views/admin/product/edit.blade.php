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
            <label for="threshold">الحد الأدنى للكمية</label>
            <input type="number" class="form-control" id="threshold" name="threshold" value="{{ $product->threshold }}" required>
        </div>

        <div class="form-group">
            <label for="image">صورة المنتج</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>        

        <!-- Display and update purchase quantities -->
        <h3>الكميات لكل فاتورة</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>الكمية</th>
                    <th>إجمالي الفاتورة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->invoice_number }}</td>
                        <td>
                            <input type="number" name="purchase_quantities[{{ $purchase->id }}]" class="form-control" value="{{ $purchase->pivot->quantity }}">
                        </td>
                        <td>{{ $purchase->total_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Display the total quantity (read-only) -->
        <div class="form-group">
            <label for="total_quantity">إجمالي الكمية</label>
            <input type="number" class="form-control" id="total_quantity" name="total_quantity" value="{{ $totalQuantity }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary">تحديث المنتج</button>
    </form>
</div>
@endsection
