@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>{{ __('app.supplier_returns.show_title') }}{{ $supplierReturn->id }}</h1>
            <div>
                @if($supplierReturn->status !== 'completed')
                    <a href="{{ route('supplier-returns.edit', $supplierReturn) }}" class="btn btn-warning">{{ __('app.common.edit') }}</a>
                @endif
                <a href="{{ route('supplier-returns.index') }}" class="btn btn-secondary">{{ __('app.common.back_to_list') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('app.supplier_returns.product_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>{{ __('app.products.name') }}:</th>
                                <td>{{ $supplierReturn->product->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('app.products.barcode') }}:</th>
                                <td>{{ $supplierReturn->product->barcode ?? __('app.common.not_specified') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('app.products.category') }}:</th>
                                <td>{{ $supplierReturn->product->category->name ?? __('app.common.not_specified') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('app.products.brand') }}:</th>
                                <td>{{ $supplierReturn->product->brand->name ?? __('app.common.not_specified') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('app.supplier_returns.available_quantity') }}</th>
                                <td>{{ $supplierReturn->product->quantity }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('app.supplier_returns.supplier_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>{{ __('app.supplier_returns.supplier') }}:</th>
                                <td>{{ $supplierReturn->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('app.clients.phone') }}:</th>
                                <td>{{ $supplierReturn->supplier->phone ?? __('app.common.not_specified') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('app.supplier_returns.return_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('app.supplier_returns.quantity_returned') }}:</th>
                                        <td>{{ $supplierReturn->quantity_returned }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('app.supplier_returns.cost_price') }}:</th>
                                        <td>{{ number_format($supplierReturn->cost_price, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('app.supplier_returns.total_value') }}:</th>
                                        <td><strong>{{ number_format($supplierReturn->total_value, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('app.supplier_returns.status') }}:</th>
                                        <td>
                                            @switch($supplierReturn->status)
                                                @case('pending')
                                                    <span class="badge badge-warning">{{ __('app.common.status_pending') }}</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-success">{{ __('app.common.status_completed') }}</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">{{ __('app.common.status_cancelled') }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('app.supplier_returns.created_at') }}</th>
                                        <td>{{ $supplierReturn->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('app.supplier_returns.return_date') }}:</th>
                                        <td>{{ $supplierReturn->returned_at ? $supplierReturn->returned_at->format('Y-m-d H:i:s') : __('app.common.not_specified') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>{{ __('app.supplier_returns.reason') }}:</th>
                                        <td>{{ $supplierReturn->reason ?? __('app.common.not_specified') }}</td>
                                    </tr>
                                    @if($supplierReturn->purchase)
                                        <tr>
                                            <th>{{ __('app.supplier_returns.purchase_invoice') }}:</th>
                                            <td>
                                                <a href="{{ route('purchases.show', $supplierReturn->purchase) }}" class="text-primary">
                                                    {{ $supplierReturn->purchase->invoice_number }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($supplierReturn->notes)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h6>{{ __('app.supplier_returns.notes') }}:</h6>
                                    <p class="bg-light p-3 rounded">{{ $supplierReturn->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($supplierReturn->status !== 'completed')
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>{{ __('app.common.note') }}:</strong> {{ __('app.supplier_returns.pending_note') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
