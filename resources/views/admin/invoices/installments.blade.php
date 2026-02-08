@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>{{ __('app.invoices.installments_for', ['code' => $invoice->invoice_code]) }}</h1>

        <div class="row">
            <div class="col-md-6">
                <!-- Installments List -->
                <h4>{{ __('app.invoices.paid_installments') }}</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('app.invoices.amount_paid') }}</th>
                            <th>{{ __('app.invoices.payment_date') }}</th>
                            <th>{{ __('app.invoices.actions') }}</th> <!-- Column for actions -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($installments as $installment)
                            <tr>
                                <td>{{ number_format($installment->amount_paid, 2) }}
                                    {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</td>
                                <td>{{ $installment->date_paid }}</td>
                                <td>
                                    <!-- Edit Button that opens the modal -->
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#editModal{{ $installment->id }}">
                                        {{ __('app.common.edit') }}
                                    </button>

                                    <!-- Modal for editing the installment -->
                                    <div class="modal fade" id="editModal{{ $installment->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="editModalLabel{{ $installment->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{ $installment->id }}">
                                                        {{ __('app.invoices.edit_installment') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('sales.installments.update', [$invoice->id, $installment->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label
                                                                for="amount_paid">{{ __('app.invoices.amount_paid') }}</label>
                                                            <input type="number" name="amount_paid" class="form-control"
                                                                value="{{ $installment->amount_paid }}" step="0.01" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="date_paid">{{ __('app.invoices.payment_date') }}</label>
                                                            <input type="date" name="date_paid" class="form-control"
                                                                value="{{ $installment->date_paid }}" required>
                                                        </div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('app.invoices.update_installment') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <!-- Add New Installment -->
                <h4>{{ __('app.invoices.add_new_installment') }}</h4>
                <form action="{{ route('sales.installments.store', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="amount_paid">{{ __('app.invoices.amount_paid') }}</label>
                        <input type="number" name="amount_paid" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="date_paid">{{ __('app.invoices.payment_date') }}</label>
                        <input type="date" name="date_paid" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('app.invoices.add_installment_btn') }}</button>
                </form>
            </div>
        </div>

        <!-- Return Button to go back to Invoices Index -->
        <div class="row mt-4">
            <div class="col-md-12">
                <a href="{{ route('invoices.index') }}"
                    class="btn btn-secondary">{{ __('app.invoices.back_to_invoices') }}</a>
            </div>
        </div>
    </div>
@endsection