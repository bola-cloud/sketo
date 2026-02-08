@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('app.returns.edit_title') }}: {{ $customerReturn->id }}</h4>
                    <div class="heading-elements">
                        <a href="{{ route('customer-returns.show', $customerReturn) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-right"></i> {{ __('app.common.back_to_details') }}
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
                                            <h5>{{ __('app.returns.info_readonly') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ __('app.reports.invoice_code') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->invoice->invoice_code }}" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.clients.name') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->invoice->buyer_name }}" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.products.product') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->product->name }}" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.returns.qty_returned') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->quantity_returned }}" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.returns.return_amount') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ number_format($customerReturn->return_amount, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}"
                                                    readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.returns.return_date') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->created_at->format('Y-m-d H:i:s') }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Editable Fields -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('app.returns.editable_fields') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="reason">{{ __('app.returns.return_reason') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="reason" id="reason" class="form-control" required>
                                                    <option value="">{{ __('app.returns.select_reason') }}</option>
                                                    <option value="عيب في المنتج" {{ $customerReturn->reason == 'عيب في المنتج' ? 'selected' : '' }}>{{ __('app.returns.reason_defect') }}
                                                    </option>
                                                    <option value="منتج غير مطابق للمواصفات" {{ $customerReturn->reason == 'منتج غير مطابق للمواصفات' ? 'selected' : '' }}>{{ __('app.returns.reason_not_match') }}</option>
                                                    <option value="منتج منتهي الصلاحية" {{ $customerReturn->reason == 'منتج منتهي الصلاحية' ? 'selected' : '' }}>
                                                        {{ __('app.returns.reason_expired') }}</option>
                                                    <option value="طلب العميل" {{ $customerReturn->reason == 'طلب العميل' ? 'selected' : '' }}>{{ __('app.returns.reason_customer_request') }}
                                                    </option>
                                                    <option value="خطأ في الفاتورة" {{ $customerReturn->reason == 'خطأ في الفاتورة' ? 'selected' : '' }}>
                                                        {{ __('app.returns.reason_invoice_error') }}</option>
                                                    <option value="أخرى" {{ $customerReturn->reason == 'أخرى' ? 'selected' : '' }}>{{ __('app.returns.reason_other') }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="status">{{ __('app.returns.return_status') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="pending" {{ $customerReturn->status == 'pending' ? 'selected' : '' }}>{{ __('app.common.status_pending') }}</option>
                                                    <option value="completed" {{ $customerReturn->status == 'completed' ? 'selected' : '' }}>{{ __('app.common.status_completed') }}</option>
                                                    <option value="cancelled" {{ $customerReturn->status == 'cancelled' ? 'selected' : '' }}>{{ __('app.common.status_cancelled') }}</option>
                                                </select>
                                                <small class="form-text text-muted">
                                                    {{ __('app.returns.cancel_warning') }}
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('app.returns.user_returned') }}</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $customerReturn->user->name }}" readonly>
                                            </div>

                                            <div class="alert alert-warning">
                                                <h6>{{ __('app.returns.important_note') }}</h6>
                                                <p class="mb-0">{{ __('app.returns.edit_note') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> {{ __('app.common.save_changes') }}
                                </button>
                                <a href="{{ route('customer-returns.show', $customerReturn) }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> {{ __('app.common.cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection