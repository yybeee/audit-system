@extends('layouts.app')

@section('title', 'Export & Archive Reports')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Export Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-excel"></i> Export & Archive Reports
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Warning Alert -->
                    <div class="alert alert-warning border-warning">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading mb-2">⚠️ Important Notice</h6>
                                <ul class="mb-0 small">
                                    <li>This action will <strong>permanently delete</strong> selected reports from database</li>
                                    <li>All photos will be <strong>removed from server</strong></li>
                                    <li>Data will be exported to <strong>Excel file first</strong></li>
                                    <li>This action <strong>CANNOT be undone</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-info bg-opacity-10 border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-range fs-1 text-info"></i>
                                    <h3 class="mt-2 mb-0">{{ $availableYears->count() }}</h3>
                                    <p class="text-muted mb-0">Available Years</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success bg-opacity-10 border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-text fs-1 text-success"></i>
                                    <h3 class="mt-2 mb-0">{{ array_sum($reportStats['years'] ?? []) }}</h3>
                                    <p class="text-muted mb-0">Total Reports</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Form -->
                    <form action="{{ route('reports.exportAndDeleteByPeriod') }}" method="POST" id="exportForm">
                        @csrf

                        <!-- Period Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar3"></i> Select Period Type
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="period_type" id="period_month" value="month" required>
                                <label class="btn btn-outline-primary" for="period_month">
                                    <i class="bi bi-calendar-month"></i> Monthly
                                </label>

                                <input type="radio" class="btn-check" name="period_type" id="period_year" value="year" required>
                                <label class="btn btn-outline-primary" for="period_year">
                                    <i class="bi bi-calendar"></i> Yearly
                                </label>
                            </div>
                        </div>

                        <!-- Year Selection -->
                        <div class="mb-4">
                            <label for="year" class="form-label fw-bold">
                                <i class="bi bi-calendar4"></i> Select Year
                            </label>
                            <select name="year" id="year" class="form-select" required>
                                <option value="">-- Select Year --</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" data-count="{{ $reportStats['years'][$year] ?? 0 }}">
                                        {{ $year }} ({{ $reportStats['years'][$year] ?? 0 }} reports)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Selection (Hidden by default) -->
                        <div class="mb-4 d-none" id="monthSelection">
                            <label for="month" class="form-label fw-bold">
                                <i class="bi bi-calendar2-month"></i> Select Month
                            </label>
                            <select name="month" id="month" class="form-select">
                                <option value="">-- Select Month --</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="monthReportCount" class="mt-2 small text-muted"></div>
                        </div>

                        <!-- Preview Info -->
                        <div class="alert alert-info d-none" id="previewInfo">
                            <strong><i class="bi bi-info-circle"></i> Preview:</strong>
                            <div id="previewText" class="mt-2"></div>
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confirm_delete" value="1" id="confirmDelete" required>
                                <label class="form-check-label fw-bold text-danger" for="confirmDelete">
                                    <i class="bi bi-check-square"></i> 
                                    I understand this will permanently delete the selected reports and cannot be undone
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg" id="submitBtn" disabled>
                                <i class="bi bi-download"></i> Export to Excel & Delete from Database
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Available Reports by Period -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-list-ul"></i> Available Reports by Period</h6>
                </div>
                <div class="card-body">
                    @foreach($availableYears as $year)
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary">
                                <i class="bi bi-calendar-check"></i> {{ $year }} 
                                <span class="badge bg-primary">{{ $reportStats['years'][$year] ?? 0 }} reports</span>
                            </h6>
                            
                            @if(isset($reportStats['months'][$year]))
                                <div class="row g-2 ms-3">
                                    @foreach($reportStats['months'][$year] as $month => $count)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="small text-muted">
                                                <i class="bi bi-calendar3"></i> 
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}: 
                                                <strong>{{ $count }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
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
    
    .btn-check:checked + .btn-outline-primary {
        background-color: #047857;
        border-color: #047857;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodMonth = document.getElementById('period_month');
    const periodYear = document.getElementById('period_year');
    const yearSelect = document.getElementById('year');
    const monthSelection = document.getElementById('monthSelection');
    const monthSelect = document.getElementById('month');
    const confirmCheckbox = document.getElementById('confirmDelete');
    const submitBtn = document.getElementById('submitBtn');
    const previewInfo = document.getElementById('previewInfo');
    const previewText = document.getElementById('previewText');
    const monthReportCount = document.getElementById('monthReportCount');
    
    const reportStats = @json($reportStats);
    
    periodMonth.addEventListener('change', function() {
        if (this.checked) {
            monthSelection.classList.remove('d-none');
            monthSelect.required = true;
            updatePreview();
        }
    });
    
    periodYear.addEventListener('change', function() {
        if (this.checked) {
            monthSelection.classList.add('d-none');
            monthSelect.required = false;
            monthSelect.value = '';
            updatePreview();
        }
    });
    
    yearSelect.addEventListener('change', function() {
        updatePreview();
        updateMonthOptions();
    });
    
    monthSelect.addEventListener('change', function() {
        updatePreview();
        updateMonthReportCount();
    });
    
    confirmCheckbox.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });
    
    function updateMonthOptions() {
        const selectedYear = yearSelect.value;
        if (!selectedYear) return;
        
        Array.from(monthSelect.options).forEach(option => {
            if (option.value === '') return;
            
            const month = parseInt(option.value);
            const count = reportStats.months?.[selectedYear]?.[month] || 0;
            
            if (count > 0) {
                option.text = `${option.text.split(' (')[0]} (${count} reports)`;
                option.disabled = false;
            } else {
                option.text = `${option.text.split(' (')[0]} (No reports)`;
                option.disabled = true;
            }
        });
    }
    
    function updateMonthReportCount() {
        const selectedYear = yearSelect.value;
        const selectedMonth = monthSelect.value;
        
        if (selectedYear && selectedMonth) {
            const count = reportStats.months?.[selectedYear]?.[parseInt(selectedMonth)] || 0;
            monthReportCount.innerHTML = `<i class="bi bi-info-circle"></i> <strong>${count}</strong> reports will be exported and deleted`;
        }
    }
    
    function updatePreview() {
        const periodType = document.querySelector('input[name="period_type"]:checked')?.value;
        const selectedYear = yearSelect.value;
        const selectedMonth = monthSelect.value;
        
        if (!periodType || !selectedYear) {
            previewInfo.classList.add('d-none');
            return;
        }
        
        let count = 0;
        let periodLabel = '';
        
        if (periodType === 'year') {
            count = reportStats.years?.[selectedYear] || 0;
            periodLabel = `Year ${selectedYear}`;
        } else if (periodType === 'month' && selectedMonth) {
            count = reportStats.months?.[selectedYear]?.[parseInt(selectedMonth)] || 0;
            const monthName = new Date(selectedYear, selectedMonth - 1).toLocaleString('default', { month: 'long' });
            periodLabel = `${monthName} ${selectedYear}`;
        } else {
            previewInfo.classList.add('d-none');
            return;
        }
        
        previewText.innerHTML = `
            You will export and delete <strong class="text-danger">${count} reports</strong> from <strong>${periodLabel}</strong>
        `;
        previewInfo.classList.remove('d-none');
    }
    
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        const periodType = document.querySelector('input[name="period_type"]:checked')?.value;
        const selectedYear = yearSelect.value;
        const selectedMonth = monthSelect.value;
        
        let periodLabel = '';
        let count = 0;
        
        if (periodType === 'year') {
            count = reportStats.years?.[selectedYear] || 0;
            periodLabel = `Year ${selectedYear}`;
        } else {
            count = reportStats.months?.[selectedYear]?.[parseInt(selectedMonth)] || 0;
            const monthName = new Date(selectedYear, selectedMonth - 1).toLocaleString('default', { month: 'long' });
            periodLabel = `${monthName} ${selectedYear}`;
        }
        
        const confirmed = confirm(
            `⚠️ FINAL CONFIRMATION\n\n` +
            `Period: ${periodLabel}\n` +
            `Reports: ${count}\n\n` +
            `This will:\n` +
            `✓ Export data to Excel\n` +
            `✗ Delete ${count} reports from database\n` +
            `✗ Delete all related photos from server\n\n` +
            `This action CANNOT be undone!\n\n` +
            `Are you absolutely sure?`
        );
        
        if (!confirmed) {
            e.preventDefault();
        }
    });
});
</script>
@endpush