@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow p-4">
        <h1 class="text-center mb-4">تقرير تحديثات وإضافات المنتجات</h1>

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>اسم المنتج</th>
                        <th>الكمية القديمة</th>
                        <th>الكمية الجديدة</th>
                        <th>الإجراء</th>
                        <th>المستخدم</th>
                        <th>تاريخ التحديث</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quantityUpdates as $update)
                        <tr>
                            <td>{{ $update->product_name }}</td>
                            <td>{{ $update->old_quantity ?? '------' }}</td>
                            <td>{{ $update->new_quantity }}</td>
                            <td>{{ $update->action == 'added' ? 'إضافة' : 'تحديث' }}</td>
                            <td>{{ $update->user_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($update->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
