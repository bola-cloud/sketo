@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>تفاصيل الفاتورة</h1>
    
    @php
        $user = auth()->user();
        $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
    @endphp

    <!-- Invoice Details -->
    <div class="card mt-4">
        <div class="card-body">
            <h5>كود الفاتورة: {{ $invoice->invoice_code }}</h5>
            <p>اسم المشتري: {{ $invoice->buyer_name }}</p>
            <p>هاتف المشتري: {{ $invoice->buyer_phone }}</p>
            <p>اسم البائع: {{ $invoice->user->name }}</p>
            <p>الإجمالي الفرعي: {{ number_format($invoice->subtotal, 2) }} ج.م</p>
            <p>الخصم: {{ number_format($invoice->discount, 2) }} ج.م</p>
            <p>الإجمالي: {{ number_format($invoice->total_amount, 2) }} ج.م</p>
            <p>المبلغ المدفوع: {{ number_format($invoice->paid_amount, 2) }} ج.م</p>
            <p>التغيير (المبلغ المتبقي/الزائد): {{ number_format($invoice->change, 2) }} ج.م</p>
            <p>تاريخ الإنشاء: {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <!-- Update Paid Amount and Change Form -->
    <div class="container mt-4">
        <h2>تحديث المبلغ المدفوع والاقساط </h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- <form action="{{ route('invoices.updatePayment', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- PUT method for updating -->

            <!-- Paid Amount -->
            <div class="form-group">
                <label for="paid_amount">المبلغ المدفوع</label>
                <input type="number" 
                       class="form-control @error('paid_amount') is-invalid @enderror" 
                       id="paid_amount" 
                       name="paid_amount" 
                       placeholder="أدخل المبلغ المدفوع"
                       step="0.01"
                       min="0"
                       value="{{ old('paid_amount', $invoice->paid_amount) }}" required>
                @error('paid_amount')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Change (read-only, calculated automatically) -->
            <div class="form-group">
                <label for="change">التغيير (المبلغ المتبقي/الزائد)</label>
                <input type="text" 
                       class="form-control" 
                       id="change" 
                       name="change" 
                       value="{{ old('change', $invoice->change) }}" readonly>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">تحديث الفاتورة</button>
        </form> --}}

        @if($invoice->total_amount > $invoice->paid_amount)
            <a href="{{ route('sales.installments.index', $invoice->id) }}" class="btn btn-info btn-sm">عرض الأقساط</a> <!-- Link to Installments -->
        @endif
    </div>

    <!-- Display the discount and provide an option to edit it -->
    <form action="{{ route('invoices.updateDiscount', $invoice->id) }}" method="POST" class="p-2 mt-2">
        @csrf
        @method('PUT') <!-- PUT method for updating -->
        <div class="form-group">
            <label for="discount">تعديل الخصم:</label>
            <input type="number" class="form-control" id="discount" name="discount" value="{{ number_format($invoice->discount, 2) }}" step="0.01" required>
        </div>
        
        <button type="submit" class="btn btn-warning">تحديث الخصم</button>
    </form>



    <!-- Sold Products Section -->
    <h2 class="mt-4">المنتجات المباعة</h2>
    <form action="{{ route('invoices.returnProducts', $invoice->id) }}" method="POST">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية المباعة</th>
                    <th>الكمية المراد إرجاعها</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->sales as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>
                            <input type="number" name="sales[{{ $sale->id }}]" value="0" min="0" max="{{ $sale->quantity }}" class="form-control">
                        </td>
                        <td>{{ number_format($sale->product->selling_price, 2) }}</td>
                        <td>{{ number_format($sale->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">الإجمالي الفرعي:</th>
                    <th>{{ number_format($invoice->subtotal, 2) }} ج.م</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">الخصم:</th>
                    <th>{{ number_format($invoice->discount, 2) }} ج.م</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">الإجمالي:</th>
                    <th>{{ number_format($invoice->total_amount, 2) }} ج.م</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">المبلغ المدفوع:</th>
                    <th>{{ number_format($invoice->paid_amount, 2) }} ج.م</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">التغيير:</th>
                    <th>{{ number_format($invoice->change, 2) }} ج.م</th>
                </tr>
            </tfoot>
        </table>
        @if($user->hasRole('admin') || $permissions->contains('حذف الفواتير'))
            <button type="submit" class="btn btn-danger">إرجاع المنتجات المختارة</button>
        @endif
    </form>

    <!-- Add Product to Invoice -->
    @if($user->hasRole('admin') || $permissions->contains('حذف الفواتير'))
        <h3 class="mt-4">إضافة منتج جديد إلى الفاتورة</h3>
        <form action="{{ route('invoices.addProduct', $invoice->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="product_id">اختر المنتج</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - {{ number_format($product->selling_price, 2) }} ج.م</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">الكمية</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">إضافة المنتج</button>
        </form>

        <!-- Delete Invoice -->
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف الفاتورة؟ سيتم إرجاع الكميات إلى المخزون.')">حذف الفاتورة</button>
        </form>
    @endif
</div>
@endsection
