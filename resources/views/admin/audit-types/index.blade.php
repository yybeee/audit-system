@extends('layouts.app')

@section('title', 'Manage Audit Types')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Manage Audit Types</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuditTypeModal">
                        <i class="bi bi-plus-circle"></i> Add Audit Type
                    </button>
                </div>
                <div class="card-body">
                    @if($auditTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditTypes as $index => $type)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $type->name }}</td>
                                            <td><span class="badge bg-info">{{ $type->code }}</span></td>
                                            <td>{{ $type->description ?? '-' }}</td>
                                            <td>
                                                @if($type->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editAuditType({{ $type->id }}, '{{ $type->name }}', '{{ $type->code }}', '{{ $type->description }}', {{ $type->is_active ? 'true' : 'false' }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('admin.audit-types.delete', $type) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this audit type?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No audit types yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Audit Type Modal -->
<div class="modal fade" id="addAuditTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.audit-types.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Audit Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Audit Type Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., HSE, K3, 5R">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g., HSE, K3" maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_add" checked>
                        <label class="form-check-label" for="is_active_add">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Audit Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Audit Type Modal -->
<div class="modal fade" id="editAuditTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAuditTypeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Audit Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Audit Type Name *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" id="edit_code" class="form-control" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Audit Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editAuditType(id, name, code, description, isActive) {
    document.getElementById('editAuditTypeForm').action = '/admin/audit-types/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_is_active').checked = isActive;
    new bootstrap.Modal(document.getElementById('editAuditTypeModal')).show();
}
</script>
@endpush