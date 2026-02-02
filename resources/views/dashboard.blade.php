@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); position: relative;">
                <div class="card-body p-4" style="position: relative; z-index: 2;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.3);">
                                    <i class="bi bi-speedometer2 text-white" style="font-size: 1.75rem;"></i>
                                </div>
                                <div>
                                    <h3 class="text-white mb-1 fw-bold" style="font-size: 1.75rem; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                        Dashboard Overview
                                    </h3>
                                    <p class="text-white mb-0" style="opacity: 0.95; font-size: 1rem;">
                                        Welcome back, <strong>{{ auth()->user()->name }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-inline-block px-4 py-2" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; border: 2px solid rgba(255,255,255,0.3);">
                                <div class="text-white">
                                    <i class="bi bi-person-badge-fill"></i> 
                                    <strong style="font-size: 1rem;">{{ auth()->user()->role_label }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; z-index: 1;"></div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        @foreach($stats as $label => $value)
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1">
                                    {{ str_replace('_', ' ', $label) }}
                                </p>
                                <h2 class="mb-0 fw-bold">{{ $value }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                @switch($label)
                                    @case('total_reports')
                                    @case('my_reports')
                                    @case('assigned')
                                        <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                                        @break
                                    @case('pending')
                                    @case('need_review')
                                        <i class="bi bi-eye text-info fs-4"></i>
                                        @break
                                    @case('completed')
                                    @case('fixed')
                                    @case('approved')
                                        <i class="bi bi-check-circle text-success fs-4"></i>
                                        @break
                                    @case('rejected')
                                        <i class="bi bi-x-circle text-danger fs-4"></i>
                                        @break
                                    @case('departments')
                                        <i class="bi bi-building text-info fs-4"></i>
                                        @break
                                    @default
                                        <i class="bi bi-graph-up text-primary fs-4"></i>
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Quick Actions -->
    @if(auth()->user()->role === 'auditor')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-lightning-charge"></i> Quick Actions
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create New Report
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> View All Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(auth()->user()->role === 'staff_departemen')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-lightning-charge"></i> Quick Actions
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('reports.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> View Assigned Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(auth()->user()->role === 'supervisor')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-lightning-charge"></i> Quick Actions
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('reports.index') }}" class="btn btn-primary">
                            <i class="bi bi-clipboard-check"></i> Review Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Recent Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Recent Reports
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentReports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Report #</th>
                                        <th>Audit Type</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReports as $report)
                                        <tr>
                                            <td class="fw-bold">{{ $report->report_number }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $report->auditType->name }}
                                                </span>
                                            </td>
                                            <td>{{ $report->department->name }}</td>
                                            <td>{{ $report->location }}</td>
                                            <td>{!! $report->status_badge !!}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $report->created_at->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('reports.show', $report) }}" 
                                                   class="btn btn-sm btn-outline-primary">
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