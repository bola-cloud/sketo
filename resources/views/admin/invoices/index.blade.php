@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.invoices.title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.invoices.all_invoices') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                <a href="{{ route('invoices.export') }}" class="btn btn-success round px-2 shadow">
                    <i class="la la-file-excel-o"></i> {{ __('app.invoices.export_excel') }}
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Search Filters -->
        <div class="card pull-up border-0 shadow-sm mb-2"
            style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <form id="search-form" action="{{ route('invoices.search') }}" method="GET">
                        @csrf
                        <div class="row items-align-center">
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.invoices.search_general') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" class="form-control round border-primary" name="query"
                                            placeholder="{{ __('app.invoices.search_placeholder') }}"
                                            value="{{ request('query') }}">
                                        <div class="form-control-position">
                                            <i class="la la-search primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.invoices.date_from') }}</label>
                                    <input type="date" class="form-control round border-primary" name="date_from"
                                        value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.invoices.date_to') }}</label>
                                    <input type="date" class="form-control round border-primary" name="date_to"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group mb-1 w-100">
                                    <button type="submit" class="btn btn-primary round btn-block shadow-sm">
                                        <i class="la la-filter"></i> {{ __('app.invoices.filter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ __('app.common.success') }}!</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>خطأ!</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Invoices Table -->
        <div class="card pull-up border-0 shadow-sm"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0" id="invoice-table">
                            <thead>
                                <tr>
                                    <th>{{ __('app.invoices.invoice_code') }}</th>
                                    <th>{{ __('app.invoices.client') }}</th>
                                    <th>{{ __('app.invoices.seller') }}</th>
                                    <th>{{ __('app.invoices.date') }}</th>
                                    <th>{{ __('app.invoices.status') }}</th>
                                    <th>{{ __('app.invoices.installments') }}</th>
                                    <th class="text-right">{{ __('app.invoices.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    @php
                                        $returnsCount = $invoice->returns()->count();
                                        $hasUnpaidAmount = $invoice->total_amount > $invoice->paid_amount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="badge badge-soft-primary round-lg px-1 text-bold-700">{{ $invoice->invoice_code }}</span>
                                                @if($returnsCount > 0)
                                                    <span class="badge badge-warning round ml-1"
                                                        title="{{ __('app.invoices.returns_count', ['count' => $returnsCount]) }}">
                                                        <i class="la la-undo"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-bold-700 text-dark">{{ $invoice->client ? $invoice->client->name : __('app.invoices.no_client') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small"><i class="la la-user"></i>
                                                {{ $invoice->user->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small"><i class="la la-calendar"></i>
                                                {{ $invoice->created_at->format('Y-m-d') }}</span>
                                        </td>
                                        <td>
                                            @if($hasUnpaidAmount)
                                                <span class="badge badge-soft-danger round px-1">{{ __('app.invoices.incomplete') }}</span>
                                            @else
                                                <span class="badge badge-soft-success round px-1">{{ __('app.invoices.paid_full') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasUnpaidAmount)
                                                <a href="{{ route('sales.installments.index', $invoice->id) }}"
                                                    class="btn btn-sm btn-soft-info round px-1">
                                                    <i class="la la-list"></i> {{ __('app.invoices.view_installments') }}
                                                </a>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{ route('invoices.show', ['invoice' => $invoice->id]) }}"
                                                    class="btn btn-sm btn-soft-info round mr-1 px-1">
                                                    <i class="la la-eye"></i> {{ __('app.invoices.view') }}
                                                </a>
                                                <a class="btn btn-sm btn-soft-primary round mr-1 px-1"
                                                    href="{{ route('cashier.printInvoice', $invoice->id) }}">
                                                    <i class="la la-print"></i> {{ __('app.invoices.print') }}
                                                </a>
                                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger round px-1"
                                                        onclick="return confirm('{{ __('app.invoices.delete_confirm') }}')">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-2">
                        {{ $invoices->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .round-lg {
            border-radius: 10px !important;
        }

        .badge-soft-primary {
            color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
        }

        .badge-soft-success {
            color: #10b981;
            background-color: rgba(16, 185, 129, 0.1);
        }

        .badge-soft-danger {
            color: #ef4444;
            background-color: rgba(239, 68, 68, 0.1);
        }

        .btn-soft-info {
            color: #0891b2;
            background-color: rgba(6, 182, 212, 0.1);
            border: none;
        }

        .btn-soft-primary {
            color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
            border: none;
        }

        .btn-soft-danger {
            color: #ef4444;
            background-color: rgba(239, 68, 68, 0.1);
            border: none;
        }

        .btn-soft-info:hover {
            background-color: #0891b2;
            color: white !important;
        }

        .btn-soft-primary:hover {
            background-color: #3b82f6;
            color: white !important;
        }

        .btn-soft-danger:hover {
            background-color: #ef4444;
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