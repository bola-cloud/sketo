@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">{{ __('app.categories.title') }}</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('app.sidebar.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('app.categories.all_categories') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right">
                @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
                    <a href="{{ route('categories.create') }}" class="btn btn-primary round px-2 shadow">
                        <i class="la la-plus"></i> {{ __('app.categories.add_new') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="content-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>{{ __('app.common.success') }}!</strong> {{ session('success') }}
            </div>
        @endif

        <div class="card pull-up border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>{{ __('app.categories.name') }}</th>
                                    <th class="text-right">{{ __('app.categories.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $key => $category)
                                    <tr>
                                        <td class="text-bold-600">{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-soft-primary mr-2">
                                                    <i class="la la-folder primary"></i>
                                                </div>
                                                <span class="text-bold-600">{{ $category->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            @if(auth()->user()->hasRole('admin') || auth()->user()->can('create-categories'))
                                                <a href="{{ route('categories.edit', $category->id) }}"
                                                    class="btn btn-sm btn-soft-warning mr-1">
                                                    <i class="la la-edit"></i> {{ __('app.common.edit') }}
                                                </a>
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger"
                                                        onclick="return confirm('{{ __('app.categories.delete_confirm') }}')">
                                                        <i class="la la-trash"></i> {{ __('app.common.delete') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background: rgba(59, 130, 246, 0.1);
        }

        .btn-soft-warning {
            color: #d97706;
            background: rgba(217, 119, 6, 0.1);
            border: none;
        }

        .btn-soft-warning:hover {
            background: #d97706;
            color: #fff;
        }

        .btn-soft-danger {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
        }

        .btn-soft-danger:hover {
            background: #ef4444;
            color: #fff;
        }

        .table-premium th {
            font-weight: 700;
            color: #1e293b;
            border-top: none;
            padding: 1.25rem 1rem;
        }

        .table-premium td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem;
        }
    </style>
@endsection