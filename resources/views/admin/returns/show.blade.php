@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('app.returns.show_title') }}: {{ $customerReturn->id }}</h4>
                    <div class="heading-elements">
                        <a href="{{ route('customer-returns.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-right"></i> {{ __('app.common.back_to_list') }}
                        </a>
                        <a href="{{ route('customer-returns.edit', $customerReturn) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> {{ __('app.common.edit') }}
                        </a>
                    </div>
                </div>

                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <!-- Return Details -->
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>{{ __('app.returns.return_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('app.returns.return_id') }}:</strong></td>
                                                <td>{{ $customerReturn->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.products.product') }}:</strong></td>
                                                <td>{{ $customerReturn->product->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.returns.qty_returned') }}:</strong></td>
                                                <td>{{ $customerReturn->quantity_returned }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.returns.return_amount') }}:</strong></td>
                                                <td>{{ number_format($customerReturn->return_amount, 2) }}
                                                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.returns.return_reason') }}:</strong></td>
                                                <td>{{ $customerReturn->reason }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.common.status') }}:</strong></td>
                                                <td>
                                                    @if($customerReturn->status == 'pending')
                                                        <span
                                                            class="badge badge-warning">{{ __('app.common.status_pending') }}</span>
                                                    @elseif($customerReturn->status == 'completed')
                                                        <span
                                                            class="badge badge-success">{{ __('app.common.status_completed') }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-danger">{{ __('app.common.status_cancelled') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.users.user') }}:</strong></td>
                                                <td>{{ $customerReturn->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.returns.return_date') }}:</strong></td>
                                                <td>{{ $customerReturn->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Details -->
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>{{ __('app.returns.original_invoice_details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('app.reports.invoice_code') }}:</strong></td>
                                                <td>
                                                    <a href="{{ route('invoices.show', $customerReturn->invoice) }}">
                                                        {{ $customerReturn->invoice->invoice_code }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.clients.name') }}:</strong></td>
                                                <td>{{ $customerReturn->invoice->buyer_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.clients.phone') }}:</strong></td>
                                                <td>{{ $customerReturn->invoice->buyer_phone ?? __('app.common.not_specified') }}
                                                </td>
                                            </tr>
                                            @if($customerReturn->invoice->client)
                                                <tr>
                                                    <td><strong>{{ __('app.returns.client_data') }}</strong></td>
                                                    <td>{{ $customerReturn->invoice->client->name }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>{{ __('app.returns.invoice_date') }}:</strong></td>
                                                <td>{{ $customerReturn->invoice->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('app.returns.invoice_total') }}:</strong></td>
                                                <td>{{ number_format($customerReturn->invoice->total_amount, 2) }}
                                                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>{{ __('app.products.details') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>{{ __('app.products.name') }}:</strong></td>
                                                        <td>{{ $customerReturn->product->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('app.products.category') }}:</strong></td>
                                                        <td>{{ $customerReturn->product->category->name ?? __('app.common.not_specified') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('app.products.selling_price') }}:</strong></td>
                                                        <td>{{ number_format($customerReturn->product->selling_price, 2) }}
                                                            {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>{{ __('app.products.current_stock') }}:</strong></td>
                                                        <td>{{ $customerReturn->product->quantity }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{ __('app.products.barcode') }}:</strong></td>
                                                        <td>{{ $customerReturn->product->barcode ?? __('app.common.not_specified') }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection