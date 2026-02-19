@extends('layouts.admin')

@section('content')

    <div class="container-fluid card shadow-lg p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-center flex-grow-1">{{ __('app.reports.transfers_title') }} <i class="fas fa-exchange-alt text-primary"></i></h1>
            <span class="badge bg-info text-dark ms-3" data-bs-toggle="tooltip"
                title="{{ __('app.reports.transfers_tooltip') }}">
                <i class="fas fa-info-circle"></i> {{ __('app.reports.transfers_badge') }}
            </span>
        </div>
        <!-- Optional: Search/filter bar (UI only) -->
        <div class="row mb-3">
            <div class="col-md-6 mx-auto">
                <input type="text" class="form-control" placeholder="{{ __('app.reports.transfers_search') }}">
            </div>
        </div>


        @if($transfers->count() > 0)
            <div class="table-responsive rounded shadow-sm">
                <table class="table table-hover align-middle bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th data-bs-toggle="tooltip" title="{{ __('app.reports.old_invoice') }}">{{ __('app.reports.old_invoice') }}</th>
                            <th data-bs-toggle="tooltip" title="{{ __('app.reports.new_invoice') }}">{{ __('app.reports.new_invoice') }}</th>
                            <th>{{ __('app.reports.old_product') }}</th>
                            <th>{{ __('app.reports.new_product') }}</th>
                            <th data-bs-toggle="tooltip" title="{{ __('app.reports.qty_before') }}">{{ __('app.reports.qty_before') }}</th>
                            <th data-bs-toggle="tooltip" title="{{ __('app.reports.qty_transferred') }}">{{ __('app.reports.qty_transferred') }}</th>

                            <th data-bs-toggle="tooltip" title="{{ __('app.reports.sold_from_old') }}">{{ __('app.reports.sold_from_old') }}</th>
                            <th>{{ __('app.reports.old_cost') }}</th>
                            <th>{{ __('app.reports.old_selling') }}</th>
                            <th>{{ __('app.reports.new_cost') }}</th>
                            <th>{{ __('app.reports.new_selling') }}</th>
                            <th>{{ __('app.reports.transfer_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $transfer->old_invoice_number }}</span></td>
                                <td><span class="badge bg-primary">{{ $transfer->new_invoice_number }}</span></td>
                                <td>
                                    <strong>{{ $transfer->old_product_name }}</strong>
                                    <br><small class="text-muted">ID: {{ $transfer->product_id }}</small>
                                </td>
                                <td>
                                    <strong class="text-success">{{ $transfer->new_product_name }}</strong>
                                    <br><small class="text-muted">ID: {{ $transfer->new_product_id }}</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $transfer->quantity_before_transfer ?? '-' }}</span></td>
                                <td><span class="badge bg-info text-dark">{{ $transfer->transferred_quantity }}</span></td>

                                <td><span class="badge bg-warning text-dark">{{ $transfer->sold_quantity_old_purchase }}</span></td>
                                <td><span class="text-muted">{{ number_format($transfer->old_cost_price, 2) }} {{ __('app.common.currency') }}</span></td>
                                <td><span class="text-muted">{{ number_format($transfer->old_selling_price, 2) }} {{ __('app.common.currency') }}</span></td>
                                <td><span class="text-info">{{ number_format($transfer->new_cost_price, 2) }} {{ __('app.common.currency') }}</span></td>
                                <td><span class="text-success">{{ number_format($transfer->new_selling_price, 2) }} {{ __('app.common.currency') }}</span></td>
                                <td><small>{{ $transfer->formatted_created_at }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Statistics -->
            <div class="row mt-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-random"></i> {{ __('app.reports.total_transfers') }}</h5>
                            <h3>{{ $transfers->count() }}</h3>
                            <small>{{ __('app.reports.transfer_count') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-cubes"></i> {{ __('app.reports.total_quantities') }}</h5>
                            <h3>{{ $transfers->sum('transferred_quantity') }}</h3>
                            <small>{{ __('app.reports.transfer_unit') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-chart-line"></i> {{ __('app.reports.avg_profit') }}</h5>
                            <h3>{{ number_format($transfers->avg(function ($t) {
                return $t->new_selling_price - $t->new_cost_price; }), 2) }}
                                {{ __('app.common.currency') }}</h3>
                            <small>{{ __('app.reports.per_unit') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-coins"></i> {{ __('app.reports.total_profit') }}</h5>
                            <h3>{{ number_format($transfers->sum(function ($t) {
                return ($t->new_selling_price - $t->new_cost_price) * $t->transferred_quantity; }), 2) }}
                                {{ __('app.common.currency') }}</h3>
                            <small>{{ __('app.reports.from_transfers') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $transfers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">{{ __('app.reports.no_transfers') }}</h4>
                <p class="text-muted">{{ __('app.reports.no_transfers_msg') }}</p>
            </div>
        @endif
    </div>
@endsection