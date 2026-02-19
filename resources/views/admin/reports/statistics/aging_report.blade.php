@extends('layouts.admin')

@section('title', __('app.reports.aging_report'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold text-white mb-1">{{ __('app.reports.aging_report') }}
                </h2>
                <p class="text-muted mb-0">{{ __('app.reports.debt_status_as_of') }}
                    {{ now()->format('d M, Y') }}</p>
            </div>
            <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-premium shadow-sm">
                <i class="la la-print mr-1"></i> {{ __('app.common.print') }}
            </button>
            <a href="{{ route('reports.statistics.aging_report', ['export' => 1]) }}" class="btn btn-success shadow-sm ml-2" style="border-radius: 12px; font-weight: 600;">
                <i class="la la-file-excel-o mr-1"></i> {{ __('app.products.export_excel') }}
            </a>
        </div>
        </div>

        <div class="row">
            <!-- Accounts Receivable (Clients) -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="premium-card p-0 overflow-hidden shadow-lg border-0 bg-glass h-100"
                    style="border-radius: 20px;">
                    <div
                        class="p-4 border-bottom border-white-10 bg-indigo-900 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white font-weight-bold">
                            {{ __('app.reports.accounts_receivable') }}</h4>
                        <span class="badge badge-indigo">{{ $receivables->count() }}
                            {{ __('app.reports.debtors') }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 custom-table">
                            <thead class="bg-slate-800 text-white border-0">
                                <tr>
                                    <th class="border-0">{{ __('app.reports.client') }}</th>
                                    <th class="border-0">{{ __('app.reports.invoice') }}</th>
                                    <th class="border-0 text-right">{{ __('app.reports.balance_due') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-white">
                                @forelse($receivables as $invoice)
                                    <tr class="border-white-10">
                                        <td>{{ $invoice->client->name ?? __('app.common.anonymous') }}</td>
                                        <td><small class="text-muted">{{ $invoice->invoice_code }}</small></td>
                                        <td class="text-right font-weight-bold text-rose">
                                            {{ number_format($invoice->change, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted small">
                                            {{ __('app.reports.no_outstanding_receivables') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($receivables->count() > 0)
                                <tfoot class="bg-slate-900 font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right text-white-50">{{ __('app.common.total') }}:</td>
                                        <td class="text-right text-rose">{{ number_format($receivables->sum('change'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Accounts Payable (Suppliers) -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="premium-card p-0 overflow-hidden shadow-lg border-0 bg-glass h-100"
                    style="border-radius: 20px;">
                    <div
                        class="p-4 border-bottom border-white-10 bg-purple-900 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white font-weight-bold">
                            {{ __('app.reports.accounts_payable') }}</h4>
                        <span class="badge badge-purple">{{ $payables->count() }}
                            {{ __('app.reports.unpaid_bills') }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 custom-table">
                            <thead class="bg-slate-800 text-white border-0">
                                <tr>
                                    <th class="border-0">{{ __('app.reports.supplier') }}</th>
                                    <th class="border-0">{{ __('app.reports.invoice') }}</th>
                                    <th class="border-0 text-right">{{ __('app.reports.balance_due') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-white">
                                @forelse($payables as $purchase)
                                    <tr class="border-white-10">
                                        <td>{{ $purchase->supplier->name ?? __('app.reports.miscellaneous_expense') }}</td>
                                        <td><small class="text-muted">{{ $purchase->invoice_number }}</small></td>
                                        <td class="text-right font-weight-bold text-rose">
                                            {{ number_format($purchase->change, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted small">
                                            {{ __('app.reports.no_outstanding_payables') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($payables->count() > 0)
                                <tfoot class="bg-slate-900 font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right text-white-50">{{ __('app.common.total') }}:</td>
                                        <td class="text-right text-rose">{{ number_format($payables->sum('change'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
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
            padding: 15px 20px;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }

        .custom-table td {
            padding: 12px 20px;
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

        .text-emerald {
            color: #10b981 !important;
        }

        .text-rose {
            color: #f43f5e !important;
        }

        .badge-indigo {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .badge-purple {
            background: rgba(168, 85, 247, 0.2);
            color: #a78bfa;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        .bg-indigo-900 {
            background: rgba(49, 46, 129, 0.8) !important;
        }

        .bg-purple-900 {
            background: rgba(81, 31, 144, 0.8) !important;
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
            .text-muted,
            .text-white-50 {
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

            .bg-indigo-900,
            .bg-purple-900 {
                background: white !important;
                color: black !important;
                border-bottom: 2px solid #000 !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endsection