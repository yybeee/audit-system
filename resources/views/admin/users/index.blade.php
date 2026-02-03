@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Manage Users</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus-circle"></i> Add User
                    </button>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Audit Type</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->role === 'super_admin')
                                                    <span class="badge bg-danger">Super Admin</span>
                                                @elseif($user->role === 'auditor')
                                                    <span class="badge bg-primary">Auditor</span>
                                                @elseif($user->role === 'staff_departemen')
                                                    <span class="badge bg-info">Staff Dept</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->department->name ?? '-' }}</td>
                                            <td>{{ $user->auditType->name ?? '-' }}</td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser({{ json_encode($user) }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No users yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., John Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" required placeholder="e.g., johndoe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required placeholder="e.g., john@company.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" id="role_add" class="form-select" required onchange="toggleFields('add')">
                            <option value="">Select Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="auditor">Auditor</option>
                            <option value="staff_departemen">Staff Departemen</option>
                        </select>
                    </div>
                    <!-- Department: muncul saat staff_departemen -->
                    <div class="mb-3" id="department_add_wrapper" style="display: none;">
                        <label class="form-label">Department *</label>
                        <select name="department_id" id="department_add" class="form-select">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Audit Type: muncul saat auditor -->
                    <div class="mb-3" id="audit_type_add_wrapper" style="display: none;">
                        <label class="form-label">Audit Type *</label>
                        <select name="audit_type_id" id="audit_type_add" class="form-select">
                            <option value="">Select Audit Type</option>
                            @foreach(\App\Models\AuditType::all() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_add" checked>
                        <label class="form-check-label" for="is_active_add">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="edit_password" class="form-control" minlength="6">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit_role" class="form-select" required onchange="toggleFields('edit')">
                            <option value="super_admin">Super Admin</option>
                            <option value="auditor">Auditor</option>
                            <option value="staff_departemen">Staff Departemen</option>
                        </select>
                    </div>
                    <!-- Department: muncul saat staff_departemen -->
                    <div class="mb-3" id="department_edit_wrapper" style="display: none;">
                        <label class="form-label">Department</label>
                        <select name="department_id" id="edit_department_id" class="form-select">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Audit Type: muncul saat auditor -->
                    <div class="mb-3" id="audit_type_edit_wrapper" style="display: none;">
                        <label class="form-label">Audit Type</label>
                        <select name="audit_type_id" id="audit_type_edit" class="form-select">
                            <option value="">Select Audit Type</option>
                            @foreach(\App\Models\AuditType::all() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFields(type) {
    const roleSelect = document.getElementById('role_' + type);
    const deptWrapper = document.getElementById('department_' + type + '_wrapper');
    const deptSelect = document.getElementById(type === 'add' ? 'department_add' : 'edit_department_id');
    const auditTypeWrapper = document.getElementById('audit_type_' + type + '_wrapper');
    const auditTypeSelect = document.getElementById('audit_type_' + type);

    // Department: muncul saat staff_departemen
    if (roleSelect.value === 'staff_departemen') {
        deptWrapper.style.display = 'block';
        deptSelect.required = true;
    } else {
        deptWrapper.style.display = 'none';
        deptSelect.required = false;
        deptSelect.value = '';
    }

    // Audit Type: muncul saat auditor
    if (roleSelect.value === 'auditor') {
        auditTypeWrapper.style.display = 'block';
        auditTypeSelect.required = true;
    } else {
        auditTypeWrapper.style.display = 'none';
        auditTypeSelect.required = false;
        auditTypeSelect.value = '';
    }
}

function editUser(user) {
    document.getElementById('editUserForm').action = '/admin/users/' + user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_department_id').value = user.department_id || '';
    document.getElementById('audit_type_edit').value = user.audit_type_id || '';
    document.getElementById('edit_is_active').checked = user.is_active;

    toggleFields('edit');

    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
</script>
@endpush