@extends('layouts.admin')

@section('title', __('app.reports.inventory_valuation'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold text-white mb-1">
                    {{ __('app.reports.inventory_valuation') }}</h2>
                <p class="text-muted mb-0">{{ __('app.reports.stock_status_as_of') }}
                    {{ now()->format('d M, Y') }}</p>
            </div>
            <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-premium shadow-sm">
                <i class="la la-print mr-1"></i> {{ __('app.common.print') }}
            </button>
            <a href="{{ route('reports.statistics.inventory_valuation', ['export' => 1]) }}" class="btn btn-success shadow-sm ml-2" style="border-radius: 12px; font-weight: 600;">
                <i class="la la-file-excel-o mr-1"></i> {{ __('app.products.export_excel') }}
            </a>
        </div>
        </div>

        <!-- Summary Tiles -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="premium-card" style="--card-accent: var(--p-indigo);">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">
                        {{ __('app.reports.total_cost_value') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">{{ number_format($totalCostValue, 2) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card" style="--card-accent: var(--p-emerald);">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">
                        {{ __('app.reports.total_retail_value') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">{{ number_format($totalRetailValue, 2) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card" style="--card-accent: var(--p-purple);">
                    <p class="text-muted small font-weight-bold mb-1 uppercase">
                        {{ __('app.reports.potential_profit') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">{{ number_format($potentialProfit, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="premium-card p-0 overflow-hidden shadow-lg border-0 bg-glass" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover mb-0 custom-table">
                    <thead class="bg-slate-800 text-white border-0">
                        <tr>
                            <th class="border-0">{{ __('app.products.name') }}</th>
                            <th class="border-0">{{ __('app.products.barcode') }}</th>
                            <th class="border-0">{{ __('app.products.quantity') }}</th>
                            <th class="border-0">{{ __('app.products.cost_price') }}</th>
                            <th class="border-0">{{ __('app.products.selling_price') }}</th>
                            <th class="border-0">{{ __('app.reports.cost_value') }}</th>
                            <th class="border-0">{{ __('app.reports.retail_value') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        @foreach($products as $product)
                            <tr class="border-white-10">
                                <td>{{ $product->name }}</td>
                                <td><span class="badge badge-indigo">{{ $product->barcode }}</span></td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ number_format($product->cost_price, 2) }}</td>
                                <td>{{ number_format($product->selling_price, 2) }}</td>
                                <td>{{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                                <td>{{ number_format($product->quantity * $product->selling_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-glass {
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.7);
        }

        .border-white-10 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .custom-table th {
            padding: 20px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .custom-table td {
            padding: 15px 20px;
            vertical-align: middle;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--p-indigo), var(--p-purple));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .badge-indigo {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        @media print {

            .btn-premium,
            .sidebar-wrapper,
            .header-navbar,
            .footer {
                display: none !important;
            }

            .premium-card {
                background: white !important;
                color: black !important;
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }

            .text-white,
            .text-muted {
                color: black !important;
            }

            .bg-slate-800 {
                background: #f8fafc !important;
                color: black !important;
            }

            .bg-glass {
                background: white !important;
            }

            .custom-table td,
            .custom-table th {
                border: 1px solid #eee !important;
                color: black !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endsection