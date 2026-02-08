@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.purchases.title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">{{ __('app.purchases.all') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                <a href="{{ route('purchases.export') }}" class="btn btn-success round px-2 shadow mr-1">
                    <i class="la la-file-excel-o"></i> {{ __('app.purchases.export') }}
                </a>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                    <i class="la la-plus"></i> {{ __('app.purchases.add_new') }}
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Filter Card -->
        <div class="card pull-up border-0 shadow-sm mb-2"
            style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row items-align-center">
                            <div class="col-md-4">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.purchases.general_search') }}</label>
                                    <div class="position-relative has-icon-left">
                                        <input type="text" name="search" class="form-control round border-primary"
                                            value="{{ request('search') }}" placeholder="{{ __('app.purchases.search_placeholder') }}">
                                        <div class="form-control-position">
                                            <i class="la la-search primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.purchases.supplier') }}</label>
                                    <select name="supplier_id" class="form-control round border-primary">
                                        <option value="">{{ __('app.purchases.all_suppliers') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label class="text-bold-600 small">{{ __('app.purchases.type') }}</label>
                                    <select name="type" class="form-control round border-primary">
                                        <option value="">{{ __('app.purchases.type_all') }}</option>
                                        <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>{{ __('app.purchases.type_product') }}</option>
                                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>{{ __('app.purchases.type_expense') }}</option>
                                    </select>
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
                <strong>تم بنجاح!</strong> {{ session('success') }}
            </div>
        @endif

        <!-- Purchases Table -->
        <div class="card pull-up border-0 shadow-sm"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('app.purchases.invoice_number') }}</th>
                                    <th>{{ __('app.purchases.supplier') }}</th>
                                    <th>{{ __('app.purchases.type') }}</th>
                                    <th>{{ __('app.invoices.paid_amount') }}</th>
                                    <th>{{ __('app.invoices.remaining_amount') }}</th>
                                    <th>{{ __('app.invoices.total') }}</th>
                                    <th class="text-right">{{ __('app.invoices.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <span class="badge badge-soft-primary round-lg px-1 text-bold-700">
                                                #{{ $purchase->invoice_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-bold-700 text-dark">{{ $purchase->supplier ? $purchase->supplier->name : 'لا يوجد مورد' }}</span>
                                        </td>
                                        <td>
                                            @if ($purchase->type == 'product')
                                                <span class="badge badge-soft-success round px-1">{{ __('app.purchases.type_product') }}</span>
                                            @else
                                                <span class="badge badge-soft-warning round px-1">{{ __('app.purchases.type_expense') }}</span>
                                            @endif
                                        </td>
                                        <td><span
                                                class="success text-bold-600">{{ number_format($purchase->paid_amount, 2) }}</span>
                                            <small class="text-muted">ج.م</small>
                                        </td>
                                        <td><span class="danger text-bold-600">{{ number_format($purchase->change, 2) }}</span>
                                            <small class="text-muted">ج.م</small>
                                        </td>
                                        <td><span
                                                class="text-bold-700 text-dark">{{ number_format($purchase->total_amount, 2) }}</span>
                                            <small class="text-muted">ج.م</small>
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{ route('purchases.show', $purchase->id) }}"
                                                    class="btn btn-sm btn-soft-info round mr-1 px-1">
                                                    <i class="la la-eye"></i> {{ __('app.invoices.view') }}
                                                </a>
                                                @if ($purchase->total_amount != $purchase->paid_amount)
                                                    <a href="{{ route('purchases.installments.create', ['purchase' => $purchase->id]) }}"
                                                        class="btn btn-sm btn-soft-primary round mr-1 px-1">
                                                        <i class="la la-plus"></i> {{ __('app.purchases.installment') }}
                                                    </a>
                                                @endif
                                                @if($purchase->type == 'expense')
                                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST"
                                                        style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-soft-danger round px-1"
                                                            onclick="return confirm('{{ __('app.invoices.delete_confirm') }}');">
                                                            <i class="la la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="la la-info-circle font-large-3 d-block mb-2 text-muted"></i>
                                            <span class="text-muted">لا توجد نتائج مطابقة لخيارات البحث.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-2">
                        {{ $purchases->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
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

        .badge-soft-warning {
            color: #f59e0b;
            background-color: rgba(245, 158, 11, 0.1);
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