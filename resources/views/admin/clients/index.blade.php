@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <div class="card p-3">
        <div class="card-header d-flex justify-content-between">
            <h1>العملاء</h1>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createClientModal">
                إضافة عميل جديد
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
            <tbody id="clientTable">
                @foreach($clients as $client)
                    <tr id="clientRow{{ $client->id }}">
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>
                            <a class="btn btn-info" href="{{ route('clients.show', $client->id) }}">عرض</a>
                            <button class="btn btn-warning" onclick="editClient({{ $client->id }})" data-toggle="modal" data-target="#editClientModal">تعديل</button>
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline;">
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

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClientModalLabel">إضافة عميل جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientForm">
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
                    <button type="submit" class="btn btn-primary">إضافة العميل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">تعديل العميل</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editClientForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_client_id" name="client_id">
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
                    <button type="submit" class="btn btn-primary">تحديث العميل</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Create Client
$('#createClientForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: "{{ route('clients.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#createClientModal').modal('hide');
            location.reload(); // You can refresh the page or dynamically update the table
        },
        error: function(xhr) {
            alert('فشل في إضافة العميل.');
        }
    });
});

// Edit Client - Show existing client data in the modal
function editClient(id) {
    $.ajax({
        url: "/clients/" + id + "/edit",
        type: "GET",
        success: function(response) {
            $('#edit_client_id').val(response.id);
            $('#edit_name').val(response.name);
            $('#edit_phone').val(response.phone);
        },
        error: function(xhr) {
            alert('فشل في جلب بيانات العميل.');
        }
    });
}

// Update Client
$('#editClientForm').on('submit', function(e) {
    e.preventDefault();
    var clientId = $('#edit_client_id').val();
    $.ajax({
        url: "/clients/" + clientId,
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#editClientModal').modal('hide');
            location.reload(); // Reload the page or update the table dynamically
        },
        error: function(xhr) {
            alert('فشل في تحديث بيانات العميل.');
        }
    });
});
</script>
@endpush
