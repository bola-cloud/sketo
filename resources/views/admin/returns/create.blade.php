@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('app.returns.add_new') }}</h4>
                    <div class="heading-elements">
                        <a href="{{ route('customer-returns.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-right"></i> {{ __('app.common.back_to_list') }}
                        </a>
                    </div>
                </div>

                <div class="card-content">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h4>{{ __('app.returns.how_to_add') }}</h4>
                            <p>{{ __('app.returns.how_to_add_desc') }}</p>
                            <ul>
                                <li>{{ __('app.returns.step_1') }}</li>
                                <li>{{ __('app.returns.step_2') }}</li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_search">{{ __('app.returns.search_invoice') }}</label>
                                    <input type="text" id="invoice_search" class="form-control"
                                        placeholder="{{ __('app.returns.search_placeholder') }}">
                                </div>
                                <div id="search_results"></div>
                            </div>
                        </div>

                        <hr>

                        <h5>{{ __('app.returns.recent_invoices') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.reports.invoice_code') }}</th>
                                        <th>{{ __('app.clients.name') }}</th>
                                        <th>{{ __('app.common.date') }}</th>
                                        <th>{{ __('app.common.total') }}</th>
                                        <th>{{ __('app.common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $recentInvoices = \App\Models\Invoice::with('client')
                                            ->withSum('sales', 'quantity')
                                            ->withCount('returns')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(10)
                                            ->get();
                                    @endphp
                                    @foreach($recentInvoices as $invoice)
                                        @php
                                            $isFullyReturned = $invoice->returns_count > 0 && $invoice->sales_sum_quantity <= 0.0001;
                                            $hasPartialReturns = $invoice->returns_count > 0 && $invoice->sales_sum_quantity > 0.0001;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $invoice->invoice_code }}
                                                @if($isFullyReturned)
                                                    <span class="badge badge-danger ml-1" style="font-size: 11px;">مرتجع بالكامل</span>
                                                @elseif($hasPartialReturns)
                                                    <span class="badge badge-warning text-dark ml-1" style="font-size: 11px;">مرتجع جزئي</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->buyer_name }}</td>
                                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                            <td>{{ number_format($invoice->total_amount, 2) }}
                                                {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</td>
                                            <td>
                                                @if($isFullyReturned)
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        مرتجع بالكامل
                                                    </button>
                                                @else
                                                    <a href="{{ route('customer-returns.createForInvoice', $invoice) }}"
                                                        class="btn btn-sm btn-primary">
                                                        {{ __('app.returns.return_products_btn') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('invoice_search').addEventListener('input', function () {
            const query = this.value;
            if (query.length > 2) {
                // Here you would typically make an AJAX call to search for invoices
                // For now, we'll just show a message
                document.getElementById('search_results').innerHTML = '<p class="text-muted">{{ __('app.returns.search_result_msg') }}</p>';
            } else {
                document.getElementById('search_results').innerHTML = '';
            }
        });
    </script>
@endsection