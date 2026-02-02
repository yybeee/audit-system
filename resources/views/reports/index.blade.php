@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text"></i> 
                        @if(auth()->user()->role === 'auditor')
                            Laporan Saya
                        @elseif(auth()->user()->role === 'staff_departemen')
                            Laporan yang Ditugaskan
                        @elseif(auth()->user()->role === 'supervisor')
                            Laporan untuk Direview
                        @else
                            Semua Laporan
                        @endif
                    </h5>
                    
                    @if(auth()->user()->role === 'auditor')
                        <a href="{{ route('reports.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Buat Laporan
                        </a>
                    @endif
                </div>
                
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                            <div class="row g-3">
                                <!-- Search -->
                                <div class="col-md-4">
                                    <label class="form-label">Cari Laporan</label>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Nomor, lokasi, jenis masalah..." 
                                               value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Status -->
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Terkirim</option>
                                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                        <option value="fixed" {{ request('status') === 'fixed' ? 'selected' : '' }}>Selesai Diperbaiki</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>

                                <!-- Filter Departemen -->
                                <div class="col-md-3">
                                    <label class="form-label">Departemen</label>
                                    <select name="department" class="form-select" onchange="this.form.submit()">
                                        <option value="">Semua Departemen</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Periode Cepat -->
                                <div class="col-md-2">
                                    <label class="form-label">Periode</label>
                                    <select name="period" class="form-select" onchange="this.form.submit()">
                                        <option value="">Pilih Periode</option>
                                        <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                                        <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                                        <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                                    </select>
                                </div>

                                <!-- Filter Tanggal Custom -->
                                <div class="col-md-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ request('date_from') }}" 
                                           onchange="this.form.submit()">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ request('date_to') }}" 
                                           onchange="this.form.submit()">
                                </div>

                                <!-- Reset Filter Button -->
                                <div class="col-md-6 d-flex align-items-end">
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Status Filter (Mobile Friendly) -->
                    <div class="mb-3 d-md-none">
                        <div class="btn-group btn-group-sm d-flex flex-wrap" role="group">
                            <a href="?status=all" class="btn btn-outline-primary {{ request('status', 'all') === 'all' ? 'active' : '' }}">
                                Semua
                            </a>
                            <a href="?status=submitted" class="btn btn-outline-primary {{ request('status') === 'submitted' ? 'active' : '' }}">
                                Terkirim
                            </a>
                            <a href="?status=in_progress" class="btn btn-outline-warning {{ request('status') === 'in_progress' ? 'active' : '' }}">
                                Proses
                            </a>
                            <a href="?status=fixed" class="btn btn-outline-info {{ request('status') === 'fixed' ? 'active' : '' }}">
                                Selesai
                            </a>
                            <a href="?status=approved" class="btn btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">
                                Disetujui
                            </a>
                            <a href="?status=rejected" class="btn btn-outline-danger {{ request('status') === 'rejected' ? 'active' : '' }}">
                                Ditolak
                            </a>
                        </div>
                    </div>

                    <!-- Info jumlah hasil -->
                    @if(request()->hasAny(['status', 'department', 'period', 'date_from', 'date_to', 'search']))
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Menampilkan {{ $reports->total() }} laporan dari hasil filter
                        </div>
                    @endif

                    @if($reports->count() > 0)
                        <!-- Desktop View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nomor Laporan</th>
                                        <th>Tipe Audit</th>
                                        <th>Departemen</th>
                                        <th>Lokasi</th>
                                        <th>Jenis Masalah</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td class="fw-bold">{{ $report->report_number }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $report->auditType->name }}</span>
                                            </td>
                                            <td>{{ $report->department->name }}</td>
                                            <td>{{ $report->location }}</td>
                                            <td>{{ $report->issue_type }}</td>
                                            <td>{!! $report->status_badge !!}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $report->created_at->format('d M Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="d-md-none">
                            @foreach($reports as $report)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 fw-bold">{{ $report->report_number }}</h6>
                                            {!! $report->status_badge !!}
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-info">{{ $report->auditType->name }}</span>
                                            <span class="badge bg-secondary">{{ $report->department->name }}</span>
                                        </div>
                                        
                                        <p class="mb-1">
                                            <i class="bi bi-geo-alt"></i> <strong>{{ $report->location }}</strong>
                                        </p>
                                        <p class="mb-1 text-muted">
                                            <i class="bi bi-exclamation-triangle"></i> {{ $report->issue_type }}
                                        </p>
                                        <p class="mb-2 text-muted small">
                                            <i class="bi bi-calendar"></i> {{ $report->created_at->format('d M Y H:i') }}
                                        </p>
                                        
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $reports->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            <p>Tidak ada laporan ditemukan</p>
                            @if(auth()->user()->role === 'auditor' && !request()->hasAny(['status', 'department', 'period', 'date_from', 'date_to', 'search']))
                                <a href="{{ route('reports.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle"></i> Buat Laporan Pertama
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
    .btn-group-sm .btn {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
    
    @media (max-width: 767.98px) {
        .btn-group {
            gap: 0.25rem;
        }
        .btn-group .btn {
            flex: 1 1 auto;
        }
    }
</style>
@endpush