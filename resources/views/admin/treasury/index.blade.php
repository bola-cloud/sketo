@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">الخزينة ليوم واحد</h1>

        <!-- Form to Select Date -->
        <form action="{{ route('treasury') }}" method="GET" class="mb-4">
            <div class="form-group row">
                <label for="date" class="col-sm-2 col-form-label">{{ __('app.treasury.select_date') }}</label>
                <div class="col-sm-4">
                    <input type="date" id="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">{{ __('app.treasury.show_treasury') }}</button>
                </div>
            </div>
        </form>

        <!-- Display Treasury Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">{{ __('app.treasury.treasury_for_date') }} {{ $date }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>{{ __('app.treasury.total_sales_installments') }} {{ number_format($salesInstallments, 2) }}
                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</h4>
                    </div>
                    <div class="col-md-6">
                        <h4>{{ __('app.treasury.total_purchase_installments') }}
                            {{ number_format($purchaseInstallments, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                        </h4>
                    </div>
                </div>

                <hr>

                <h3 class="text-center">
                    {{ __('app.treasury.difference') }}
                    <strong>{{ number_format($difference, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</strong>
                </h3>

                @if ($difference > 0)
                    <p class="text-center text-success">{{ __('app.treasury.surplus') }}</p>
                @elseif ($difference < 0)
                    <p class="text-center text-danger">{{ __('app.treasury.deficit') }}</p>
                @else
                    <p class="text-center text-warning">{{ __('app.treasury.no_difference') }}</p>
                @endif

                <hr>

                <!-- Details of Purchases (مصروفات) -->
                <h4 class="mt-4">{{ __('app.treasury.purchase_details') }}</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('app.purchases.invoice_number') }}</th>
                            <th>{{ __('app.treasury.description') }}</th>
                            <th>{{ __('app.suppliers.supplier') }}</th>
                            <th>{{ __('app.treasury.amount_paid') }}</th>
                            <th>{{ __('app.treasury.total_amount') }}</th>
                            <th>{{ __('app.treasury.date_paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseDetails as $installment)
                            <tr>
                                <td>{{ $installment->purchase->invoice_number ?? '-' }}</td>
                                <td>{{ $installment->purchase->description ?? '-' }}</td>
                                <td>{{ $installment->purchase->supplier->name ?? '-' }}</td>
                                <td>{{ number_format($installment->amount_paid, 2) }}</td>
                                <td>{{ number_format($installment->purchase->total_amount ?? 0, 2) }}</td>
                                <td>{{ $installment->date_paid }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Details of Sales (مصادر الدخل) -->
                <h4 class="mt-4">{{ __('app.treasury.sales_details') }}</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('app.purchases.invoice_number') }}</th>
                            <th>{{ __('app.clients.client_name') }}</th>
                            <th>{{ __('app.treasury.amount_paid') }}</th>
                            <th>{{ __('app.treasury.total_amount') }}</th>
                            <th>{{ __('app.treasury.date_paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesDetails as $installment)
                            <tr>
                                <td>{{ $installment->invoice->invoice_code ?? '-' }}</td>
                                <td>{{ $installment->invoice->client->name ?? '-' }}</td>
                                <td>{{ number_format($installment->amount_paid, 2) }}</td>
                                <td>{{ number_format($installment->invoice->total_amount ?? 0, 2) }}</td>
                                <td>{{ $installment->date_paid }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Supplier Returns (مردودات الموردين) -->
                <h4 class="mt-4">{{ __('app.treasury.supplier_returns_header') }}</h4>
                <h5>{{ __('app.treasury.total_supplier_returns') }} {{ number_format($supplierReturnsTotal, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('app.suppliers.supplier') }}</th>
                            <th>{{ __('app.purchases.invoice_number') }}</th>
                            <th>{{ __('app.products.product') }}</th>
                            <th>{{ __('app.supplier_returns.quantity_returned') }}</th>
                            <th>{{ __('app.treasury.unit_price') }}</th>
                            <th>{{ __('app.supplier_returns.total_value') }}</th>
                            <th>{{ __('app.supplier_returns.return_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplierReturns as $return)
                            <tr>
                                <td>{{ $return->supplier->name ?? '-' }}</td>
                                <td>{{ $return->purchase->invoice_number ?? '-' }}</td>
                                <td>{{ $return->product->name ?? '-' }}</td>
                                <td>{{ $return->quantity_returned }}</td>
                                <td>{{ number_format($return->cost_price, 2) }}</td>
                                <td>{{ number_format($return->getTotalValueAttribute(), 2) }}</td>
                                <td>{{ $return->returned_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Customer Returns (مردودات العملاء) -->
                <h4 class="mt-4">{{ __('app.treasury.customer_returns_header') }}</h4>
                <h5>{{ __('app.treasury.total_customer_returns') }} {{ number_format($customerReturnsTotal, 2) }}
                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('app.purchases.invoice_number') }}</th>
                            <th>{{ __('app.products.product') }}</th>
                            <th>{{ __('app.supplier_returns.quantity_returned') }}</th>
                            <th>{{ __('app.treasury.return_value') }}</th>
                            <th>{{ __('app.supplier_returns.return_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customerReturns as $return)
                            <tr>
                                <td>{{ $return->invoice->invoice_code ?? '-' }}</td>
                                <td>{{ $return->product->name ?? '-' }}</td>
                                <td>{{ $return->quantity_returned }}</td>
                                <td>{{ number_format($return->return_amount, 2) }}</td>
                                <td>{{ $return->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection