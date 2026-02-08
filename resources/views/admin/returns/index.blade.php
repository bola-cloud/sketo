@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('app.returns.title') }}</h4>
                    <div class="heading-elements">
                        <a href="{{ route('customer-returns.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> {{ __('app.returns.add_new') }}
                        </a>
                    </div>
                </div>

                <div class="card-content">
                    <div class="card-body">
                        <!-- Search Form -->
                        <form method="POST" action="{{ route('customer-returns.search') }}" class="row mb-3">
                            @csrf
                            <div class="col-md-2">
                                <input type="text" name="invoice_code" class="form-control"
                                    placeholder="{{ __('app.reports.invoice_code') }}"
                                    value="{{ request('invoice_code') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="product_name" class="form-control"
                                    placeholder="{{ __('app.products.name') }}" value="{{ request('product_name') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('app.common.all_statuses') }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        {{ __('app.common.status_pending') }}</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        {{ __('app.common.status_completed') }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        {{ __('app.common.status_cancelled') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">{{ __('app.common.search') }}</button>
                                <a href="{{ route('customer-returns.index') }}"
                                    class="btn btn-secondary">{{ __('app.common.reset') }}</a>
                            </div>
                        </form>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.returns.return_id') }}</th>
                                        <th>{{ __('app.reports.invoice_code') }}</th>
                                        <th>{{ __('app.clients.name') }}</th>
                                        <th>{{ __('app.products.product') }}</th>
                                        <th>{{ __('app.returns.qty_returned') }}</th>
                                        <th>{{ __('app.returns.return_amount') }}</th>
                                        <th>{{ __('app.returns.reason') }}</th>
                                        <th>{{ __('app.common.status') }}</th>
                                        <th>{{ __('app.users.user') }}</th>
                                        <th>{{ __('app.common.date') }}</th>
                                        <th>{{ __('app.common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returns as $return)
                                        <tr>
                                            <td>{{ $return->id }}</td>
                                            <td>
                                                <a href="{{ route('invoices.show', $return->invoice_id) }}">
                                                    {{ $return->invoice->invoice_code }}
                                                </a>
                                            </td>
                                            <td>{{ $return->invoice->buyer_name }}</td>
                                            <td>{{ $return->product->name }}</td>
                                            <td>{{ $return->quantity_returned }}</td>
                                            <td>{{ number_format($return->return_amount, 2) }}
                                                {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                            <td>{{ $return->reason }}</td>
                                            <td>
                                                @if($return->status == 'pending')
                                                    <span class="badge badge-warning">{{ __('app.common.status_pending') }}</span>
                                                @elseif($return->status == 'completed')
                                                    <span class="badge badge-success">{{ __('app.common.status_completed') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('app.common.status_cancelled') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $return->user->name }}</td>
                                            <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('customer-returns.show', $return) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('customer-returns.edit', $return) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">{{ __('app.returns.no_returns') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $returns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection