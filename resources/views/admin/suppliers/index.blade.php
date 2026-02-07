@extends('layouts.admin')

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">إدارة الموردين</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">الموردين</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-header-right col-md-6 col-12 mb-2 text-right">
            <button type="button" class="btn btn-primary round shadow-sm px-2" data-toggle="modal"
                data-target="#createSupplierModal">
                <i class="la la-plus"></i> إضافة مورد جديد
            </button>
        </div>
    </div>

    <div class="content-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>تم بنجاح!</strong> {{ session('success') }}
            </div>
        @endif

        <div class="card pull-up border-0 shadow-sm"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px;">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">#</th>
                                    <th>اسم المورد</th>
                                    <th>رقم الهاتف</th>
                                    <th class="text-right" style="width: 250px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="supplierTable">
                                @foreach($suppliers as $supplier)
                                    <tr id="supplierRow{{ $supplier->id }}">
                                        <td><span class="text-bold-600">#{{ $supplier->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-soft-info round-lg mr-1 d-flex align-items-center justify-content-center"
                                                    style="width: 35px; height: 35px;">
                                                    <span
                                                        class="info text-bold-700">{{ mb_substr($supplier->name, 0, 1) }}</span>
                                                </div>
                                                <span class="text-bold-700 text-dark">{{ $supplier->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info round px-1">
                                                <i class="la la-phone"></i> {{ $supplier->phone }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{ route('suppliers.show', $supplier->id) }}"
                                                    class="btn btn-sm btn-soft-info round mr-1 px-1">
                                                    <i class="la la-eye"></i> عرض
                                                </a>
                                                <button class="btn btn-sm btn-soft-warning round mr-1 px-1"
                                                    onclick="editSupplier({{ $supplier->id }})" data-toggle="modal"
                                                    data-target="#editSupplierModal">
                                                    <i class="la la-edit"></i> تعديل
                                                </button>
                                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger round px-1"
                                                        onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                        <i class="la la-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
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

    <!-- Create Supplier Modal -->
    <div class="modal fade text-left" id="createSupplierModal" tabindex="-1" role="dialog"
        aria-labelledby="createSupplierModalLabel" aria-hidden="true" style="direction: rtl;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-primary text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title font-weight-bold white" id="createSupplierModalLabel"><i
                            class="la la-plus-circle"></i> إضافة مورد جديد</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createSupplierForm">
                    @csrf
                    <div class="modal-body p-2">
                        <div class="form-group mb-2">
                            <label for="name" class="text-bold-600">اسم المورد <span class="danger">*</span></label>
                            <input type="text" class="form-control round border-primary" id="name" name="name"
                                placeholder="أدخل اسم المورد..." required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="phone" class="text-bold-600">رقم الهاتف <span class="danger">*</span></label>
                            <input type="text" class="form-control round border-primary" id="phone" name="phone"
                                placeholder="01XXXXXXXXX" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-2">
                        <button type="button" class="btn btn-light round px-2" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary round px-2 shadow">حفظ المورد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade text-left" id="editSupplierModal" tabindex="-1" role="dialog"
        aria-labelledby="editSupplierModalLabel" aria-hidden="true" style="direction: rtl;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-warning text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title font-weight-bold white" id="editSupplierModalLabel"><i class="la la-edit"></i>
                        تعديل بيانات المورد</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editSupplierForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-2">
                        <input type="hidden" id="edit_supplier_id" name="supplier_id">
                        <div class="form-group mb-2">
                            <label for="edit_name" class="text-bold-600">اسم المورد <span class="danger">*</span></label>
                            <input type="text" class="form-control round border-warning" id="edit_name" name="name"
                                required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="edit_phone" class="text-bold-600">رقم الهاتف <span class="danger">*</span></label>
                            <input type="text" class="form-control round border-warning" id="edit_phone" name="phone"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-2">
                        <button type="button" class="btn btn-light round px-2" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning round px-2 shadow text-white">تحديث البيانات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .round-lg {
            border-radius: 12px !important;
        }

        .avatar.bg-soft-info {
            background-color: rgba(6, 182, 212, 0.1);
        }

        .btn-soft-info {
            color: #0891b2;
            background-color: rgba(6, 182, 212, 0.1);
            border: none;
        }

        .btn-soft-warning {
            color: #f59e0b;
            background-color: rgba(245, 158, 11, 0.1);
            border: none;
        }

        .btn-soft-danger {
            color: #ef4444;
            background-color: rgba(239, 68, 68, 0.1);
            border: none;
        }

        .btn-soft-info:hover {
            background-color: #0891b2;
            color: white !important;
        }

        .btn-soft-warning:hover {
            background-color: #f59e0b;
            color: white !important;
        }

        .btn-soft-danger:hover {
            background-color: #ef4444;
            color: white !important;
        }

        .badge-soft-info {
            color: #0891b2;
            background-color: rgba(6, 182, 212, 0.1);
        }

        .table-premium th {
            font-weight: 700;
            color: #1e293b;
            border-top: none;
        }

        .table-premium td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Create Supplier
        $('#createSupplierForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('suppliers.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    $('#createSupplierModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    alert('فشل في إضافة المورد.');
                }
            });
        });

        // Edit Supplier - Show existing supplier data in the modal
        function editSupplier(id) {
            $.ajax({
                url: "/suppliers/" + id + "/edit",
                type: "GET",
                success: function (response) {
                    $('#edit_supplier_id').val(response.id);
                    $('#edit_name').val(response.name);
                    $('#edit_phone').val(response.phone);
                },
                error: function (xhr) {
                    alert('فشل في جلب بيانات المورد.');
                }
            });
        }

        // Update Supplier
        $('#editSupplierForm').on('submit', function (e) {
            e.preventDefault();
            var supplierId = $('#edit_supplier_id').val();
            $.ajax({
                url: "/suppliers/" + supplierId,
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    $('#editSupplierModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    alert('فشل في تحديث بيانات المورد.');
                }
            });
        });
    </script>
@endpush


@push('scripts')
    <script>
        // Create Supplier
        $('#createSupplierForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('suppliers.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    $('#createSupplierModal').modal('hide');
                    location.reload(); // You can refresh the page or dynamically update the table
                },
                error: function (xhr) {
                    alert('فشل في إضافة المورد.');
                }
            });
        });

        // Edit Supplier - Show existing supplier data in the modal
        function editSupplier(id) {
            $.ajax({
                url: "/suppliers/" + id + "/edit",
                type: "GET",
                success: function (response) {
                    $('#edit_supplier_id').val(response.id);
                    $('#edit_name').val(response.name);
                    $('#edit_phone').val(response.phone);
                },
                error: function (xhr) {
                    alert('فشل في جلب بيانات المورد.');
                }
            });
        }

        // Update Supplier
        $('#editSupplierForm').on('submit', function (e) {
            e.preventDefault();
            var supplierId = $('#edit_supplier_id').val();
            $.ajax({
                url: "/suppliers/" + supplierId,
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    $('#editSupplierModal').modal('hide');
                    location.reload(); // Reload the page or update the table dynamically
                },
                error: function (xhr) {
                    alert('فشل في تحديث بيانات المورد.');
                }
            });
        });
    </script>
@endpush