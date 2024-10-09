@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>الأقساط الخاصة بالفاتورة {{ $invoice->invoice_code }}</h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Installments List -->
            <h4>الأقساط المدفوعة</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>المبلغ المدفوع</th>
                        <th>تاريخ الدفع</th>
                        <th>الإجراءات</th> <!-- Column for actions -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($installments as $installment)
                        <tr>
                            <td>{{ number_format($installment->amount_paid, 2) }} ج.م</td>
                            <td>{{ $installment->date_paid }}</td>
                            <td>
                                <!-- Edit Button that opens the modal -->
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $installment->id }}">
                                    تعديل
                                </button>

                                <!-- Modal for editing the installment -->
                                <div class="modal fade" id="editModal{{ $installment->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $installment->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $installment->id }}">تعديل القسط</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('sales.installments.update', [$invoice->id, $installment->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="amount_paid">المبلغ المدفوع</label>
                                                        <input type="number" name="amount_paid" class="form-control" value="{{ $installment->amount_paid }}" step="0.01" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="date_paid">تاريخ الدفع</label>
                                                        <input type="date" name="date_paid" class="form-control" value="{{ $installment->date_paid }}" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">تحديث القسط</button>
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
            <h4>إضافة قسط جديد</h4>
            <form action="{{ route('sales.installments.store', $invoice->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="amount_paid">المبلغ المدفوع</label>
                    <input type="number" name="amount_paid" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="date_paid">تاريخ الدفع</label>
                    <input type="date" name="date_paid" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">إضافة القسط</button>
            </form>
        </div>
    </div>

    <!-- Return Button to go back to Invoices Index -->
    <div class="row mt-4">
        <div class="col-md-12">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">العودة إلى الفواتير</a>
        </div>
    </div>
</div>
@endsection
