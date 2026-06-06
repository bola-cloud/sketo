@extends('layouts.admin')

@section('content')
    <style>
        /* ===== Cart Page Theme Support ===== */
        .cart-container {
            overflow: hidden;
        }

        .cart-panel {
            backdrop-filter: blur(10px);
        }

        /* Dynamic Panel Backgrounds */
        .cart-panel-body {
            background-color: var(--p-card-bg, rgba(30, 41, 59, 0.6));
            color: var(--p-text, #e2e8f0);
        }
        body.light-mode .cart-panel-body {
            background-color: #ffffff;
            color: #0f172a;
        }

        /* Override Bootstrap .card inside cart for dark mode */
        .cart-panel-body .card {
            background: rgba(255,255,255,0.05) !important;
            border-color: rgba(255,255,255,0.1) !important;
            color: var(--p-text, #e2e8f0);
        }
        .cart-panel-body .card .card-header {
            border-bottom-color: rgba(255,255,255,0.08) !important;
        }
        .cart-panel-body .card .card-body {
            color: var(--p-text, #e2e8f0);
        }
        .cart-panel-body .card-title {
            color: inherit !important;
        }
        .cart-panel-body .form-control,
        .cart-panel-body .input-group-text {
            background: rgba(255,255,255,0.08) !important;
            border-color: rgba(255,255,255,0.15) !important;
            color: var(--p-text, #e2e8f0) !important;
        }
        .cart-panel-body .form-control::placeholder {
            color: var(--p-text-muted, #94a3b8) !important;
        }
        .cart-panel-body .list-group-item {
            background: transparent !important;
            border-color: rgba(255,255,255,0.06) !important;
            color: var(--p-text, #e2e8f0) !important;
        }
        .cart-panel-body .text-muted {
            color: var(--p-text-muted, #94a3b8) !important;
        }
        .cart-panel-body .text-dark {
            color: var(--p-text, #e2e8f0) !important;
        }

        /* Light mode: restore normal Bootstrap look */
        body.light-mode .cart-panel-body .card {
            background: #fff !important;
            border-color: rgba(0,0,0,0.1) !important;
            color: #0f172a;
        }
        body.light-mode .cart-panel-body .card .card-body {
            color: #0f172a;
        }
        body.light-mode .cart-panel-body .form-control,
        body.light-mode .cart-panel-body .input-group-text {
            background: #fff !important;
            border-color: #ced4da !important;
            color: #212529 !important;
        }
        body.light-mode .cart-panel-body .form-control::placeholder {
            color: #6c757d !important;
        }
        body.light-mode .cart-panel-body .list-group-item {
            background: #fff !important;
            border-color: rgba(0,0,0,0.08) !important;
            color: #212529 !important;
        }
        body.light-mode .cart-panel-body .text-muted {
            color: #6c757d !important;
        }
        body.light-mode .cart-panel-body .text-dark {
            color: #212529 !important;
        }

        .product-item:hover {
            background-color: rgba(99, 102, 241, 0.08) !important;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        body.light-mode .product-item:hover {
            background-color: #f8f9fa !important;
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

        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            border-color: #007bff;
        }

        .stats-card {
            border-radius: 15px;
        }

        /* Custom scrollbar for search results */
        #productList::-webkit-scrollbar { width: 8px; }
        #productList::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 4px; }
        #productList::-webkit-scrollbar-thumb { background: var(--p-indigo, #6366f1); border-radius: 4px; }

        /* Search results specific styling */
        .search-results-container { overflow: hidden; }
        .search-results-container .card-body { padding: 0; overflow-y: auto; }
        #productList { max-height: 100%; overflow-y: auto; }

        /* Search results scroll indicator */
        .search-results-container::after {
            content: "{{ __('app.cashier.scroll_more_results') }}";
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
        .search-results-container.show-scroll-hint::after { opacity: 1; }

        /* ===== Mobile Responsive ===== */
        @media (max-width: 767.98px) {
            /* Remove fixed height, allow natural stacking */
            .cart-split-row {
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
            }
            .cart-split-row > .col-md-4,
            .cart-split-row > .col-md-8 {
                height: auto !important;
                min-height: auto !important;
            }
            .cart-split-row > .col-md-4 .h-100,
            .cart-split-row > .col-md-8 .h-100 {
                height: auto !important;
            }
            /* Show cart panel first on mobile */
            .cart-split-row > .col-md-8 { order: -1; margin-bottom: 8px; }
            .cart-split-row > .col-md-4 { order: 1; border: none !important; }

            /* Compact container */
            .cart-container { padding-bottom: 8px !important; }

            /* Kill the big header on mobile */
            .cart-container > .row.mb-3 { margin-bottom: 4px !important; }
            .cart-container > .row.mb-3 .p-3 { padding: 6px 8px !important; }
            .cart-container > .row.mb-3 h2 { font-size: 1rem !important; margin: 0 !important; }

            /* Compact panel headers */
            .cart-split-row .card-header { padding: 6px 10px !important; }
            .cart-split-row .card-header h5 { font-size: 0.85rem !important; }

            /* Tight panel body */
            .cart-panel-body { padding: 8px !important; min-height: 0 !important; }

            /* Compact cards inside panels */
            .cart-panel-body .card { margin-bottom: 6px !important; }
            .cart-panel-body .card .card-body { padding: 8px !important; }
            .cart-panel-body .card .card-header { padding: 4px 8px !important; }
            .cart-panel-body .card h6 { font-size: 0.8rem !important; margin-bottom: 4px !important; }

            /* Shrink search results container drastically */
            .search-results-container { min-height: 150px !important; margin-bottom: 6px !important; }

            /* Compact input groups */
            .cart-panel-body .input-group { margin-bottom: 0 !important; }
            .cart-panel-body .mb-2 { margin-bottom: 6px !important; }

            /* Compact stats card */
            .stats-card { margin-bottom: 8px !important; }
            .stats-card .card-body { padding: 6px !important; }
            .stats-card h6 { font-size: 0.85rem !important; }

            /* Compact table */
            .cart-panel-body .table th,
            .cart-panel-body .table td { padding: 6px 4px !important; font-size: 0.75rem !important; }
            .cart-panel-body .table .badge { font-size: 0.7rem !important; }

            /* Compact cart summary section */
            .cart-panel-body .border-top { padding: 8px !important; }
            .cart-panel-body .border-top .card { margin-bottom: 8px !important; }
            .cart-panel-body .border-top .card-body { padding: 8px !important; }
            .cart-panel-body .border-top h6 { font-size: 0.8rem !important; }

            /* Quantity controls smaller */
            .quantity-controls .btn { width: 28px !important; height: 28px !important; }

            /* Product icon smaller */
            .cart-panel-body .rounded-circle { width: 30px !important; height: 30px !important; min-width: 30px !important; }

            /* Form controls compact */
            .cart-panel-body .form-control { font-size: 0.8rem !important; padding: 4px 8px !important; }
            .cart-panel-body .form-label { font-size: 0.75rem !important; margin-bottom: 2px !important; }
            .cart-panel-body .btn-success.w-100 { padding: 8px !important; font-size: 0.85rem !important; }
        }
    </style>

    <div class="container-fluid px-0 cart-container" style="padding-bottom: 20px;">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="text-white p-3 text-center">
                    <h2 class="mb-0"><i class="fas fa-cash-register me-2"></i>{{ __('app.cashier.cart_title') }}</h2>
                </div>
            </div>
        </div>

        <!-- Split Screen Layout -->
        <div class="row g-0 cart-split-row" style="height: calc(100vh - 130px); max-height: calc(100vh - 130px); overflow: hidden;">
            <!-- Left Panel - Product Search & Barcode Scanner -->
            <div class="col-md-4 border-end border-2">
                <div class="h-100 d-flex flex-column">
                    <div class="card-header bg-gradient flex-shrink-0"
                        style="background: linear-gradient(135deg, #007bff, #0056b3);">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-search me-2"></i>{{ __('app.cashier.product_search') }}
                        </h5>
                    </div>
                    <div class="flex-grow-1 p-3 cart-panel-body" style="overflow-y: auto; min-height: 0;">
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
                                            <i class="fas fa-barcode me-2"></i>{{ __('app.cashier.barcode_scanner') }}
                                        </h6>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-barcode"></i>
                                            </span>
                                            <input type="text" class="form-control search-input" id="barcode" name="barcode"
                                                placeholder="{{ __('app.cashier.scan_barcode_placeholder') }}" autofocus
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Name Search Section -->
                            <div class="mb-2 flex-shrink-0">
                                <div class="card border-success">
                                    <div class="card-body p-2">
                                        <h6 class="card-title text-success mb-2">
                                            <i class="fas fa-search me-2"></i>{{ __('app.cashier.search_by_name') }}
                                        </h6>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control search-input" id="product_name"
                                                name="product_name"
                                                placeholder="{{ __('app.cashier.search_name_placeholder') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Results - Always visible container -->
                            <div id="searchResultsContainer"
                                class="flex-shrink-0 search-results-container position-relative mb-3"
                                style="min-height: 400px; overflow: hidden;">
                                <div class="card border-warning h-100">
                                    <div class="card-header bg-warning text-dark py-1">
                                        <h6 class="mb-0 small">
                                            <i class="fas fa-list me-2"></i>{{ __('app.cashier.search_results') }}
                                            <span class="badge bg-dark ms-2" id="search-count">0</span>
                                        </h6>
                                    </div>
                                    <div class="card-body p-0 d-flex flex-column"
                                        style="height: calc(100% - 35px); overflow: hidden;">
                                        <ul id="productList" class="list-group list-group-flush flex-grow-1"
                                            style="overflow-y: auto;">
                                            <!-- Default empty state -->
                                            <li class="list-group-item text-center text-muted py-4" id="empty-search-state">
                                                <i class="fas fa-search me-2"></i>{{ __('app.cashier.start_typing') }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-2 flex-shrink-0">
                                <div class="card border-secondary stats-card mb-5">
                                    <div class="card-header bg-secondary text-white py-1">
                                        <h6 class="mb-0 small"><i
                                                class="fas fa-chart-bar me-2"></i>{{ __('app.cashier.quick_stats') }}</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <h6 class="text-primary mb-0" id="cart-items-count">
                                                        {{ count($cart ?? []) }}
                                                    </h6>
                                                    <small class="text-muted">{{ __('app.cashier.products_count') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-success mb-0" id="cart-total-amount">
                                                    {{ number_format($subtotal ?? 0, 2) }}
                                                    {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}
                                                </h6>
                                                <small class="text-muted">{{ __('app.cashier.total_amount') }}</small>
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
                    <div class="card-header bg-gradient flex-shrink-0"
                        style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-shopping-cart me-2"></i>{{ __('app.cashier.shopping_cart') }}
                            <span class="badge bg-light text-dark ms-2" id="cart-badge">{{ count($cart ?? []) }}</span>
                        </h5>
                    </div>
                    <div class="flex-grow-1 cart-panel-body" style="overflow-y: auto; min-height: 0;">
                        <div id="cart-content">@include('admin.cashier.partials.cart_content')</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('modals')
    <!-- Quick Add Client Modal -->
    <div class="modal fade text-left" id="quickAddClientModal" tabindex="-1" role="dialog" aria-labelledby="quickAddClientModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="quickAddClientModalLabel">
                        <i class="fas fa-user-plus me-2"></i>{{ __('app.cashier.add_client') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quickAddClientForm">
                    @csrf
                    <div class="modal-body">
                        <p>{{ __('app.cashier.add_client_desc') }}</p>
                        <fieldset class="form-group mb-2">
                            <label for="client_name">{{ __('app.clients.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="client_name" name="name" required placeholder="{{ __('app.clients.name') }}...">
                        </fieldset>
                        <fieldset class="form-group mb-2">
                            <label for="client_phone">{{ __('app.clients.phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="client_phone" name="phone" required placeholder="{{ __('app.clients.phone') }}...">
                        </fieldset>
                        <fieldset class="form-group mb-2">
                            <label for="client_address">{{ __('app.clients.address') }}</label>
                            <textarea class="form-control" id="client_address" name="address" rows="2" placeholder="{{ __('app.clients.address') }}..."></textarea>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">{{ __('app.common.close') }}</button>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-save me-1"></i>{{ __('app.common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

<!-- jQuery must be loaded first -->
<script src="{{ asset('assets/js/jquery.js') }}"></script>

@push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

    <script>
        $(document).ready(function () {
            // Global AJAX error handling for CSRF/Session expiration
            $(document).ajaxError(function (event, xhr, settings) {
                if (xhr.status === 419 || (xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message.includes('CSRF'))) {
                    alert("انتهت صلاحية الجلسة أو رمز الحماية (CSRF). سيتم إعادة تحميل الصفحة لتحديث الجلسة تلقائياً.");
                    location.reload();
                }
            });

            // Initialize Select2 for client dropdown
            $('.select2-client').select2({
                placeholder: "{{ __('app.cashier.select_client') }}",
                allowClear: true,
                width: '100%'
            });

            // Initialize cart scripts
            initializeCartScripts();
        });

        // Global handler for offline success (called by SketoSync)
        window.showOfflineSuccess = function () {
            // Show a nice glassmorphism alert
            const alertHtml = `
                        <div class="position-fixed top-0 start-50 translate-middle-x mt-4 z-index-2000">
                            <div class="alert bg-glass-dark text-white border-info shadow-lg p-4 rounded-xl text-center" style="backdrop-filter: blur(20px);">
                                <i class="la la-cloud-download font-large-2 text-info mb-2 d-block"></i>
                                <h4>{{ __('app.cashier.saved_offline') }}</h4>
                                <p class="mb-0 text-white-50">{{ __('app.cashier.offline_sync_desc') }}</p>
                            </div>
                        </div>
                    `;
            $('body').append(alertHtml);

            setTimeout(() => {
                location.reload(); // Clear cart and reset UI
            }, 3000);
        };

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
                success: function (response) {
                    updateCartContent(); // Update cart content via AJAX
                },
                error: function (xhr) {
                    alert('{{ __('app.cashier.update_fail') }}');
                }
            });
        }

        // Function to set absolute cart quantity via AJAX
        function setCartQuantity(barcode, absoluteQuantity) {
            $.ajax({
                url: "{{ route('cashier.updateCartQuantity') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    barcode: barcode,
                    absolute_quantity: absoluteQuantity
                },
                success: function (response) {
                    updateCartContent(); // Update cart content via AJAX
                },
                error: function (xhr) {
                    alert('{{ __('app.cashier.update_fail') }}');
                }
            });
        }

        // Function to prompt for grams and convert to Kg
        function promptGramInput(barcode) {
            let grams = prompt("{{ __('app.cashier.prompt_gram_input') }}", "");
            if (grams !== null && grams !== "") {
                let kg = parseFloat(grams) / 1000;
                if (!isNaN(kg)) {
                    setCartQuantity(barcode, kg);
                } else {
                    alert("{{ __('app.cashier.prompt_valid_number') }}");
                }
            }
        }

        // Function to initialize cart scripts
        function initializeCartScripts() {
            // Barcode input handling with loading indicator
            let barcodeTimer;
            // Prevent form submit on Enter in barcode field
            $('#barcode').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var barcode = $(this).val().trim();
                    if (barcode.length) {
                        addToCartByBarcode(barcode);
                    }
                }
            });
            // Always use AJAX for barcode input
            $('#barcode').off('input').on('input', function () {
                clearTimeout(barcodeTimer);
                let barcode = $(this).val().trim();
                if (barcode.length) {
                    $(this).addClass('border-warning');
                    barcodeTimer = setTimeout(function () {
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
                    success: function (response) {
                        $('#barcode').val('').prop('disabled', false).removeClass('border-info border-warning');
                        // Update cart content via AJAX (no reload)
                        updateCartContent();
                    },
                    error: function (xhr) {
                        $('#barcode').prop('disabled', false).removeClass('border-info').addClass('border-danger');
                        let errorMsg = '{{ __('app.cashier.product_not_found') }}';
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
                        setTimeout(function () {
                            $('.alert').fadeOut();
                            $('#barcode').removeClass('border-danger');
                        }, 5000);
                    }
                });
            }

            // Product name search handling with improved UI
            $('#product_name').off('input').on('input', async function () {
                var query = $(this).val().trim();

                if (query.length >= 2) {
                    if (SketoSync && !SketoSync.isOnline) {
                        // Offline Search using Dexie
                        const results = await SketoSync.db.products
                            .where('name').startsWithIgnoreCase(query)
                            .or('barcode').startsWithIgnoreCase(query)
                            .toArray();
                        renderSearchResults(results);
                        return;
                    }

                    $.ajax({
                        url: "{{ route('cashier.searchProductByName') }}",
                        type: "GET",
                        data: { query: query },
                        success: function (data) {
                            renderSearchResults(data);
                        },
                        error: function () {
                            $('#productList').empty().append(
                                '<li class="list-group-item text-center text-danger py-4">' +
                                '<i class="fas fa-exclamation-triangle me-2"></i>{{ __('app.cashier.search_error') }}' +
                                '</li>'
                            );
                            $('#search-count').text('Error');
                            $('#searchResultsContainer').removeClass('show-scroll-hint');
                        }
                    });
                } else {
                    // Reset to default empty state
                    $('#productList').empty().append(
                        '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
                        '<i class="fas fa-search me-2"></i>{{ __('app.cashier.start_typing') }}' +
                        '</li>'
                    );
                    $('#searchResultsContainer').removeClass('show-scroll-hint');
                    $('#search-count').text('0');
                }
            });

            function renderSearchResults(data) {
                $('#productList').empty();
                $('#search-count').text(data.length);
                if (data.length === 0) {
                    $('#productList').append(
                        '<li class="list-group-item text-center text-muted py-4">' +
                        '<i class="fas fa-search me-2"></i>{{ __('app.cashier.no_results') }}' +
                        '</li>'
                    );
                    $('#searchResultsContainer').removeClass('show-scroll-hint');
                } else {
                    data.forEach(function (batch) {
                        var productName = batch.name || '{{ __('app.cashier.undefined_product') }}';
                        var barcode = batch.barcode;
                        var price = parseFloat(batch.price || 0);
                        var qty = batch.quantity || 0;
                        var safeName = JSON.stringify(productName);
                        safeName = safeName.slice(1, -1).replace(/'/g, "\\'");
                        $('#productList').append(
                            '<li class="list-group-item list-group-item-action d-flex align-items-center py-3" ' +
                            'onclick="selectProduct(\'' + barcode + '\',\'' + safeName + '\')" ' +
                            'style="cursor: pointer;" title="{{ __('app.cashier.click_to_add') }}">' +
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
                            '<span class="badge bg-info">' + price.toFixed(2) + ' {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}</span>' +
                            '<br><small class="text-muted">{{ __('app.cashier.available') }}: ' + qty + '</small>' +
                            '</div>' +
                            '</li>'
                        );
                    });
                    if (data.length > 5) {
                        $('#searchResultsContainer').addClass('show-scroll-hint');
                        setTimeout(function () {
                            $('#searchResultsContainer').removeClass('show-scroll-hint');
                        }, 3000);
                    }
                }
                $('#productList').scrollTop(0);
            }
            // Hide search results when clicking outside (reset to empty state)
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#product_name, #searchResultsContainer').length) {
                    if ($('#product_name').val().trim() === '') {
                        $('#productList').empty().append(
                            '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
                            '<i class="fas fa-search me-2"></i>{{ __('app.cashier.start_typing') }}' +
                            '</li>'
                        );
                        $('#search-count').text('0');
                    }
                }
            });

            // Discount input handling with real-time calculation
            $('#discount').off('input').on('input', function () {
                var discount = parseFloat($(this).val()) || 0;
                // Get subtotal dynamically from the UI instead of static PHP variable
                var subtotalText = $('#cart-content').find('span:contains("{{ __("app.cashier.subtotal_before_discount") }}")').next().text() || '0';
                var subtotal = parseFloat(subtotalText.replace(/[^\d.]/g, '')) || 0;
                
                var totalAfterDiscount = Math.max(0, subtotal - discount); // Ensure non-negative

                $('#total_after_discount').text(totalAfterDiscount.toFixed(2));
                $('#apply_discount_hidden').val(discount);
                
                // Real-time update for paid amount to match final total
                $('#paid_amount').val(totalAfterDiscount.toFixed(2));

                // Visual feedback for discount validation
                if (discount > subtotal) {
                    $(this).addClass('border-warning');
                } else {
                    $(this).removeClass('border-warning');
                }
            });

            // Auto-focus barcode input when page loads
            setTimeout(function () {
                $('#barcode').focus();
            }, 500);
        }

        // Function to select a product from search results
        function selectProduct(barcode, name) {
            // Add to cart by batch if batchId is provided, else by barcode
            if (!barcode) {
                alert('{{ __('app.cashier.invalid_barcode') }}');
                return;
            }
            // Add to cart directly via AJAX using product barcode
            $('#product_name').val('');
            $('#barcode').val('');
            $('#productList').empty().append(
                '<li class="list-group-item text-center text-muted py-4" id="empty-search-state">' +
                '<i class="fas fa-search me-2"></i>{{ __('app.cashier.start_typing') }}' +
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
                success: function (response) {
                    $('#barcode').val('').removeClass('border-success');
                    // Update cart content via AJAX (no reload)
                    updateCartContent();
                },
                error: function (xhr) {
                    $('#barcode').removeClass('border-success').addClass('border-danger');
                    let errorMsg = '{{ __('app.cashier.add_fail') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                    setTimeout(function () {
                        $('#barcode').removeClass('border-danger');
                    }, 3000);
                }
            });
        }

        // Update cart content via AJAX (no reload)
        function updateCartContent(newClientId = null) {
            // Preserve current state before update
            let selectedClient = newClientId || $('#client_id').val();
            let currentDiscount = $('#discount').val();

            $.ajax({
                url: "{{ route('cashier.cartContent') }}",
                type: "GET",
                success: function (html) {
                    $('#cart-content').html(html);
                    
                    // Restore state
                    if (selectedClient) {
                        // Using a timeout to ensure the new HTML is fully parsed and Select2 initialized
                        setTimeout(() => {
                            $('#client_id').val(selectedClient).trigger('change');
                        }, 100);
                    }
                    if (currentDiscount) {
                        $('#discount').val(currentDiscount);
                        // Trigger input to recalculate totals
                        setTimeout(() => $('#discount').trigger('input'), 100);
                    }

                    // Re-initialize scripts to attach listeners to new elements
                    initializeCartScripts();
                    
                    // Update cart badge and stats
                    let itemsCount = $('#cart-badge-val').text() || '0';
                    if(!itemsCount || itemsCount == '0') {
                        itemsCount = $('#cart-content').find('.product-item').length || '0';
                    }
                    
                    $('#cart-badge').text(itemsCount);
                    $('#cart-items-count').text(itemsCount);
                    
                    // Update total from the new HTML
                    let totalVal = $('#total_after_discount').text() || '0';
                    $('#cart-total-amount').text(totalVal + ' {{ auth()->check() ? (auth()->user()->vendor->currency ?? 'ج.م') : 'ج.م' }}');
                },
                error: function () {
                    // fallback: reload if AJAX fails
                    location.reload();
                }
            });
        }

        // Quick Add Client AJAX Submission
        $(document).on('submit', '#quickAddClientForm', function(e) {
            e.preventDefault();
            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            let originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> ' + "{{ __('app.cashier.adding_client') }}");
            
            $.ajax({
                url: "{{ route('clients.store') }}", // Using standard clients store route
                type: "POST",
                data: form.serialize() + '&ajax=true',
                success: function(response) {
                    // Assuming response returns the new client id and name
                    $('#quickAddClientModal').modal('hide');
                    form.trigger('reset');
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    // Refresh cart content to fetch updated clients list and auto-select new client
                    let newClientId = response.client ? response.client.id : null;
                    updateCartContent(newClientId);
                    
                    alert("{{ __('app.cashier.client_added_success') }}");
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalText);
                    let errorMsg = "{{ __('app.cashier.client_added_fail') }}";
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        });

        // Keyboard shortcuts
        $(document).keydown(function (e) {
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
            // F10 - Complete Payment & Print
            else if (e.which === 121) { // F10
                e.preventDefault();
                if ($('#checkout-form').length) {
                    $('#checkout-form').submit();
                }
            }
        });
    </script>
@endpush