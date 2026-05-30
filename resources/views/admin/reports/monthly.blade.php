@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold text-white mb-1">{{ __('app.reports.title_monthly') }}</h2>
                <p class="text-muted mb-0">{{ now()->format('M, Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-premium shadow-sm">
                    <i class="la la-print mr-1"></i> {{ __('app.common.print') }}
                </button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="premium-card bg-glass" style="border-radius: 15px;">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.reports.total_quantity_sold') }}</p>
                    <h2 class="font-weight-bold mb-0 text-info">{{ $totalQuantity }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card bg-glass" style="border-radius: 15px;">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.reports.total_revenue') }}</p>
                    <h2 class="font-weight-bold mb-0 text-success">{{ number_format($totalRevenue, 2) }} {{ __('app.common.currency') }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card bg-glass" style="border-radius: 15px;">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.reports.total_profit') }}</p>
                    <h2 class="font-weight-bold mb-0 text-warning">{{ number_format($totalProfit, 2) }} {{ __('app.common.currency') }}</h2>
                </div>
            </div>
        </div>

        <div class="premium-card p-0 overflow-hidden shadow-lg border-0 bg-glass" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover mb-0 custom-table">
                    <thead class="bg-slate-800 text-white border-0">
                        <tr>
                            <th class="border-0">{{ __('app.reports.invoice_code') }}</th>
                            <th class="border-0">{{ __('app.reports.paid_amount') }}</th>
                            <th class="border-0">{{ __('app.reports.total_after_discount') }}</th>
                            <th class="border-0">{{ __('app.reports.change_remaining') }}</th>
                            <th class="border-0">{{ __('app.reports.sales_details') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        @foreach($invoices as $invoice)
                            <tr class="border-white-10">
                                <td><span class="badge badge-info">{{ $invoice->invoice_code }}</span></td>
                                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ number_format($invoice->change, 2) }}</td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($invoice->sales as $sale)
                                            <li><i class="la la-check text-success mr-1"></i>{{ $sale->product->name }} ({{ $sale->quantity }}) - {{ number_format($sale->product->selling_price, 2) }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-glass { backdrop-filter: blur(10px); background: rgba(30, 41, 59, 0.7); }
        .border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
        .custom-table th { padding: 20px; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .custom-table td { padding: 15px 20px; vertical-align: middle; }
        .btn-premium { background: linear-gradient(135deg, var(--p-indigo), var(--p-purple)); color: white; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 600; transition: all 0.3s ease; }
        @media print {
            .btn-premium, .sidebar-wrapper, .header-navbar, .footer { display: none !important; }
            .premium-card { background: white !important; color: black !important; box-shadow: none !important; border: 1px solid #eee !important; }
            .text-white, .text-muted, .text-info, .text-success, .text-warning { color: black !important; }
            .bg-slate-800 { background: #f8fafc !important; color: black !important; border-bottom: 2px solid #000; }
            .custom-table td, .custom-table th { border: 1px solid #eee !important; color: black !important; }
            body { background: white !important; }
        }
    </style>
@endsection