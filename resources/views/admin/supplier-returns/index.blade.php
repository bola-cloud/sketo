@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>{{ __('app.supplier_returns.title') }}</h1>
            <a href="{{ route('supplier-returns.create') }}" class="btn btn-primary">
                {{ __('app.supplier_returns.add_new') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('app.supplier_returns.product') }}</th>
                        <th>{{ __('app.supplier_returns.supplier') }}</th>
                        <th>{{ __('app.supplier_returns.quantity_returned') }}</th>
                        <th>{{ __('app.supplier_returns.cost_price') }}</th>
                        <th>{{ __('app.supplier_returns.total_value') }}</th>
                        <th>{{ __('app.supplier_returns.reason') }}</th>
                        <th>{{ __('app.supplier_returns.status') }}</th>
                        <th>{{ __('app.supplier_returns.return_date') }}</th>
                        <th>{{ __('app.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td>{{ $return->id }}</td>
                            <td>{{ $return->product->name }}</td>
                            <td>{{ $return->supplier->name }}</td>
                            <td>{{ $return->quantity_returned }}</td>
                            <td>{{ number_format($return->cost_price, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                            <td>{{ number_format($return->total_value, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                            <td>{{ $return->reason ?? __('app.common.not_specified') }}</td>
                            <td>
                                @switch($return->status)
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
                            <td>
                                @if($return->returned_at)
                                    @if(is_numeric($return->returned_at))
                                        {{ date('Y-m-d H:i', $return->returned_at) }}
                                    @else
                                        {{ $return->returned_at->format('Y-m-d H:i') }}
                                    @endif
                                @else
                                    {{ __('app.common.not_specified') }}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('supplier-returns.show', $return) }}" class="btn btn-info btn-sm">{{ __('app.common.show') }}</a>
                                    @if($return->status !== 'completed')
                                        <a href="{{ route('supplier-returns.edit', $return) }}" class="btn btn-warning btn-sm">{{ __('app.common.edit') }}</a>
                                    @endif
                                    <form action="{{ route('supplier-returns.destroy', $return) }}" method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('{{ __('app.supplier_returns.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('app.common.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('app.supplier_returns.no_returns') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
