@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>مرتجعات الموردين</h1>
            <a href="{{ route('supplier-returns.create') }}" class="btn btn-primary">
                إضافة مرتجع جديد
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
                        <th>المنتج</th>
                        <th>المورد</th>
                        <th>الكمية المرتجعة</th>
                        <th>سعر التكلفة</th>
                        <th>القيمة الإجمالية</th>
                        <th>السبب</th>
                        <th>الحالة</th>
                        <th>تاريخ الإرجاع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td>{{ $return->id }}</td>
                            <td>{{ $return->product->name }}</td>
                            <td>{{ $return->supplier->name }}</td>
                            <td>{{ $return->quantity_returned }}</td>
                            <td>{{ number_format($return->cost_price, 2) }} ج.م</td>
                            <td>{{ number_format($return->total_value, 2) }} ج.م</td>
                            <td>{{ $return->reason ?? 'غير محدد' }}</td>
                            <td>
                                @switch($return->status)
                                    @case('pending')
                                        <span class="badge badge-warning">في الانتظار</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success">مكتمل</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">ملغي</span>
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
                                    غير محدد
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('supplier-returns.show', $return) }}" class="btn btn-info btn-sm">عرض</a>
                                    @if($return->status !== 'completed')
                                        <a href="{{ route('supplier-returns.edit', $return) }}" class="btn btn-warning btn-sm">تعديل</a>
                                    @endif
                                    <form action="{{ route('supplier-returns.destroy', $return) }}" method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المرتجع؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">لا توجد مرتجعات</td>
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
