@extends('layouts.admin')

@section('title', __('app.reports.financial_summary'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="font-weight-bold text-white mb-1">
                    {{ __('app.reports.financial_summary') ?? 'Financial Summary (P&L)' }}
                </h2>
                <p class="text-muted mb-0">{{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-premium shadow-sm">
                    <i class="la la-print mr-1"></i> {{ __('app.common.print') }}
                </button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success shadow-sm ml-2"
                    style="border-radius: 12px; font-weight: 600;">
                    <i class="la la-file-excel-o mr-1"></i> {{ __('app.products.export_excel') ?? 'Export' }}
                </a>
            </div>
        </div>

        <!-- P&L Statement Card -->
        <div class="premium-card p-0 overflow-hidden shadow-lg border-0 bg-glass" style="border-radius: 20px;">
            <div class="p-4 border-bottom border-white-10 bg-slate-800">
                <h4 class="mb-0 text-white font-weight-bold">{{ __('app.reports.income_statement') }}
                </h4>
            </div>

            <div class="p-5">
                <div class="row section-row mb-4">
                    <div class="col-8">
                        <h5 class="text-white-50 uppercase small font-weight-bold mb-0">
                            {{ __('app.reports.revenue') }}
                        </h5>
                    </div>
                    <div class="col-4 text-right">
                        <h5 class="text-white font-weight-bold mb-0">{{ number_format($revenue, 2) }}</h5>
                    </div>
                </div>

                <div class="row section-row mb-4">
                    <div class="col-8 pl-4">
                        <h5 class="text-white-50 uppercase small font-weight-bold mb-0">(-)
                            {{ __('app.reports.cogs') }}
                        </h5>
                    </div>
                    <div class="col-4 text-right">
                        <h5 class="text-rose font-weight-bold mb-0">({{ number_format($cogs, 2) }})</h5>
                    </div>
                </div>

                <div class="row section-row py-3 border-top border-bottom border-white-10 mb-5 bg-white-5">
                    <div class="col-8">
                        <h4 class="text-white font-weight-bold mb-0">{{ __('app.reports.gross_profit') }}
                        </h4>
                    </div>
                    <div class="col-4 text-right">
                        <h4 class="text-emerald font-weight-bold mb-0">{{ number_format($grossProfit, 2) }}</h4>
                    </div>
                </div>

                <div class="row section-row mb-4">
                    <div class="col-8 pl-4">
                        <h5 class="text-white-50 uppercase small font-weight-bold mb-0">(-)
                            {{ __('app.reports.operating_expenses') }}
                        </h5>
                    </div>
                    <div class="col-4 text-right">
                        <h5 class="text-rose font-weight-bold mb-0">({{ number_format($operatingExpenses, 2) }})</h5>
                    </div>
                </div>

                <div class="row section-row py-4 border-top border-bottom border-white-20 bg-indigo-900 shadow-inner mt-5"
                    style="border-radius: 12px;">
                    <div class="col-8">
                        <h3 class="text-white font-weight-bold mb-0">{{ __('app.reports.net_profit') }}</h3>
                    </div>
                    <div class="col-4 text-right">
                        <h3 class="{{ $netProfit >= 0 ? 'text-emerald' : 'text-rose' }} font-weight-bold mb-0">
                            {{ number_format($netProfit, 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-900 text-center">
                <small class="text-muted">{{ __('app.reports.generated_on') }}
                    {{ now()->format('Y-m-d H:i') }}</small>
            </div>
        </div>
    </div>

    <style>
        .bg-white-5 {
            background: rgba(255, 255, 255, 0.05);
        }

        .border-white-10 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .border-white-20 {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        .text-emerald {
            color: #10b981 !important;
        }

        .text-rose {
            color: #f43f5e !important;
        }

        .bg-glass {
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.7);
        }

        .section-row {
            align-items: center;
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

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            color: white;
        }

        @media print {

            .btn-premium,
            .sidebar-wrapper,
            .header-navbar,
            .footer {
                display: none !important;
            }

            .main-menu {
                display: none !important;
            }

            .app-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            .premium-card {
                background: white !important;
                color: black !important;
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }

            .text-white,
            .text-white-50 {
                color: black !important;
            }

            .bg-slate-800,
            .bg-slate-900,
            .bg-indigo-900 {
                background: white !important;
                color: black !important;
                border-bottom: 2px solid #000 !important;
            }

            .text-emerald,
            .text-rose {
                font-weight: bold !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
@endsection