@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);">
                <div class="card-body text-white p-4">
                    <h3 class="mb-1"><i class="bi bi-gear"></i> Admin Dashboard</h3>
                    <p class="mb-0 opacity-75">System Management & Statistics</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Total Reports</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_reports'] }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Pending</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['pending_reports'] }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Completed</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['completed_reports'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Departments</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_departments'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Total Users</p>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_users'] }}</h2>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-secondary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Management Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('admin.departments') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-building fs-3 d-block mb-2"></i>
                                <strong>Manage Departments</strong>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.audit-types') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                                <strong>Manage Audit Types</strong>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>
                                <strong>Manage Users</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Reports</h5>
                </div>
                <div class="card-body p-0">
                    @if($recent_reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Report #</th>
                                        <th>Audit Type</th>
                                        <th>Department</th>
                                        <th>Auditor</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_reports as $report)
                                        <tr>
                                            <td class="fw-bold">{{ $report->report_number }}</td>
                                            <td><span class="badge bg-info">{{ $report->auditType->name }}</span></td>
                                            <td>{{ $report->department->name }}</td>
                                            <td>{{ $report->auditor->name }}</td>
                                            <td>{!! $report->status_badge !!}</td>
                                            <td><small class="text-muted">{{ $report->created_at->format('d M Y') }}</small></td>
                                            <td>
                                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">No reports yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection