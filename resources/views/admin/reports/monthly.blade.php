@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow p-4">
            <h1 class="text-center mb-4">{{ __('app.reports.title_monthly') }}</h1>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('app.reports.total_quantity_sold') }}</h5>
                            <p class="card-text">{{ $totalQuantity }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('app.reports.total_revenue') }}</h5>
                            <p class="card-text">{{ number_format($totalRevenue, 2) }}
                                {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('app.reports.total_profit') }}</h5>
                            <p class="card-text">{{ number_format($totalProfit, 2) }}
                                {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                    <tr>
                        <th>{{ __('app.reports.invoice_code') }}</th>
                        <th>{{ __('app.reports.paid_amount') }}</th>
                        <th>{{ __('app.reports.total_after_discount') }}</th>
                        <th>{{ __('app.reports.change_remaining') }}</th>
                        <th>{{ __('app.reports.sales_details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_code }}</td>
                            <td>{{ number_format($invoice->paid_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                            </td>
                            <td>{{ number_format($invoice->total_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                            </td>
                            <td>{{ number_format($invoice->change, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                            <td>
                                <ul>
                                    @foreach($invoice->sales as $sale)
                                        <li>{{ $sale->product->name }} - {{ __('app.cashier.quantity') }}: {{ $sale->quantity }} -
                                            {{ __('app.cashier.price') }}: {{ number_format($sale->product->selling_price, 2) }}
                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection