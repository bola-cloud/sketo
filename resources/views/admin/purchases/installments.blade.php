@extends('layouts.admin')

@section('content')
    <div class="container-fluid card p-5">
        <h1 class="text-center mb-4">{{ __('app.purchases.details') }}</h1>

        <h3>{{ __('app.purchases.invoice_number') }}: {{ $purchase->invoice_number }}</h3>
        <h4>{{ __('app.purchases.type') }}:
            {{ $purchase->type == 'product' ? __('app.purchases.type_product') : __('app.purchases.type_expense') }}</h4>
        <h4>{{ __('app.invoices.total') }}: {{ $purchase->total_amount }} ج.م</h4>
        <h4>{{ __('app.purchases.total_paid') }}: {{ $purchase->total_paid }} ج.م</h4>
        <h4>{{ __('app.invoices.remaining_amount') }}: {{ $purchase->change }} ج.م</h4>

        @if($purchase->type == 'product')
            <!-- Product Table (same as before) -->
        @endif

        <!-- Installments Table -->
        <h4>{{ __('app.purchases.installments_list') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('app.invoices.amount_paid') }}</th>
                        <th>{{ __('app.invoices.payment_date') }}</th>
                        <th>{{ __('app.invoices.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->installments as $installment)
                        <tr>
                            <td>{{ $installment->amount_paid }} ج.م</td>
                            <td>{{ $installment->date_paid }}</td>
                            <td>
                                <!-- Option to delete or update installment if needed -->
                                <form action="{{ route('purchases.installments.destroy', $installment->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('app.common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-5">
            <div class="col-md-8">
                <!-- Add Installment Form -->
                <h4>{{ __('app.purchases.add_payment') }}</h4>
                <form action="{{ route('purchases.installments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                    <div class="form-group">
                        <label for="amount_paid">{{ __('app.invoices.amount_paid') }}</label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date_paid">{{ __('app.invoices.payment_date') }}</label>
                        <input type="date" name="date_paid" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('app.purchases.add_payment_btn') }}</button>
                </form>
            </div>
        </div>

    </div>
@endsection