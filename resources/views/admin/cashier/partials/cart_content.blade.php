@if(!empty($cart))
    <!-- Cart Items Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="bg-light sticky-top">
                <tr>
                    <th width="35%"><i class="fas fa-box me-1"></i>المنتج</th>
                    <th width="25%" class="text-center"><i class="fas fa-sort-numeric-up me-1"></i>الكمية</th>
                    <th width="15%" class="text-center"><i class="fas fa-dollar-sign me-1"></i>السعر</th>
                    <th width="15%" class="text-center"><i class="fas fa-calculator me-1"></i>الإجمالي</th>
                    <th width="10%" class="text-center"><i class="fas fa-cogs me-1"></i>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $barcode => $details)
                    <tr class="product-item">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $details['name'] }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>{{ $barcode }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center quantity-controls">
                                <button type="button" class="btn btn-outline-danger btn-sm me-2"
                                        onclick="updateCartQuantity('{{ $barcode }}', -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="bg-light px-3 py-1 rounded fw-bold text-primary">
                                    {{ $details['quantity'] }}
                                </span>
                                <button type="button" class="btn btn-outline-success btn-sm ms-2"
                                        onclick="updateCartQuantity('{{ $barcode }}', 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6">{{ number_format($details['price'], 2) }} ج.م</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-success fs-6">{{ number_format($details['price'] * $details['quantity'], 2) }} ج.م</span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('cashier.removeFromCart') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="barcode" value="{{ $barcode }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('هل أنت متأكد من إزالة هذا المنتج؟')"
                                        title="إزالة المنتج">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Cart Summary -->
    <div class="border-top bg-light p-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-white border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-calculator me-2"></i>ملخص الفاتورة
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الإجمالي قبل الخصم:</span>
                            <span class="fw-bold">{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>الخصم:</span>
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <input type="text" id="discount" name="discount"
                                       class="form-control text-center" value="0"
                                       placeholder="خصم">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-success">الإجمالي النهائي:</span>
                            <span class="fw-bold text-success h5" id="total_after_discount">
                                {{ number_format($subtotal - ($discount ?? 0), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-white border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-success">
                            <i class="fas fa-credit-card me-2"></i>تفاصيل الدفع
                        </h6>
                        <form action="{{ route('cashier.checkout') }}" method="POST">
                            @csrf
                            <!-- Client Selection -->
                            <div class="form-group mb-3">
                                <label for="client_id" class="form-label">العميل (اختياري)</label>
                                <select class="form-control select2-client" id="client_id" name="client_id">
                                    <option value="" selected>اختر العميل (اختياري)</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Paid Amount -->
                            <div class="form-group mb-3">
                                <label for="paid_amount" class="form-label">المبلغ المدفوع</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control @error('paid_amount') is-invalid @enderror"
                                           id="paid_amount"
                                           name="paid_amount"
                                           placeholder="أدخل المبلغ المدفوع"
                                           required
                                           value="{{ old('paid_amount') }}">
                                </div>
                                @error('paid_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="apply_discount_hidden" id="apply_discount_hidden" value="0" />

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>إتمام الدفع وطباعة الفاتورة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Empty Cart Message -->
    <div class="text-center p-5">
        <div class="mb-4">
            <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4 class="text-muted mb-3">عربة التسوق فارغة</h4>
        <p class="text-muted mb-4">ابدأ بإضافة المنتجات عن طريق مسح الباركود أو البحث بالاسم</p>
        <div class="d-flex justify-content-center gap-3">
            <button class="btn btn-outline-primary" onclick="$('#barcode').focus()">
                <i class="fas fa-barcode me-2"></i>مسح الباركود
            </button>
            <button class="btn btn-outline-success" onclick="$('#product_name').focus()">
                <i class="fas fa-search me-2"></i>البحث بالاسم
            </button>
        </div>
    </div>
@endif
