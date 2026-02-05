@extends('layouts.admin')

@section('content')

    <div class="container-fluid card shadow-lg p-5 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-center flex-grow-1">تقرير نقل المنتجات <i class="fas fa-exchange-alt text-primary"></i></h1>
            <span class="badge bg-info text-dark ms-3" data-bs-toggle="tooltip"
                title="يتم عرض عمليات النقل التي تحتوي على كمية منقولة فقط (1 أو أكثر)">
                <i class="fas fa-info-circle"></i> يعرض فقط النقل بكمية &ge; 1
            </span>
        </div>
        <!-- Optional: Search/filter bar (UI only) -->
        <div class="row mb-3">
            <div class="col-md-6 mx-auto">
                <input type="text" class="form-control" placeholder="ابحث عن منتج أو فاتورة... (بحث واجهة فقط)">
            </div>
        </div>


        @if($transfers->count() > 0)
            <div class="table-responsive rounded shadow-sm">
                <table class="table table-hover align-middle bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th data-bs-toggle="tooltip" title="فاتورة الشراء الأصلية">رقم الفاتورة القديمة</th>
                            <th data-bs-toggle="tooltip" title="فاتورة الشراء الجديدة">رقم الفاتورة الجديدة</th>
                            <th>المنتج الأصلي</th>
                            <th>المنتج الجديد</th>
                            <th data-bs-toggle="tooltip" title="الكمية التي كانت متاحة في الفاتورة قبل عملية النقل">الكمية قبل
                                النقل</th>
                            <th data-bs-toggle="tooltip" title="الكمية التي تم نقلها فعلياً">الكمية المنقولة</th>

                            <th data-bs-toggle="tooltip" title="كمية تم بيعها من الدفعة القديمة">المباعة من القديمة</th>
                            <th>سعر الشراء الأصلي</th>
                            <th>سعر البيع الأصلي</th>
                            <th>سعر الشراء الجديد</th>
                            <th>سعر البيع الجديد</th>
                            <th>تاريخ النقل</th>
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
                                <td><span class="text-muted">{{ number_format($transfer->old_cost_price, 2) }} ج.م</span></td>
                                <td><span class="text-muted">{{ number_format($transfer->old_selling_price, 2) }} ج.م</span></td>
                                <td><span class="text-info">{{ number_format($transfer->new_cost_price, 2) }} ج.م</span></td>
                                <td><span class="text-success">{{ number_format($transfer->new_selling_price, 2) }} ج.م</span></td>
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
                            <h5><i class="fas fa-random"></i> إجمالي النقل</h5>
                            <h3>{{ $transfers->count() }}</h3>
                            <small>عملية نقل</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-cubes"></i> إجمالي الكميات</h5>
                            <h3>{{ $transfers->sum('transferred_quantity') }}</h3>
                            <small>قطعة منقولة</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-chart-line"></i> متوسط الربح</h5>
                            <h3>{{ number_format($transfers->avg(function ($t) {
                return $t->new_selling_price - $t->new_cost_price; }), 2) }}
                                ج.م</h3>
                            <small>لكل قطعة</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body text-center">
                            <h5><i class="fas fa-coins"></i> إجمالي الربح</h5>
                            <h3>{{ number_format($transfers->sum(function ($t) {
                return ($t->new_selling_price - $t->new_cost_price) * $t->transferred_quantity; }), 2) }}
                                ج.م</h3>
                            <small>من النقل</small>
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
                <h4 class="text-muted">لا توجد عمليات نقل مسجلة</h4>
                <p class="text-muted">لم يتم نقل أي منتجات بعد أو جميع الكميات المنقولة كانت صفر.</p>
            </div>
        @endif
    </div>
@endsection