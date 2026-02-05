@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text"></i> 
                            @if(auth()->user()->role === 'auditor')
                                My Reports
                            @elseif(auth()->user()->role === 'staff_departemen')
                                Assigned Reports
                            @elseif(auth()->user()->role === 'supervisor')
                                Reports to Review
                            @else
                                All Reports
                            @endif
                        </h5>
                        
                        @if(auth()->user()->role === 'auditor')
                            <a href="{{ route('reports.create') }}" class="btn btn-light btn-sm mt-2 mt-md-0">
                                <i class="bi bi-plus-circle"></i> Create Report
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter Reports</h6>
                            <button class="btn btn-sm btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                <i class="bi bi-funnel"></i> Toggle Filter
                            </button>
                        </div>
                        
                        <div class="collapse show" id="filterCollapse">
                            <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <!-- Search -->
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label small fw-bold">Search Report</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" 
                                                   placeholder="Number, location, issue type..." 
                                                   value="{{ request('search') }}">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Filter Status -->
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <label class="form-label small fw-bold">Status</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="">All Status</option>
                                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="fixed" {{ request('status') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>

                                    <!-- Filter Departemen -->
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="form-label small fw-bold">Department</label>
                                        <select name="department" class="form-select form-select-sm">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter Periode -->
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="form-label small fw-bold">Period</label>
                                        <select name="period" class="form-select form-select-sm">
                                            <option value="">All Time</option>
                                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>This Week</option>
                                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>This Month</option>
                                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>This Year</option>
                                        </select>
                                    </div>

                                    <!-- Filter Tanggal Custom -->
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="form-label small fw-bold">From Date</label>
                                        <input type="date" name="date_from" class="form-control form-control-sm" 
                                               value="{{ request('date_from') }}">
                                    </div>

                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="form-label small fw-bold">To Date</label>
                                        <input type="date" name="date_to" class="form-control form-control-sm" 
                                               value="{{ request('date_to') }}">
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="col-12 col-md-4 col-lg-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                            <i class="bi bi-filter"></i> Apply
                                        </button>
                                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info jumlah hasil -->
                    @if(request()->hasAny(['status', 'department', 'period', 'date_from', 'date_to', 'search']))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle"></i> 
                            Showing <strong>{{ $reports->total() }}</strong> reports from filter results
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($reports->count() > 0)
                        <!-- Desktop View -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-bold">Report Number</th>
                                        <th class="fw-bold">Audit Type</th>
                                        <th class="fw-bold">Department</th>
                                        <th class="fw-bold">Location</th>
                                        <th class="fw-bold">Issue Type</th>
                                        <th class="fw-bold text-center">Status</th>
                                        <th class="fw-bold">Date</th>
                                        <th class="fw-bold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ $report->report_number }}</td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                    {{ $report->auditType->name }}
                                                </span>
                                            </td>
                                            <td>{{ $report->department->name }}</td>
                                            <td>
                                                <i class="bi bi-geo-alt text-muted"></i> {{ Str::limit($report->location, 30) }}
                                            </td>
                                            <td>{{ Str::limit($report->issue_type, 25) }}</td>
                                            <td class="text-center">{!! $report->status_badge !!}</td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3"></i> {{ $report->created_at->format('d M Y') }}<br>
                                                    <i class="bi bi-clock"></i> {{ $report->created_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Tablet View -->
                        <div class="d-none d-md-block d-lg-none">
                            @foreach($reports as $report)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-8">
                                                <h6 class="fw-bold text-primary mb-2">{{ $report->report_number }}</h6>
                                                <div class="mb-2">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info me-1">
                                                        {{ $report->auditType->name }}
                                                    </span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                        {{ $report->department->name }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-4 text-end">
                                                {!! $report->status_badge !!}
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <p class="mb-1">
                                                <i class="bi bi-geo-alt text-primary"></i> 
                                                <strong>{{ $report->location }}</strong>
                                            </p>
                                            <p class="mb-1 text-muted small">
                                                <i class="bi bi-exclamation-triangle"></i> {{ $report->issue_type }}
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-calendar3"></i> {{ $report->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Mobile View -->
                        <div class="d-md-none">
                            @foreach($reports as $report)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 fw-bold text-primary small">{{ $report->report_number }}</h6>
                                            <div>{!! $report->status_badge !!}</div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info me-1" style="font-size: 0.7rem;">
                                                {{ Str::limit($report->auditType->name, 15) }}
                                            </span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary" style="font-size: 0.7rem;">
                                                {{ Str::limit($report->department->name, 15) }}
                                            </span>
                                        </div>
                                        
                                        <p class="mb-1 small">
                                            <i class="bi bi-geo-alt text-primary"></i> 
                                            <strong>{{ Str::limit($report->location, 35) }}</strong>
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            <i class="bi bi-exclamation-triangle"></i> {{ Str::limit($report->issue_type, 30) }}
                                        </p>
                                        <p class="mb-2 text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-calendar3"></i> {{ $report->created_at->format('d M Y H:i') }}
                                        </p>
                                        
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $reports->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">No reports found</h5>
                            @if(request()->hasAny(['status', 'department', 'period', 'date_from', 'date_to', 'search']))
                                <p class="text-muted mb-3">Try changing your filter or search criteria</p>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                </a>
                            @elseif(auth()->user()->role === 'auditor')
                                <p class="text-muted mb-3">You haven't created any reports yet</p>
                                <a href="{{ route('reports.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create First Report
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #065F46 0%, #047857 100%);
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .table th {
        border-bottom: 2px solid #dee2e6;
        font-size: 0.875rem;
    }
    
    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem;
        }
        
        .form-label {
            margin-bottom: 0.25rem;
        }
        
        .badge {
            font-size: 0.65rem !important;
            padding: 0.25em 0.5em;
        }
    }
    
    @media (max-width: 991.98px) {
        .table-responsive {
            font-size: 0.85rem;
        }
    }
</style>
@endpush