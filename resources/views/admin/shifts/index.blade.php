@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.shifts.title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('app.shifts.all_shifts') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                @if(!$activeShift)
                    <a href="{{ route('shifts.create') }}" class="btn btn-primary round px-2 shadow">
                        <i class="la la-plus"></i> {{ __('app.shifts.open_new_shift') }}
                    </a>
                @else
                    <a href="{{ route('shifts.edit', $activeShift->id) }}" class="btn btn-danger round px-2 shadow">
                        <i class="la la-power-off"></i> {{ __('app.shifts.close_current_shift') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="content-body">
        @if($activeShift)
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-directional-primary white border-0 shadow-lg mb-2"
                        style="border-radius: 20px;">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="white">{{ __('app.shifts.current_open_shift') }}</h3>
                                        <span>{{ __('app.shifts.started_at') }} {{ $activeShift->start_time }}</span>
                                        <br>
                                        <span>{{ __('app.shifts.starting_cash_label') }} {{ number_format($activeShift->starting_cash, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="la la-clock-o white font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card pull-up border-0 shadow-sm"
            style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>{{ __('app.shifts.cashier') }}</th>
                                    <th>{{ __('app.shifts.start_time') }}</th>
                                    <th>{{ __('app.shifts.end_time') }}</th>
                                    <th>{{ __('app.shifts.starting_cash') }}</th>
                                    <th>{{ __('app.shifts.ending_cash') }}</th>
                                    <th>{{ __('app.shifts.sales') }}</th>
                                    <th>{{ __('app.shifts.status') }}</th>
                                    <th>{{ __('app.shifts.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                    <tr class="text-center align-middle">
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="avatar avatar-sm bg-light-primary mr-1">
                                                    <span class="text-primary">{{ substr($shift->user->name, 0, 1) }}</span>
                                                </div>
                                                <span class="text-bold-600">{{ $shift->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $shift->start_time }}</td>
                                        <td>{{ $shift->end_time ?? '-' }}</td>
                                        <td class="text-success text-bold-600">{{ number_format($shift->starting_cash, 2) }} {{ App::getLocale() == 'ar' ? 'ج.م' : 'EGP' }}
                                        </td>
                                        <td class="text-danger text-bold-600">
                                            {{ $shift->ending_cash ? number_format($shift->ending_cash, 2) . (App::getLocale() == 'ar' ? ' ج.م' : ' EGP') : '-' }}
                                        </td>
                                        <td class="text-primary text-bold-600">
                                            {{ $shift->total_sales ? number_format($shift->total_sales, 2) . (App::getLocale() == 'ar' ? ' ج.م' : ' EGP') : '-' }}
                                        </td>
                                        <td>
                                            @if($shift->status == 'open')
                                                <span class="badge badge-success badge-glow">{{ __('app.shifts.open') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('app.shifts.closed') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shift->status == 'open')
                                                <a href="{{ route('shifts.edit', $shift->id) }}"
                                                    class="btn btn-sm btn-outline-danger round">
                                                    {{ __('app.shifts.close') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-center">
                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection