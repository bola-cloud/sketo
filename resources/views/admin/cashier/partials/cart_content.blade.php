@if(!empty($cart))
    <!-- Cart Items Table -->
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="color: inherit;">
            <thead class="sticky-top" style="background: var(--p-card-bg, rgba(0,0,0,0.05));">
                <tr>
                    <th width="35%"><i class="fas fa-box me-1"></i>{{ __('app.cashier.product') }}</th>
                    <th width="25%" class="text-center"><i
                            class="fas fa-sort-numeric-up me-1"></i>{{ __('app.cashier.quantity') }}</th>
                    <th width="15%" class="text-center"><i class="fas fa-dollar-sign me-1"></i>{{ __('app.cashier.price') }}
                    </th>
                    <th width="15%" class="text-center"><i class="fas fa-calculator me-1"></i>{{ __('app.cashier.total') }}
                    </th>
                    <th width="10%" class="text-center"><i class="fas fa-cogs me-1"></i>{{ __('app.shifts.actions') }}</th>
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
                                    <div class="fw-bold">{{ $details['name'] }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>{{ $barcode }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $isService = (isset($details['type']) && $details['type'] === 'service') || str_contains($barcode, 'SRV');
                            @endphp
                            <div class="d-flex flex-column align-items-center justify-content-center quantity-controls">
                                @if($isService)
                                    <span class="fw-bold text-primary cart-qty-static fs-6" style="padding: 0.25rem 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 20px;">
                                        {{ number_format($details['quantity'], 0) }}
                                    </span>
                                @else
                                    <div class="d-flex align-items-center mb-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm me-2"
                                            onclick="updateCartQuantity('{{ $barcode }}', -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" step="0.001" class="form-control form-control-sm text-center fw-bold text-primary cart-qty-input" 
                                            value="{{ number_format($details['quantity'], 3, '.', '') }}"
                                            style="width: 80px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);"
                                            onchange="setCartQuantity('{{ $barcode }}', this.value)">
                                        <button type="button" class="btn btn-outline-success btn-sm ms-2"
                                            onclick="updateCartQuantity('{{ $barcode }}', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    
                                    @if(isset($details['is_weighted']) && $details['is_weighted'])
                                        <div class="btn-group btn-group-sm mt-1">
                                            <button type="button" class="btn btn-secondary py-0" style="font-size: 10px;" onclick="setCartQuantity('{{ $barcode }}', 0.125)">ثمن</button>
                                            <button type="button" class="btn btn-secondary py-0" style="font-size: 10px;" onclick="setCartQuantity('{{ $barcode }}', 0.250)">ربع</button>
                                            <button type="button" class="btn btn-secondary py-0" style="font-size: 10px;" onclick="setCartQuantity('{{ $barcode }}', 0.500)">نص</button>
                                            <button type="button" class="btn btn-info text-white py-0" style="font-size: 10px;" onclick="promptGramInput('{{ $barcode }}')">جرام</button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6">{{ number_format($details['price'], 2) }}
                                {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</span>
                        </td>
                        <td class="text-center">
                            <span
                                class="fw-bold text-success fs-6">{{ number_format($details['price'] * $details['quantity'], 2) }}
                                {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('cashier.removeFromCart') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="barcode" value="{{ $barcode }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('{{ __('app.cashier.confirm_remove') }}')"
                                    title="{{ __('app.cashier.remove_item') }}">
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
    <div class="border-top p-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card premium-card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-calculator me-2"></i>{{ __('app.cashier.invoice_summary') }}
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('app.cashier.subtotal_before_discount') }}:</span>
                            <span class="fw-bold">{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('app.cashier.discount') }}:</span>
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <input type="text" id="discount" name="discount" class="form-control text-center" value="0"
                                    placeholder="{{ __('app.cashier.discount_placeholder') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-success">{{ __('app.cashier.final_total') }}:</span>
                            <span class="fw-bold text-success h5" id="total_after_discount">
                                {{ number_format($subtotal - ($discount ?? 0), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card premium-card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-success">
                            <i class="fas fa-credit-card me-2"></i>{{ __('app.cashier.payment_details') }}
                        </h6>
                        <form id="checkout-form" action="{{ route('cashier.checkout') }}" method="POST">
                            @csrf
                            <!-- Client Selection -->
                            <div class="form-group mb-3">
                                <label for="client_id" class="form-label">{{ __('app.cashier.client_optional') }}</label>
                                <div class="input-group flex-nowrap">
                                    <select class="form-control select2-client" id="client_id" name="client_id">
                                        <option value="" selected>{{ __('app.cashier.select_client') }}</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#quickAddClientModal"><i class="la la-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Paid Amount -->
                            <div class="form-group mb-3">
                                <label for="paid_amount" class="form-label">{{ __('app.cashier.paid_amount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <input type="text" class="form-control @error('paid_amount') is-invalid @enderror"
                                        id="paid_amount" name="paid_amount"
                                        placeholder="{{ __('app.cashier.enter_paid_amount') }}" required
                                        value="{{ old('paid_amount', number_format($subtotal - ($discount ?? 0), 2, '.', '')) }}"
                                        @unless(auth()->user()->can('change_paid_amount')) readonly @endunless>
                                </div>
                                @error('paid_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="apply_discount_hidden" id="apply_discount_hidden" value="0" />

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>{{ __('app.cashier.complete_payment') }}
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
        <h4 class="text-muted mb-3">{{ __('app.cashier.cart_empty') }}</h4>
        <p class="text-muted mb-4">{{ __('app.cashier.start_adding') }}</p>
        <div class="d-flex justify-content-center gap-3">
            <button class="btn btn-outline-primary" onclick="$('#barcode').focus()">
                <i class="fas fa-barcode me-2"></i>{{ __('app.cashier.scan_barcode') }}
            </button>
            <button class="btn btn-outline-success" onclick="$('#product_name').focus()">
                <i class="fas fa-search me-2"></i>{{ __('app.cashier.search_name') }}
            </button>
        </div>
    </div>
@endif