@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">
                                <i class="bi bi-speedometer2"></i> 
                                @if(Auth::user()->role === 'super_admin')
                                    Admin Dashboard
                                @elseif(Auth::user()->role === 'auditor')
                                    Auditor Dashboard
                                @else
                                    Department Dashboard
                                @endif
                            </h3>
                            <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}!</p>
                        </div>
                        
                        {{-- Department Filter - Only for Admin & Auditor --}}
                        @if(in_array(Auth::user()->role, ['super_admin', 'auditor']))
                        <div>
                            <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                                <select name="department" class="form-select form-select-sm bg-white" onchange="this.form.submit()" style="min-width: 200px;">
                                    <option value="all" {{ $departmentFilter === 'all' ? 'selected' : '' }}>
                                        🌐 All Departments
                                    </option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $departmentFilter == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        @if(Auth::user()->role === 'super_admin')
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Total Reports</p>
                                <h2 class="mb-0 fw-bold text-primary">{{ $stats['total_reports'] }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending</p>
                                <h2 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Fixed</p>
                                <h2 class="mb-0 fw-bold text-success">{{ $stats['completed'] }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Approved</p>
                                <h2 class="mb-0 fw-bold" style="color: #9b59b6;">{{ $stats['approved'] }}</h2>
                            </div>
                            <div class="p-3 rounded-circle" style="background-color: rgba(155, 89, 182, 0.1);">
                                <i class="bi bi-award fs-4" style="color: #9b59b6;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(Auth::user()->role === 'auditor')
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">My Reports</p>
                                <h2 class="mb-0 fw-bold text-primary">{{ $stats['my_reports'] }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-clipboard-data text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending</p>
                                <h2 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Need Review</p>
                                <h2 class="mb-0 fw-bold text-info">{{ $stats['need_review'] }}</h2>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-eye-fill text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Approved</p>
                                <h2 class="mb-0 fw-bold text-success">{{ $stats['approved'] }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Assigned</p>
                                <h2 class="mb-0 fw-bold text-primary">{{ $stats['assigned'] }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-inbox-fill text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending</p>
                                <h2 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Fixed</p>
                                <h2 class="mb-0 fw-bold text-success">{{ $stats['fixed'] }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-tools text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted text-uppercase small mb-1 fw-semibold">Approved</p>
                                <h2 class="mb-0 fw-bold" style="color: #9b59b6;">{{ $stats['approved'] }}</h2>
                            </div>
                            <div class="p-3 rounded-circle" style="background-color: rgba(155, 89, 182, 0.1);">
                                <i class="bi bi-patch-check-fill fs-4" style="color: #9b59b6;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Chart & Recent Reports --}}
    <div class="row g-4">
        {{-- Pie Chart --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-pie-chart-fill text-primary"></i> Status Distribution
                    </h5>
                    <small class="text-muted">
                        @if($departmentFilter === 'all')
                            All Departments
                        @else
                            {{ $departments->firstWhere('id', $departmentFilter)->name ?? 'Unknown' }}
                        @endif
                    </small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    
                    {{-- Legend --}}
                    <div class="mt-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div style="width: 16px; height: 16px; background-color: #3498db; border-radius: 3px;" class="me-2"></div>
                                    <small class="text-muted">Submitted ({{ $chartData['percentages'][0] }}%)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div style="width: 16px; height: 16px; background-color: #f39c12; border-radius: 3px;" class="me-2"></div>
                                    <small class="text-muted">In Progress ({{ $chartData['percentages'][1] }}%)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div style="width: 16px; height: 16px; background-color: #27ae60; border-radius: 3px;" class="me-2"></div>
                                    <small class="text-muted">Fixed ({{ $chartData['percentages'][2] }}%)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div style="width: 16px; height: 16px; background-color: #9b59b6; border-radius: 3px;" class="me-2"></div>
                                    <small class="text-muted">Approved ({{ $chartData['percentages'][3] }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Reports --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary"></i> Recent Reports
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentReports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Report #</th>
                                        <th class="border-0">Department</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReports as $report)
                                        <tr>
                                            <td class="fw-semibold">{{ $report->report_number }}</td>
                                            <td>
                                                <small class="text-muted">{{ $report->department->name }}</small>
                                            </td>
                                            <td>{!! $report->status_badge !!}</td>
                                            <td>
                                                <small class="text-muted">{{ $report->created_at->format('d M Y') }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 opacity-50"></i>
                            <p class="mt-2 mb-0">No reports available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart Configuration
    const ctx = document.getElementById('statusPieChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: {!! json_encode($chartData['colors']) !!},
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We use custom legend
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
});
</script>

<style>
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}

.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>
@endsection