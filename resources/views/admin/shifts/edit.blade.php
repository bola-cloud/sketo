@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">إغلاق الوردية</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shifts.index') }}">الورديات</a></li>
                        <li class="breadcrumb-item active">إغلاق وردية</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card pull-up border-0 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="text-center mb-4">
                                    <div class="avatar avatar-xl bg-light-danger mb-1">
                                        <i class="la la-lock text-danger font-large-2"></i>
                                    </div>
                                    <h4 class="card-title">إنهاء وردية العمل</h4>
                                    <p class="text-muted">ملخص نشاط الوردية من {{ $shift->start_time }}</p>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 text-center">
                                        <div class="p-2 bg-light-primary rounded-lg border-0 shadow-sm">
                                            <h6 class="text-muted small mb-1 text-bold-600">الرصيد الافتتاحي</h6>
                                            <h4 class="text-primary text-bold-700">
                                                {{ number_format($shift->starting_cash, 2) }} ج.م</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="p-2 bg-light-success rounded-lg border-0 shadow-sm">
                                            <h6 class="text-muted small mb-1 text-bold-600">إجمالي المبيعات</h6>
                                            <h4 class="text-success text-bold-700">{{ number_format($totalSales, 2) }} ج.م
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="p-2 bg-light-warning rounded-lg border-0 shadow-sm">
                                            <h6 class="text-muted small mb-1 text-bold-600">الرصيد المتوقع</h6>
                                            <h4 class="text-warning text-bold-700">{{ number_format($expectedCash, 2) }} ج.م
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="ending_cash" class="text-bold-600">المبلغ الفعلي في الدرج الآن
                                                (الرصيد الختامي)</label>
                                            <div class="position-relative has-icon-left">
                                                <input type="number" step="0.01" name="ending_cash" id="ending_cash"
                                                    class="form-control round border-danger" placeholder="إبحث هنا..."
                                                    required autofocus>
                                                <div class="form-control-position">
                                                    <i class="la la-money"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">أدخل المبلغ الموجود في الدرج وقت التسليم.</small>
                                            @error('ending_cash')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="notes" class="text-bold-600">ملاحظات العجز أو الزيادة (إن
                                                وجد)</label>
                                            <textarea name="notes" id="notes" rows="3" class="form-control round"
                                                placeholder="أدخل أي ملاحظات تتعلق بالوردية..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions text-center mt-3">
                                    <button type="submit" class="btn btn-danger round px-4 shadow-lg btn-block">
                                        <i class="la la-power-off"></i> إغلاق الوردية وتسليم العهدة
                                    </button>
                                    <a href="{{ route('shifts.index') }}"
                                        class="btn btn-light round mt-1 btn-block">الرجوع</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection