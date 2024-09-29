@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
  .flatpickr-calendar {
    z-index: 10000;
    width: auto;
    max-width: 300px;
}

.flatpickr-calendar .flatpickr-month {
    background-color: #fff;
    border-radius: 5px;
}

.flatpickr-calendar.open {
    visibility: visible;
    opacity: 1;
}
</style>

@php
    $user = auth()->user();
    $permissions = $user->roles()->with('permissions')->get()->pluck('permissions.*.name')->flatten()->unique();
@endphp
@if($user->hasRole('admin'))
    <div class="row">
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="info" id="productsSold">{{ $productsSold }}</h3>
                            <h6>المنتجات المباعة</h6>
                        </div>
                        <div>
                            <i class="icon-basket-loaded info font-large-2 float-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="warning" id="totalRevenue">{{ $totalRevenue }} ج.م</h3>
                            <h6>إجمالي الإيرادات</h6>
                        </div>
                        <div>
                            <i class="icon-pie-chart warning font-large-2 float-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="success" id="totalUnsoldProducts">{{ $totalUnsoldProducts }}</h6>
                            <h6>المنتجات غير المباعة</h6>
                        </div>
                        <div>
                            <i class="icon-handbag success font-large-2 float-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="danger" id="totalPurchases"> {{ $totalPurchases }} ج.م </h3>
                            <h6>إجمالي المشتريات</h6>
                        </div>
                        <div>
                            <i class="icon-wallet danger font-large-2 float-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="primary" id="totalProfit"> {{ $totalProfit }} ج.م</h3>
                            <h6>إجمالي الأرباح</h6>
                        </div>
                        <div>
                            <i class="icon-graph primary font-large-2 float-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    <!-- Date Range Picker -->
    <div class="row">
        <div class="col-12">
        <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="text" id="start_date" name="start_date" class="form-control datepicker">
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="text" id="end_date" name="end_date" class="form-control datepicker">
            </div>
            </div>
        </div>
        </div>
    </div>
@endif

@if($lowStockProducts->count() > 0)
    <div class="alert alert-danger">
        <h4>المنتجات التي وصلت إلى الحد الأدنى</h4>
        <ul>
            @foreach($lowStockProducts as $product)
                <li>المنتج {{ $product->name }} وصل إلى الحد الأدنى. الكمية الحالية: {{ $product->quantity }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Line Chart for Monthly Products Sold -->
{{-- <div class="row">
    <div class="col-xl-12 col-lg-12 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"> المنتجات المباعة شهريا </h4>
            </div>
            <div class="card-body">
                <canvas id="productsSoldChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Line Chart for Monthly Profit -->
<div class="row">
    <div class="col-xl-12 col-lg-12 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"> العائد شهريا </h4>
            </div>
            <div class="card-body">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
    </div>
</div> --}}
@endsection

@push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="{{asset('assets/js/flatpickr.js')}}"></script> 
<script src="{{asset('assets/js/jquery.js')}}"></script>
<script>
  $(document).ready(function() {
      flatpickr('.datepicker', {
          dateFormat: "Y-m-d",
          onChange: function(selectedDates, dateStr, instance) {
              fetchFilteredData();
          }
      });

      function fetchFilteredData() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $.ajax({
            url: "{{ route('dashboard') }}",
            type: "GET",
            data: {
                start_date: startDate,
                end_date: endDate,
            },
            success: function(data) {
                updateDashboard(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data: " + error);
                console.error("Response Text: " + xhr.responseText);
            }
        });
    }

      function updateDashboard(data) {
          $('#productsSold').text(data.productsSold);
          $('#totalRevenue').text('ج.م' + data.totalRevenue);
          $('#totalUnsoldProducts').text(data.totalUnsoldProducts);
          $('#totalPurchases').text('ج.م' + data.totalPurchases);
          $('#totalProfit').text('ج.م' + data.totalProfit);
      }
  });


  var productsSoldCtx = document.getElementById('productsSoldChart').getContext('2d');
  var productsSoldChart = new Chart(productsSoldCtx, {
      type: 'line',
      data: {
          labels: {!! json_encode($monthlyData->keys()) !!},
          datasets: [
              {
                  label: 'المنتجات المباعة',
                  data: {!! json_encode($monthlyData->pluck('total_sold')) !!},
                  borderColor: 'rgba(75, 192, 192, 1)',
                  backgroundColor: 'rgba(75, 192, 192, 0.2)',
                  borderWidth: 2,
                  fill: true,
                  tension: 0.4
              }
          ]
      },
      options: {
          responsive: true,
          scales: {
              y: {
                  beginAtZero: true,
                  title: {
                      display: true,
                      text: 'الكمية'
                  }
              },
              x: {
                  title: {
                      display: true,
                      text: 'الشهر'
                  }
              }
          }
      }
  });

  var profitCtx = document.getElementById('profitChart').getContext('2d');
  var profitChart = new Chart(profitCtx, {
      type: 'line',
      data: {
          labels: {!! json_encode($monthlyData->keys()) !!},
          datasets: [
              {
                  label: 'الإيرادات الشهرية',
                  data: {!! json_encode($monthlyData->pluck('total_revenue')) !!},
                  borderColor: 'rgba(255, 99, 132, 1)',
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
                  borderWidth: 2,
                  fill: true,
                  tension: 0.4
              }
          ]
      },
      options: {
          responsive: true,
          scales: {
              y: {
                  beginAtZero: true,
                  title: {
                      display: true,
                      text: 'الإيرادات ($)'
                  }
              },
              x: {
                  title: {
                      display: true,
                      text: 'الشهر'
                  }
              }
          }
      }
  });
</script>
@endpush
