@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.invoices.details') }}</h1>

        @php
            $user = auth()->user();
            $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
        @endphp

        <!-- Invoice Details -->
        <div class="card mt-4">
            <div class="card-body">
                <h5>{{ __('app.invoices.invoice_code') }}: {{ $invoice->invoice_code }}</h5>
                <p>{{ __('app.invoices.buyer_name') }}: {{ $invoice->buyer_name }}</p>
                <p>{{ __('app.invoices.buyer_phone') }}: {{ $invoice->buyer_phone }}</p>
                <p>{{ __('app.invoices.seller') }}: {{ $invoice->user->name }}</p>
                <p>{{ __('app.invoices.subtotal') }}: {{ number_format($invoice->subtotal, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                <p>{{ __('app.invoices.discount') }}: {{ number_format($invoice->discount, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                <p>{{ __('app.invoices.total') }}: {{ number_format($invoice->total_amount, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                <p>{{ __('app.invoices.paid_amount') }}: {{ number_format($invoice->paid_amount, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                <p>
                    @if($invoice->change < 0)
                        {{ __('app.invoices.remaining_amount') }}: {{ number_format(abs($invoice->change), 2) }}
                        {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                    @elseif($invoice->change > 0)
                        {{ __('app.invoices.change_amount') }}: {{ number_format($invoice->change, 2) }}
                        {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                    @else
                        {{ __('app.invoices.fully_paid') }}
                    @endif
                </p>
                <p>{{ __('app.invoices.created_at') }}: {{ $invoice->created_at->format('Y-m-d H:i') }}</p>

                @php
                    $totalReturns = $invoice->returns()->sum('return_amount');
                    $returnsCount = $invoice->returns()->count();
                @endphp

                @if($returnsCount > 0)
                    <div class="alert alert-info mt-2">
                        <i class="fa fa-info-circle"></i>
                        <strong>{{ __('app.common.notice') }}:</strong>
                        {{ __('app.invoices.return_alert', ['count' => $returnsCount, 'amount' => number_format($totalReturns, 2)]) }}
                        {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                        <a href="{{ route('customer-returns.index') }}?invoice_code={{ $invoice->invoice_code }}"
                            class="btn btn-sm btn-info ml-2">
                            {{ __('app.invoices.view_returns') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Update Paid Amount and Change Form -->
        <div class="container mt-4">
            <h2>{{ __('app.invoices.update_payment') }}</h2>

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
                    <input type="number" class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount"
                        name="paid_amount" placeholder="أدخل المبلغ المدفوع" step="0.01" min="0"
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
                    <input type="text" class="form-control" id="change" name="change"
                        value="{{ old('change', $invoice->change) }}" readonly>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">تحديث الفاتورة</button>
            </form> --}}

            @if($invoice->total_amount > $invoice->paid_amount)
                <a href="{{ route('sales.installments.index', $invoice->id) }}"
                    class="btn btn-info btn-sm">{{ __('app.invoices.view_installments') }}</a> <!-- Link to Installments -->
            @endif
        </div>

        <!-- Display the discount and provide an option to edit it -->
        <form action="{{ route('invoices.updateDiscount', $invoice->id) }}" method="POST" class="p-2 mt-2">
            @csrf
            @method('PUT') <!-- PUT method for updating -->
            <div class="form-group">
                <label for="discount">{{ __('app.invoices.edit_discount') }}</label>
                <input type="number" class="form-control" id="discount" name="discount"
                    value="{{ number_format($invoice->discount, 2) }}" step="0.01" required>
            </div>

            <button type="submit" class="btn btn-warning">{{ __('app.invoices.update_discount') }}</button>
        </form>



        <!-- Sold Products Section -->
        <h2 class="mt-4">{{ __('app.invoices.sold_products') }}</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('app.invoices.product') }}</th>
                    <th>{{ __('app.invoices.sold_quantity') }}</th>
                    <th>{{ __('app.invoices.price') }}</th>
                    <th>{{ __('app.invoices.total_price') }}</th>
                    @if($invoice->returns()->count() > 0)
                        <th>{{ __('app.invoices.returned_quantity') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->sales as $sale)
                    @php
                        $returnedQuantity = $invoice->returns()->where('product_id', $sale->product_id)->sum('quantity_returned');
                    @endphp
                    <tr>
                        <td>
                            {{ $sale->product->name }}
                            @if($returnedQuantity > 0)
                                <span class="badge badge-warning">{{ __('app.invoices.has_returns') }}</span>
                            @endif
                        </td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->product->selling_price, 2) }}</td>
                        <td>{{ number_format($sale->total_price, 2) }}</td>
                        @if($invoice->returns()->count() > 0)
                            <td>
                                @if($returnedQuantity > 0)
                                    <span class="text-danger">{{ $returnedQuantity }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="{{ $invoice->returns()->count() > 0 ? '4' : '3' }}" class="text-right">
                        {{ __('app.invoices.subtotal') }}:</th>
                    <th>{{ number_format($invoice->subtotal, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
                </tr>
                <tr>
                    <th colspan="{{ $invoice->returns()->count() > 0 ? '4' : '3' }}" class="text-right">
                        {{ __('app.invoices.discount') }}:</th>
                    <th>{{ number_format($invoice->discount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
                </tr>
                @if($invoice->returns()->count() > 0)
                    <tr>
                        <th colspan="4" class="text-right">{{ __('app.invoices.total_returns') }}:</th>
                        <th class="text-danger">-{{ number_format($invoice->returns()->sum('return_amount'), 2) }}
                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
                    </tr>
                @endif
                <tr>
                    <th colspan="{{ $invoice->returns()->count() > 0 ? '4' : '3' }}" class="text-right">
                        {{ __('app.invoices.total') }}:</th>
                    <th>{{ number_format($invoice->total_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
                </tr>
                <tr>
                    <th colspan="{{ $invoice->returns()->count() > 0 ? '4' : '3' }}" class="text-right">
                        {{ __('app.invoices.paid_amount') }}:</th>
                    <th>{{ number_format($invoice->paid_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</th>
                </tr>
                <tr>
                    <th colspan="{{ $invoice->returns()->count() > 0 ? '4' : '3' }}" class="text-right">
                        @if($invoice->change < 0)
                            {{ __('app.invoices.remaining_amount') }}:
                        @elseif($invoice->change > 0)
                            {{ __('app.invoices.change_amount') }}:
                        @else
                            {{ __('app.invoices.status') }}:
                        @endif
                    </th>
                    <th>
                        @if($invoice->change < 0)
                            <span class="text-danger">{{ number_format(abs($invoice->change), 2) }}
                                {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span>
                        @elseif($invoice->change > 0)
                            <span class="text-success">{{ number_format($invoice->change, 2) }}
                                {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span>
                        @else
                            <span class="text-success">{{ __('app.invoices.fully_paid') }}</span>
                        @endif
                    </th>
                </tr>
            </tfoot>
        </table>

        <!-- Customer Returns Section -->
        @if($user->hasRole('admin') || $permissions->contains('إدارة الفواتير'))
            <div class="mt-3 mb-3">
                <a href="{{ route('customer-returns.createForInvoice', $invoice) }}" class="btn btn-primary">
                    <i class="fa fa-undo"></i> {{ __('app.invoices.create_return') }}
                </a>
                <small class="text-muted d-block mt-1">
                    {{ __('app.invoices.return_hint') }}
                </small>
            </div>
        @endif

        <!-- Add Product to Invoice -->
        @if($user->hasRole('admin') || $permissions->contains('حذف الفواتير'))
            <h3 class="mt-4">{{ __('app.invoices.add_product') }}</h3>
            <form action="{{ route('invoices.addProduct', $invoice->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="product_id">{{ __('app.invoices.select_product') }}</label>
                    <select name="product_id" id="product_id" class="form-control" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} -
                                {{ number_format($product->selling_price, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">{{ __('app.invoices.quantity') }}</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('app.invoices.add_btn') }}</button>
            </form>

            <!-- Delete Invoice -->
            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('{{ __('app.invoices.delete_confirm') }}')">{{ __('app.common.delete') }}</button>
            </form>
        @endif
    </div>
@endsection