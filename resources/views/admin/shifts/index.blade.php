@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">إدارة الورديات</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">الورديات</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                @if(!$activeShift)
                    <a href="{{ route('shifts.create') }}" class="btn btn-primary round px-2 shadow">
                        <i class="la la-plus"></i> فتح وردية جديدة
                    </a>
                @else
                    <a href="{{ route('shifts.edit', $activeShift->id) }}" class="btn btn-danger round px-2 shadow">
                        <i class="la la-power-off"></i> إغلاق الوردية الحالية
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
                                        <h3 class="white">الوردية الحالية مفتوحة</h3>
                                        <span>بدأت في: {{ $activeShift->start_time }}</span>
                                        <br>
                                        <span>المبلغ المستلم: {{ number_format($activeShift->starting_cash, 2) }} ج.م</span>
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
                                    <th>الكاشير</th>
                                    <th>وقت البدء</th>
                                    <th>وقت الانتهاء</th>
                                    <th>المبلغ المستلم</th>
                                    <th>المبلغ المسلم</th>
                                    <th>المبيعات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
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
                                        <td class="text-success text-bold-600">{{ number_format($shift->starting_cash, 2) }} ج.م
                                        </td>
                                        <td class="text-danger text-bold-600">
                                            {{ $shift->ending_cash ? number_format($shift->ending_cash, 2) . ' ج.م' : '-' }}
                                        </td>
                                        <td class="text-primary text-bold-600">
                                            {{ $shift->total_sales ? number_format($shift->total_sales, 2) . ' ج.م' : '-' }}
                                        </td>
                                        <td>
                                            @if($shift->status == 'open')
                                                <span class="badge badge-success badge-glow">مفتوحة</span>
                                            @else
                                                <span class="badge badge-secondary">مغلقة</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($shift->status == 'open')
                                                <a href="{{ route('shifts.edit', $shift->id) }}"
                                                    class="btn btn-sm btn-outline-danger round">
                                                    إغلاق
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