<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\AuditType;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::whereIn('status', ['submitted', 'in_progress'])->count(),
            'completed_reports' => Report::where('status', 'approved')->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_users' => User::where('is_active', true)->count(),
        ];

        $recent_reports = Report::with(['auditType', 'department', 'auditor'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_reports'));
    }

    // Department Management
    public function departments()
    {
        $departments = Department::latest()->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments')->with('success', 'Department created successfully');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments')->with('success', 'Department updated successfully');
    }

    public function deleteDepartment(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments')->with('success', 'Department deleted successfully');
    }

    // Audit Type Management
    public function auditTypes()
    {
        $auditTypes = AuditType::latest()->get();
        return view('admin.audit-types.index', compact('auditTypes'));
    }

    public function storeAuditType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:audit_types,code',
            'description' => 'nullable|string',
        ]);

        AuditType::create($request->all());

        return redirect()->route('admin.audit-types')->with('success', 'Audit Type created successfully');
    }

    public function updateAuditType(Request $request, AuditType $auditType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:audit_types,code,' . $auditType->id,
            'description' => 'nullable|string',
        ]);

        $auditType->update($request->all());

        return redirect()->route('admin.audit-types')->with('success', 'Audit Type updated successfully');
    }

    public function deleteAuditType(AuditType $auditType)
    {
        $auditType->delete();
        return redirect()->route('admin.audit-types')->with('success', 'Audit Type deleted successfully');
    }

    // User Management
    public function users()
    {
        $users = User::with('department')->latest()->get();
        $departments = Department::where('is_active', true)->get();
        return view('admin.users.index', compact('users', 'departments'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,auditor,staff_departemen,supervisor',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:super_admin,auditor,staff_departemen,supervisor',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}