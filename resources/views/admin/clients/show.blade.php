@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.clients.details') }}: {{ $client->name }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('clients.index') }}">{{ __('app.clients.all_clients') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.common.details') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12 mb-2 text-right">
            <a href="{{ route('clients.index') }}" class="btn btn-light round shadow-sm px-2">
                <i class="la la-arrow-right"></i> {{ __('app.common.back') }}
            </a>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <!-- Client Stats -->
            <div class="col-xl-4 col-lg-6 col-12">
                <div class="card pull-up border-0 shadow-sm mb-2"
                    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="primary text-bold-700">{{ $totalInvoices }} <small class="text-muted"
                                            style="font-size: 0.8rem;">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                    </h3>
                                    <span
                                        class="text-bold-600 text-muted small uppercase">{{ __('app.clients.total_invoices') }}</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="la la-file-text primary font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-12">
                <div class="card pull-up border-0 shadow-sm mb-2"
                    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="success text-bold-700">{{ $totalPaidAmount }} <small class="text-muted"
                                            style="font-size: 0.8rem;">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                    </h3>
                                    <span
                                        class="text-bold-600 text-muted small uppercase">{{ __('app.clients.total_paid') }}</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="la la-check-circle success font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-12">
                <div class="card pull-up border-0 shadow-sm mb-2"
                    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="danger text-bold-700">{{ $totalChange }} <small class="text-muted"
                                            style="font-size: 0.8rem;">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                    </h3>
                                    <span
                                        class="text-bold-600 text-muted small uppercase">{{ __('app.clients.total_due') }}</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="la la-money danger font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="col-12">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h4 class="card-title text-bold-700"><i class="la la-history"></i>
                            {{ __('app.clients.invoices_history') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-premium mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('app.clients.invoice_number') }}</th>
                                            <th>{{ __('app.clients.invoice_total') }}</th>
                                            <th>{{ __('app.clients.paid') }}</th>
                                            <th>{{ __('app.clients.due') }}</th>
                                            <th class="text-right">{{ __('app.clients.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client->invoices as $invoice)
                                            <tr>
                                                <td><span class="text-bold-600">{{ $loop->iteration }}</span></td>
                                                <td><span
                                                        class="badge badge-soft-primary round px-1">{{ $invoice->invoice_code }}</span>
                                                </td>
                                                <td><span
                                                        class="text-bold-700 text-dark">{{ number_format($invoice->total_amount, 2) }}</span>
                                                    <small
                                                        class="text-muted">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                                </td>
                                                <td><span
                                                        class="success text-bold-600">{{ number_format($invoice->paid_amount, 2) }}</span>
                                                    <small
                                                        class="text-muted">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="{{ $invoice->change > 0 ? 'danger' : 'success' }} text-bold-600">
                                                        {{ number_format($invoice->change, 2) }}
                                                    </span>
                                                    <small
                                                        class="text-muted">{{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</small>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                                        class="btn btn-sm btn-soft-info round px-1">
                                                        <i class="la la-file-text"></i> {{ __('app.clients.view_details') }}
                                                    </a>
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
    </div>

    <style>
        .badge-soft-primary {
            color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
        }

        .btn-soft-info {
            color: #0891b2;
            background-color: rgba(6, 182, 212, 0.1);
            border: none;
        }

        .btn-soft-info:hover {
            background-color: #0891b2;
            color: white !important;
        }

        .table-premium th {
            font-weight: 700;
            color: #1e293b;
            border-top: none;
        }

        .table-premium td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endsection