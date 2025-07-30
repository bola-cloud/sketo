@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تعديل المرتجع رقم: {{ $customerReturn->id }}</h4>
                <div class="heading-elements">
                    <a href="{{ route('customer-returns.show', $customerReturn) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للتفاصيل
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer-returns.update', $customerReturn) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Return Information (Read-only) -->
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5>معلومات المرتجع (غير قابلة للتعديل)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>رقم الفاتورة</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->invoice->invoice_code }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>اسم العميل</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->invoice->buyer_name }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>المنتج</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->product->name }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>الكمية المرتجعة</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->quantity_returned }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>مبلغ الإرجاع</label>
                                            <input type="text" class="form-control" value="{{ number_format($customerReturn->return_amount, 2) }} ج.م" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>تاريخ الإرجاع</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->created_at->format('Y-m-d H:i:s') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Editable Fields -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>الحقول القابلة للتعديل</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="reason">سبب الإرجاع <span class="text-danger">*</span></label>
                                            <select name="reason" id="reason" class="form-control" required>
                                                <option value="">-- اختر السبب --</option>
                                                <option value="عيب في المنتج" {{ $customerReturn->reason == 'عيب في المنتج' ? 'selected' : '' }}>عيب في المنتج</option>
                                                <option value="منتج غير مطابق للمواصفات" {{ $customerReturn->reason == 'منتج غير مطابق للمواصفات' ? 'selected' : '' }}>منتج غير مطابق للمواصفات</option>
                                                <option value="منتج منتهي الصلاحية" {{ $customerReturn->reason == 'منتج منتهي الصلاحية' ? 'selected' : '' }}>منتج منتهي الصلاحية</option>
                                                <option value="طلب العميل" {{ $customerReturn->reason == 'طلب العميل' ? 'selected' : '' }}>طلب العميل</option>
                                                <option value="خطأ في الفاتورة" {{ $customerReturn->reason == 'خطأ في الفاتورة' ? 'selected' : '' }}>خطأ في الفاتورة</option>
                                                <option value="أخرى" {{ $customerReturn->reason == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">حالة المرتجع <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="pending" {{ $customerReturn->status == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                                <option value="completed" {{ $customerReturn->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                                <option value="cancelled" {{ $customerReturn->status == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                تحذير: تغيير الحالة إلى "ملغية" قد يؤثر على المخزون
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label>المستخدم الذي قام بالإرجاع</label>
                                            <input type="text" class="form-control" value="{{ $customerReturn->user->name }}" readonly>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6>ملاحظة مهمة:</h6>
                                            <p class="mb-0">يمكن تعديل سبب الإرجاع والحالة فقط. لتعديل الكمية أو المنتج، يجب إنشاء مرتجع جديد وإلغاء هذا المرتجع.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('customer-returns.show', $customerReturn) }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
