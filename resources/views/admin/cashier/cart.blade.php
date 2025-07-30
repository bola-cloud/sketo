@extends('layouts.admin')

@section('content')
<style>
    .cart-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .cart-panel {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .product-item:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .quantity-controls .btn {
        border-radius: 20px;
        width: 35px;
        height: 35px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cart-summary {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .search-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        border-color: #007bff;
    }

    .stats-card {
        border-radius: 15px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    /* Custom scrollbar for search results */
    #productList::-webkit-scrollbar {
        width: 8px;
    }

    #productList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #productList::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 4px;
    }

    #productList::-webkit-scrollbar-thumb:hover {
        background: #0056b3;
    }

    /* Ensure proper height constraints */
    .cart-container {
        /* max-height: calc(100vh - 60px); */
        overflow: hidden;
    }

    /* Search results specific styling */
    .search-results-container {
        overflow: hidden;
    }

    .search-results-container .card-body {
        padding: 0;
        overflow-y: auto;
    }

    #productList {
        max-height: 100%;
        overflow-y: auto;
    }

    /* Search results scroll indicator */
    .search-results-container::after {
        content: "اسحب لأسفل لمزيد من النتائج";
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 123, 255, 0.8);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }

    .search-results-container.show-scroll-hint::after {
        opacity: 1;
    }
</style>

<div class="container-fluid px-0 cart-container" style="padding-bottom: 20px;">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="text-white p-3 text-center">
                <h2 class="mb-0"><i class="fas fa-cash-register me-2"></i>نظام الكاشير - عربة التسوق</h2>
            </div>
        </div>
    </div>

    <!-- Split Screen Layout -->
    <div class="row g-0" style="height: calc(100vh - 200px); max-height: calc(100vh - 200px);">
        <!-- Left Panel - Product Search & Barcode Scanner -->
        <div class="col-md-4 border-end border-2">
            <div class="h-100 d-flex flex-column">
                <div class="card-header bg-gradient flex-shrink-0" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-search me-2"></i>البحث عن المنتجات
                    </h5>
                </div>
                <div class="flex-grow-1 p-3 bg-white" style="overflow: hidden; min-height: 0;">
                    <div class="h-100 d-flex flex-column">
                        <!-- Alert Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show flex-shrink-0 mb-2">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show flex-shrink-0 mb-2">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show flex-shrink-0 mb-2">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Barcode Scanner Section -->
                        <div class="mb-2 flex-shrink-0">
                            <div class="card border-primary">
                                <div class="card-body p-2">
                                    <h6 class="card-title text-primary mb-2">
                                        <i class="fas fa-barcode me-2"></i>مسح الباركود
                                    </h6>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                        <input type="text" class="form-control search-input"
                                               id="barcode" name="barcode"
                                               placeholder="امسح الباركود هنا..."
                                               autofocus required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Name Search Section -->
                        <div class="mb-2 flex-shrink-0">
                            <div class="card border-success">
                                <div class="card-body p-2">
                                    <h6 class="card-title text-success mb-2">
                                        <i class="fas fa-search me-2"></i>البحث بالاسم
                                    </h6>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control search-input"
                                               id="product_name" name="product_name"
                                               placeholder="ابحث عن المنتج بالاسم...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Results - Always visible container -->
                        <div id="searchResultsContainer" class="flex-grow-1 search-results-container position-relative" style="min-height: 150px; max-height: 100%;">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark py-1">
                                    <h6 class="mb-0 small">
                                        <i class="fas fa-list me-2"></i>نتائج البحث
                                        <span class="badge bg-dark ms-2" id="search-count">0</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0" style="height: calc(100% - 45px); overflow-y: auto;">
                                    <ul id="productList" class="list-group list-group-flush">
                                        <!-- Default empty state -->
                                        <li class="list-group-item text-center text-muted py-4" id="empty-search-state">
                                            <i class="fas fa-search me-2"></i>ابدأ بكتابة اسم المنتج للبحث
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-2 flex-shrink-0">
                            <div class="card border-secondary stats-card">
                                <div class="card-header bg-secondary text-white py-1">
                                    <h6 class="mb-0 small"><i class="fas fa-chart-bar me-2"></i>إحصائيات سريعة</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h6 class="text-primary mb-0" id="cart-items-count">{{ count($cart ?? []) }}</h6>
                                                <small class="text-muted">المنتجات</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-success mb-0" id="cart-total-amount">{{ number_format($subtotal ?? 0, 2) }} ج.م</h6>
                                            <small class="text-muted">الإجمالي</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Shopping Cart -->
        <div class="col-md-8">
            <div class="h-100 d-flex flex-column">
                <div class="card-header bg-gradient flex-shrink-0" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-shopping-cart me-2"></i>عربة التسوق
                        <span class="badge bg-light text-dark ms-2" id="cart-badge">{{ count($cart ?? []) }}</span>
                    </h5>
                </div>
                <div class="flex-grow-1 bg-white" style="overflow-y: auto; min-height: 0;">
                    <div id="cart-content">@include('admin.cashier.partials.cart_content')</div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

<!-- jQuery must be loaded first -->
<script src="{{ asset('assets/js/jquery.js') }}"></script>

@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

<script>
$(document).ready(function() {
    // Initialize Select2 for client dropdown
    $('.select2-client').select2({
        placeholder: "اختر العميل (اختياري)",
        allowClear: true,
        width: '100%'
    });

    // Initialize cart scripts
    initializeCartScripts();
});

// Function to update cart quantity via AJAX
function updateCartQuantity(barcode, quantityChange) {
    $.ajax({
        url: "{{ route('cashier.updateCartQuantity') }}",
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            barcode: barcode,
            quantity_change: quantityChange
        },
        success: function(response) {
            location.reload(); // Reload page to update cart
        },
        error: function(xhr) {
            alert('فشل في تحديث الكمية.');
        }
    });
}

// Function to initialize cart scripts
function initializeCartScripts() {
    // Barcode input handling with loading indicator
    let barcodeTimer;
    // Prevent form submit on Enter in barcode field
    $('#barcode').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var barcode = $(this).val().trim();
            if (barcode.length) {
                addToCartByBarcode(barcode);
            }
        }
    });
    // Always use AJAX for barcode input
    $('#barcode').off('input').on('input', function() {
        clearTimeout(barcodeTimer);
        let barcode = $(this).val().trim();
        if (barcode.length) {
            $(this).addClass('border-warning');
            barcodeTimer = setTimeout(function() {
                addToCartByBarcode(barcode);
            }, 1000);
        } else {
            $(this).removeClass('border-warning border-info border-danger');
        }
    });
    function addToCartByBarcode(barcode) {
        $('#barcode').prop('disabled', true).addClass('border-info');
        $.ajax({
            url: "{{ route('cashier.addToCart') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode
            },
            success: function(response) {
                $('#barcode').val('').prop('disabled', false).removeClass('border-info border-warning');
                // Update cart content via AJAX (no reload)
                updateCartContent();
            },
            error: function(xhr) {
                $('#barcode').prop('disabled', false).removeClass('border-info').addClass('border-danger');
                let errorMsg = 'المنتج غير موجود أو فشل في الإضافة إلى العربة.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>${errorMsg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#barcode').closest('.card-body').prepend(alertHtml);
                setTimeout(function() {
                    $('.alert').fadeOut();
                    $('#barcode').removeClass('border-danger');
                }, 5000);
            }
        });
    }

    // Product name search handling with improved UI
    $('#product_name').off('input').on('input', function() {
        var query = $(this).val().trim();

        if (query.length >= 2) {
            $.ajax({
                url: "{{ route('cashier.searchProductByName') }}",
                type: "GET",
                data: { query: query },
                success: function(data) {
                    $('#productList').empty();
                    $('#search-count').text(data.length);
                    if (data.length === 0) {
                        $('#productList').append(
                            '<li class="list-group-item text-center text-muted py-4">' +
                            '<i class="fas fa-search me-2"></i>لا توجد نتائج للبحث' +
                            '</li>'
                        );
                        $('#searchResultsContainer').removeClass('show-scroll-hint');
                    } else {
                        data.forEach(function(batch) {
                            var productName = batch.name || 'منتج غير محدد';
                            var barcode = batch.barcode;
                            var price = parseFloat(batch.price || 0);
                            var qty = batch.quantity || 0;
                            // Use JSON.stringify and slice to safely encode productName for HTML attribute
                            var safeName = JSON.stringify(productName);
                            // Remove the surrounding quotes from JSON.stringify
                            safeName = safeName.slice(1, -1).replace(/'/g, "\\'");
                            $('#productList').append(
                                '<li class="list-group-item list-group-item-action d-flex align-items-center py-3" ' +
                                'onclick="selectProduct(\'' + barcode + '\',\'' + safeName + '\')" ' +
                                'style="cursor: pointer;" title="انقر لإضافة المنتج للعربة">' +
                                '<div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" ' +
                                'style="width: 35px; height: 35px; min-width: 35px;">' +
                                '<i class="fas fa-cube fa-sm"></i>' +
                                '</div>' +
                                '<div class="flex-grow-1">' +
                                '<div class="fw-bold text-dark">' + productName + ' <span class="badge bg-secondary">' + barcode + '</span></div>' +
                                '<small class="text-muted">' +
                                '<i class="fas fa-barcode me-1"></i>' + barcode +
                                '</small>' +
                                '</div>' +
                                '<div class="text-end">' +
                                '<span class="badge bg-info">' + price.toFixed(2) + ' ج.م</span>' +
                                '<br><small class="text-muted">متوفر: ' + qty + '</small>' +
                                '</div>' +
                                '</li>'
                            );
                        });
                        if (data.length > 5) {
                            $('#searchResultsContainer').addClass('show-scroll-hint');
                            setTimeout(function() {
                                $('#searchResultsContainer').removeClass('show-scroll-hint');
                            }, 3000);
                        }
                    }
                    $('#productList').scrollTop(0);
                },
                error: function() {
                    $('#productList').empty().append(
                        '<li class="list-group-item text-center text-danger py-4">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>حدث خطأ في البحث، حاول مرة أخرى' +
                        '</li>'
                    );
                    $('#search-count').text('خطأ');
                    $('#searchResultsContainer').removeClass('show-scroll-hint');
                }
            });
        } else {
            // Reset to default empty state
            $('#productList').empty().append(
                '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
                '<i class="fas fa-search me-2"></i>ابدأ بكتابة اسم المنتج للبحث' +
                '</li>'
            );
            $('#searchResultsContainer').removeClass('show-scroll-hint');
            $('#search-count').text('0');
        }
    });    // Hide search results when clicking outside (reset to empty state)
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#product_name, #searchResultsContainer').length) {
            if ($('#product_name').val().trim() === '') {
                $('#productList').empty().append(
                    '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
                    '<i class="fas fa-search me-2"></i>ابدأ بكتابة اسم المنتج للبحث' +
                    '</li>'
                );
                $('#search-count').text('0');
            }
        }
    });

    // Discount input handling with real-time calculation
    $('#discount').off('input').on('input', function() {
        var discount = parseFloat($(this).val()) || 0;
        var subtotal = {{ isset($subtotal) && is_numeric($subtotal) ? $subtotal : 0 }};
        var totalAfterDiscount = Math.max(0, subtotal - discount); // Ensure non-negative

        $('#total_after_discount').text(totalAfterDiscount.toFixed(2) + ' ج.م');
        $('#apply_discount_hidden').val(discount);

        // Visual feedback for discount validation
        if (discount > subtotal) {
            $(this).addClass('border-warning');
        } else {
            $(this).removeClass('border-warning');
        }
    });

    // Auto-focus barcode input when page loads
    setTimeout(function() {
        $('#barcode').focus();
    }, 500);
}

// Function to select a product from search results
function selectProduct(barcode, name) {
    // Add to cart by batch if batchId is provided, else by barcode
    if (!barcode) {
        alert('باركود المنتج غير صحيح');
        return;
    }
    // Add to cart directly via AJAX using product barcode
    $('#product_name').val('');
    $('#barcode').val('');
    $('#productList').empty().append(
        '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
        '<i class="fas fa-search me-2"></i>ابدأ بكتابة اسم المنتج للبحث' +
        '</li>'
    );
    $('#search-count').text('0');
    $('#barcode').addClass('border-success');
    $.ajax({
        url: "{{ route('cashier.addToCart') }}",
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            barcode: barcode // This is the product barcode
        },
        success: function(response) {
            $('#barcode').val('').removeClass('border-success');
            // Update cart content via AJAX (no reload)
            updateCartContent();
        },
        error: function(xhr) {
            $('#barcode').removeClass('border-success').addClass('border-danger');
            let errorMsg = 'فشل في إضافة المنتج إلى الفاتورة.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert(errorMsg);
            setTimeout(function() {
                $('#barcode').removeClass('border-danger');
            }, 3000);
        }
    });
}

// Update cart content via AJAX (no reload)
function updateCartContent() {
    $.ajax({
        url: "{{ route('cashier.cartContent') }}",
        type: "GET",
        success: function(html) {
            $('#cart-content').html(html);
            // Update cart badge and stats
            let itemsCount = $('#cart-content').find('#cart-badge').text() || '0';
            let totalAmount = $('#cart-content').find('#cart-total-amount').text() || '0';
            $('#cart-badge').text(itemsCount);
            $('#cart-items-count').text(itemsCount);
            $('#cart-total-amount').text(totalAmount);
        },
        error: function() {
            // fallback: reload if AJAX fails
            location.reload();
        }
    });
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // F1 - Focus barcode input
    if (e.which === 112) { // F1
        e.preventDefault();
        $('#barcode').focus();
    }
    // F2 - Focus product search
    else if (e.which === 113) { // F2
        e.preventDefault();
        $('#product_name').focus();
    }
    // F3 - Focus paid amount
    else if (e.which === 114) { // F3
        e.preventDefault();
        $('#paid_amount').focus();
    }
});
</script>
@endpush
