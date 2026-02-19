@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h2 class="mb-0 font-weight-bold text-gradient-premium">{{ __('app.platform.super_dashboard') }}
                        </h2>
                        <p class="text-muted opacity-75 mb-0">{{ __('app.platform.platform_overview') }}</p>
                    </div>
                    <div>
                        <button class="btn bg-glass border-glass text-white shadow-glow">
                            <i class="la la-calendar mr-1"></i> {{ date('F Y') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Stats -->
        <div class="row animate-fade-in-up">
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('super-admin.vendors.index') }}" class="text-decoration-none">
                    <div class="premium-card h-100" style="--card-accent: var(--p-indigo);">
                        <div class="card-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--p-indigo);">
                            <i class="la la-store"></i>
                        </div>
                        <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.sidebar.vendors') }}</p>
                        <h2 class="font-weight-bold mb-0 text-white">{{ $totalVendors }}</h2>
                        <div class="mt-3">
                            <span class="text-success small"><i class="la la-arrow-up"></i> Platform Growth</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="premium-card h-100" style="--card-accent: var(--p-emerald);">
                    <div class="card-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: var(--p-emerald);">
                        <i class="la la-wallet"></i>
                    </div>
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.platform.total_revenue') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">
                        {{ number_format($totalRevenue, 2) }}
                    </h2>
                    <div class="mt-3">
                        <span class="text-success small"><i class="la la-check-circle"></i> Verified Income</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="premium-card h-100" style="--card-accent: var(--p-amber); --bg-accent: #fffbeb;">
                    <div class="card-icon-wrapper">
                        <i class="la la-users"></i>
                    </div>
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.sidebar.users') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">{{ $totalUsers }}</h2>
                    <div class="mt-3">
                        <span class="text-primary small"><i class="la la-user-check"></i> Across all stores</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="premium-card h-100" style="--card-accent: var(--p-rose); --bg-accent: #fff1f2;">
                    <div class="card-icon-wrapper">
                        <i class="la la-box"></i>
                    </div>
                    <p class="text-muted small font-weight-bold mb-1 uppercase">{{ __('app.sidebar.products') }}</p>
                    <h2 class="font-weight-bold mb-0 text-white">{{ $totalProducts }}</h2>
                    <div class="mt-3">
                        <span class="text-muted small">Global Inventory</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Search & Top Rankings -->
        <div class="row animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-lg-8">
                <div class="premium-card mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light-primary p-3 rounded-circle mr-3">
                            <i class="la la-search text-primary h4 mb-0"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">{{ __('app.platform.vendor_search') }}</h5>
                    </div>
                    <div class="form-group mb-0">
                        <select id="vendor-search" class="form-control select2" style="width: 100%;">
                            <option value="">{{ __('app.platform.search_store_placeholder') }}</option>
                        </select>
                    </div>
                </div>

                <div class="premium-card p-0 overflow-hidden mb-4">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold mb-0">{{ __('app.platform.recent_vendors') }}</h5>
                        <a href="{{ route('super-admin.vendors.index') }}" class="btn btn-sm btn-light-primary px-3"
                            style="border-radius: 8px;">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="border-0 px-4 py-3">{{ __('app.platform.store_name') }}</th>
                                    <th class="border-0">{{ __('app.platform.owner') }}</th>
                                    <th class="border-0 text-center">{{ __('app.platform.status') }}</th>
                                    <th class="border-0 text-right px-4">{{ __('app.platform.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentVendors as $vendor)
                                    <tr>
                                        <td class="px-4 py-3 font-weight-bold">{{ $vendor->business_name }}</td>
                                        <td class="py-3">{{ $vendor->owner->name }}</td>
                                        <td class="py-3 text-center">
                                            <span
                                                class="badge badge-pill {{ $vendor->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }} px-3">
                                                {{ __('app.platform.' . $vendor->status) }}
                                            </span>
                                        </td>
                                        <td class="text-right px-4 py-3">
                                            <a href="{{ route('super-admin.vendors.insights', $vendor) }}"
                                                class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                                <i class="la la-chart-area"></i> {{ __('app.platform.insights') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card mb-4" style="--card-accent: var(--p-indigo);">
                    <div class="text-center mb-4">
                        <div class="bg-light-warning p-3 rounded-circle d-inline-block shadow-sm">
                            <i class="la la-crown text-warning font-large-1"></i>
                        </div>
                        <h5 class="font-weight-bold mt-3 mb-0 text-gradient">{{ __('app.platform.top_performing_vendors') }}
                        </h5>
                    </div>
                    <div class="mt-4">
                        @foreach($topVendors as $vendor)
                            <div class="d-flex align-items-center mb-4 p-2 rounded-lg transition-all hover-bg-light">
                                <div class="bg-slate-100 p-3 rounded-xl mr-3" style="width: 50px; text-align: center;">
                                    <span class="font-weight-bold text-muted">{{ $loop->iteration }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 font-weight-bold text-white">{{ $vendor->business_name }}</h6>
                                    <small class="text-emerald font-weight-bold">
                                        {{ number_format($vendor->invoices_sum_paid_amount, 2) }}</small>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('super-admin.vendors.insights', $vendor) }}" class="text-muted"><i
                                            class="la la-angle-right"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#vendor-search').select2({
                    ajax: {
                        url: "{{ route('super-admin.vendors.search') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.business_name + ' (' + item.owner.name + ')'
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#vendor-search').on('select2:select', function (e) {
                    var vendorId = e.params.data.id;
                    window.location.href = "{{ url('super-admin/vendors') }}/" + vendorId + "/insights";
                });
            });
        </script>
    @endpush
@endsection