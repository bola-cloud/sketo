@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">كل الفواتير</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>اسم المورد</th>
                <th>نوع الفاتورة</th>
                <th>المدفوع </th>
                <th>الباقي</th>
                <th>الإجمالي</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->supplier ? $purchase->supplier->name : 'لا يوجد مورد' }}</td>
                    <td>{{ $purchase->type == 'product' ? 'شراء منتجات' : 'نفقات' }}</td>
                    <td>{{ $purchase->paid_amount }}</td>
                    <td>{{ $purchase->change }}</td>
                    <td>{{ $purchase->total_amount }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">عرض التفاصيل</a>
                        @if ($purchase->total_amount != $purchase->paid_amount)
                            <a href="{{ route('purchases.installments.create', ['purchase' => $purchase->id] ) }}" class="btn btn-info"> اضافة قسط </a>
                        @endif     
                        @if($purchase->type == 'expense')
                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه الفاتورة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            {{ $purchases->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
    
</div>
@endsection
