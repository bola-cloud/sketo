@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>الموردين</h1>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createSupplierModal">
                إضافة مورد جديد
            </button>
        </div>
        
    
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="supplierTable">
                @foreach($suppliers as $supplier)
                    <tr id="supplierRow{{ $supplier->id }}">
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>
                            <a class="btn btn-info" href="{{ route('suppliers.destroy', $supplier->id) }}">عرض</a>
                            <button class="btn btn-warning" onclick="editSupplier({{ $supplier->id }})" data-toggle="modal" data-target="#editSupplierModal">تعديل</button>
                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" role="dialog" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSupplierModalLabel">إضافة مورد جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createSupplierForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">الاسم</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">الهاتف</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">إضافة المورد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSupplierModalLabel">تعديل المورد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSupplierForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_supplier_id" name="supplier_id">
                    <div class="form-group">
                        <label for="edit_name">الاسم</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">الهاتف</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">تحديث المورد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Create Supplier
$('#createSupplierForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: "{{ route('suppliers.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#createSupplierModal').modal('hide');
            location.reload(); // You can refresh the page or dynamically update the table
        },
        error: function(xhr) {
            alert('فشل في إضافة المورد.');
        }
    });
});

// Edit Supplier - Show existing supplier data in the modal
function editSupplier(id) {
    $.ajax({
        url: "/suppliers/" + id + "/edit",
        type: "GET",
        success: function(response) {
            $('#edit_supplier_id').val(response.id);
            $('#edit_name').val(response.name);
            $('#edit_phone').val(response.phone);
        },
        error: function(xhr) {
            alert('فشل في جلب بيانات المورد.');
        }
    });
}

// Update Supplier
$('#editSupplierForm').on('submit', function(e) {
    e.preventDefault();
    var supplierId = $('#edit_supplier_id').val();
    $.ajax({
        url: "/suppliers/" + supplierId,
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#editSupplierModal').modal('hide');
            location.reload(); // Reload the page or update the table dynamically
        },
        error: function(xhr) {
            alert('فشل في تحديث بيانات المورد.');
        }
    });
});
</script>
@endpush
