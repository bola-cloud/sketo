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
                    </tr>
                </thead>
                <tbody>
                    @foreach($installments as $installment)
                        <tr>
                            <td>{{ number_format($installment->amount_paid, 2) }} ج.م</td>
                            <td>{{ $installment->date_paid }}</td>
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
</div>
@endsection
