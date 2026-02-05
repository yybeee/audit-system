@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Laporan
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Report Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-file-earmark-text-fill"></i> {{ $report->report_number }}
                            </h5>
                            <small class="opacity-75">
                                <i class="bi bi-person"></i> {{ $report->auditor->name }} • 
                                <i class="bi bi-calendar3"></i> {{ $report->submitted_at ? $report->submitted_at->format('d M Y H:i') : 'Belum terkirim' }}
                            </small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            {!! $report->status_badge !!}
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Info Grid -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-clipboard-check text-primary"></i> Tipe Audit
                                </label>
                                <div class="info-value">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                        {{ $report->auditType->name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-building text-primary"></i> Departemen
                                </label>
                                <div class="info-value">{{ $report->department->name }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-geo-alt text-primary"></i> Lokasi
                                </label>
                                <div class="info-value">{{ $report->location }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-exclamation-triangle text-primary"></i> Jenis Masalah
                                </label>
                                <div class="info-value">{{ $report->issue_type }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="bi bi-file-text text-primary"></i> Deskripsi Masalah
                                </label>
                                <div class="info-value description-text">{{ $report->description }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    @if($report->fixed_at || $report->approved_at)
                        <div class="timeline mt-4 pt-4 border-top">
                            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Timeline</h6>
                            <div class="timeline-items">
                                <div class="timeline-item">
                                    <div class="timeline-icon bg-primary">
                                        <i class="bi bi-send"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <small class="text-muted">{{ $report->submitted_at->format('d M Y H:i') }}</small>
                                        <p class="mb-0 fw-semibold">Laporan Terkirim</p>
                                    </div>
                                </div>
                                @if($report->fixed_at)
                                    <div class="timeline-item">
                                        <div class="timeline-icon bg-success">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <small class="text-muted">{{ $report->fixed_at->format('d M Y H:i') }}</small>
                                            <p class="mb-0 fw-semibold">Selesai Diperbaiki</p>
                                        </div>
                                    </div>
                                @endif
                                @if($report->approved_at)
                                    <div class="timeline-item">
                                        <div class="timeline-icon bg-success">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <small class="text-muted">{{ $report->approved_at->format('d M Y H:i') }}</small>
                                            <p class="mb-0 fw-semibold">Disetujui</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Photos Card -->
            @php
                $photos = is_array($report->photos) ? $report->photos : (is_string($report->photos) ? json_decode($report->photos, true) : []);
            @endphp
            @if($photos && count($photos) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-camera-fill text-primary"></i> Foto Masalah ({{ count($photos) }})
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            @foreach($photos as $index => $photo)
                                <div class="col-6 col-md-4">
                                    <div class="photo-item" onclick="openImageModal('{{ Storage::url($photo) }}')">
                                        <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Foto Masalah {{ $index + 1 }}">
                                        <div class="photo-overlay">
                                            <i class="bi bi-zoom-in"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Deadline Alert -->
            @if($report->deadline)
                @php
                    $deadline = \Carbon\Carbon::parse($report->deadline);
                    $today = \Carbon\Carbon::today();
                    $daysLeft = $today->diffInDays($deadline, false);
                    $isFixed = in_array($report->status, ['fixed', 'approved']);
                @endphp
                
                <div class="card shadow-sm mb-4 {{ $isFixed ? 'border-success' : ($daysLeft < 0 ? 'border-danger' : 'border-warning') }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="deadline-icon {{ $isFixed ? 'bg-success' : ($daysLeft < 0 ? 'bg-danger' : 'bg-warning') }}">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2">Deadline Perbaikan</h6>
                                <p class="mb-2">
                                    <strong>{{ $deadline->locale('id')->format('d F Y') }}</strong>
                                    
                                    @if($isFixed)
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-check-circle-fill"></i> Selesai Tepat Waktu
                                        </span>
                                    @else
                                        @if($daysLeft > 0)
                                            <span class="badge bg-success ms-2">
                                                <i class="bi bi-hourglass-split"></i> {{ $daysLeft }} hari tersisa
                                            </span>
                                        @elseif($daysLeft == 0)
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="bi bi-exclamation-triangle"></i> Deadline hari ini!
                                            </span>
                                        @else
                                            <span class="badge bg-danger ms-2">
                                                <i class="bi bi-x-circle"></i> Terlambat {{ abs($daysLeft) }} hari
                                            </span>
                                        @endif
                                    @endif
                                </p>
                                @if($report->deadline_reason)
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-chat-left-text"></i> Keterangan:
                                        </small>
                                        <p class="mb-0 small">{{ $report->deadline_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rejection Reason -->
            @if($report->status === 'rejected' && $report->rejection_reason)
                <div class="card shadow-sm mb-4 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="deadline-icon bg-danger">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-danger mb-2">Alasan Penolakan</h6>
                                <p class="mb-0">{{ $report->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Response Section -->
            @if($report->responses && $report->responses->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-chat-dots-fill text-primary"></i> Respons Perbaikan ({{ $report->responses->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($report->responses as $response)
                            <div class="response-item">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar-circle me-3">
                                        {{ substr($response->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $response->user->name }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> {{ $response->created_at->format('d M Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="response-content">
                                    <p class="mb-3">{{ $response->description }}</p>
                                    
                                    @php
                                        $responsePhotos = is_array($response->photos) ? $response->photos : (is_string($response->photos) ? json_decode($response->photos, true) : []);
                                    @endphp
                                    @if($responsePhotos && count($responsePhotos) > 0)
                                        <div class="mb-3">
                                            <small class="text-muted fw-semibold d-block mb-2">
                                                <i class="bi bi-camera"></i> Foto Perbaikan ({{ count($responsePhotos) }})
                                            </small>
                                            <div class="row g-2">
                                                @foreach($responsePhotos as $index => $photo)
                                                    <div class="col-6 col-md-4">
                                                        <div class="photo-item" onclick="openImageModal('{{ Storage::url($photo) }}')">
                                                            <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Foto Perbaikan {{ $index + 1 }}">
                                                            <div class="photo-overlay">
                                                                <i class="bi bi-zoom-in"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-sidebar">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> Aksi Tersedia
                    </h6>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role === 'staff_departemen' && auth()->user()->department_id === $report->department_id)
                        @if($report->status === 'submitted')
                            <button type="button" class="btn btn-warning w-100 mb-3" data-bs-toggle="modal" data-bs-target="#startProgressModal">
                                <i class="bi bi-play-circle-fill"></i> Mulai Perbaikan
                            </button>
                            
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-info-circle"></i> Klik "Mulai Perbaikan" untuk mengisi respons
                            </div>
                        @endif

                        @if($report->status === 'in_progress')
                            <button type="button" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#responseModal">
                                <i class="bi bi-check-circle-fill"></i> Kirim Respons Perbaikan
                            </button>
                        @endif

                        @if($report->status === 'rejected')
                            <button type="button" class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#responseModal">
                                <i class="bi bi-arrow-repeat"></i> Kirim Respons Ulang
                            </button>
                            
                            <div class="alert alert-danger py-2 small">
                                <i class="bi bi-exclamation-triangle"></i> Laporan ditolak, perbaiki dan kirim ulang
                            </div>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'auditor' && auth()->user()->id === $report->auditor_id)
                        @if($report->status === 'fixed')
                            <form action="{{ route('reports.approve', $report) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui laporan ini?')">
                                    <i class="bi bi-check-circle-fill"></i> Setujui Laporan
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger w-100 mb-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle-fill"></i> Tolak Laporan
                            </button>

                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle"></i> Verifikasi perbaikan sebelum menyetujui
                            </div>
                        @endif

                        @if(in_array($report->status, ['submitted', 'rejected']))
                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-pencil-square"></i> Edit Laporan
                            </a>

                            <form action="{{ route('reports.destroy', $report) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 mb-3" onclick="return confirm('Hapus laporan ini? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="bi bi-trash3"></i> Hapus Laporan
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- Report Info -->
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <h6 class="small fw-bold mb-2">Informasi Laporan</h6>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-person text-muted"></i> 
                                    <strong>Pelapor:</strong><br>
                                    <span class="ms-3">{{ $report->auditor->name }}</span>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-calendar3 text-muted"></i> 
                                    <strong>Dibuat:</strong><br>
                                    <span class="ms-3">{{ $report->created_at->format('d M Y H:i') }}</span>
                                </li>
                                @if($report->updated_at != $report->created_at)
                                    <li class="mb-0">
                                        <i class="bi bi-pencil text-muted"></i> 
                                        <strong>Update Terakhir:</strong><br>
                                        <span class="ms-3">{{ $report->updated_at->format('d M Y H:i') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal (for Staff Departemen) -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('reports.respond', $report) }}" method="POST" enctype="multipart/form-data" id="responseForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-send"></i> Kirim Respons Perbaikan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Perbaikan <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan secara detail:&#10;- Apa yang telah diperbaiki?&#10;- Bagaimana cara memperbaikinya?&#10;- Apakah masalah sudah teratasi sepenuhnya?"></textarea>
                        <small class="text-muted">Minimal 20 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Setelah Perbaikan <span class="text-danger">*</span></label>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100 btn-sm" onclick="openResponseCamera()">
                                    <i class="bi bi-camera-fill"></i> Kamera
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100 btn-sm" onclick="document.getElementById('responsePhotos').click()">
                                    <i class="bi bi-folder2-open"></i> Galeri
                                </button>
                            </div>
                        </div>
                        <input type="file" name="photos[]" id="responsePhotos" class="d-none" accept="image/*" multiple required>
                        <small class="text-muted d-block">Upload foto yang menunjukkan kondisi setelah diperbaiki</small>
                        
                        <div id="responsePhotoList" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitResponseBtn">
                        <i class="bi bi-send"></i> Kirim Respons
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Start Progress -->
<div class="modal fade" id="startProgressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('reports.start-progress', $report->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-play-circle"></i> Mulai Perbaikan Audit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tentukan deadline dan rencana perbaikan
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label fw-semibold">Tanggal Deadline <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('deadline') is-invalid @enderror" 
                               id="deadline" 
                               name="deadline" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               value="{{ old('deadline') }}"
                               required>
                        @error('deadline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih tanggal target penyelesaian</small>
                    </div>
                    <div class="mb-3">
                        <label for="deadline_reason" class="form-label fw-semibold">Keterangan & Rencana <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deadline_reason') is-invalid @enderror"
                                  id="deadline_reason"
                                  name="deadline_reason"
                                  rows="4"
                                  required
                                  placeholder="Contoh: Menunggu pengiriman suku cadang dari vendor, estimasi tiba 3 hari. Perbaikan akan dilakukan segera setelah part tersedia."
                                  >{{ old('deadline_reason') }}</textarea>
                        @error('deadline_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Jelaskan rencana dan alasan deadline</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-play-circle"></i> Mulai Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal (for Auditor) -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('reports.reject', $report) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-x-circle"></i> Tolak Laporan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Laporan akan dikembalikan ke departemen untuk diperbaiki ulang
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Jelaskan secara detail:&#10;- Apa yang kurang dari perbaikan?&#10;- Apa yang perlu diperbaiki?&#10;- Standar apa yang belum terpenuhi?"></textarea>
                        <small class="text-muted">Berikan penjelasan yang jelas agar departemen dapat memperbaiki</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Tolak Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index: 1;"></button>
                <img src="" id="modalImage" class="img-fluid w-100 rounded" alt="Gambar Penuh">
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .sticky-sidebar {
        position: sticky;
        top: 80px;
    }

    /* Info Item Styling */
    .info-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #667eea;
    }

    .info-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
    }

    .description-text {
        line-height: 1.7;
        white-space: pre-wrap;
    }

    /* Photo Item */
    .photo-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .photo-item:hover {
        transform: scale(1.05);
    }

    .photo-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .photo-item:hover .photo-overlay {
        opacity: 1;
    }

    .photo-overlay i {
        color: white;
        font-size: 2rem;
    }

    /* Timeline */
    .timeline-items {
        position: relative;
    }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: -24px;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 15px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .timeline-content {
        flex: 1;
        padding-top: 5px;
    }

    /* Deadline Icon */
    .deadline-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    /* Response Item */
    .response-item {
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .response-item:last-child {
        margin-bottom: 0;
    }

    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .response-content {
        padding: 1rem;
        background: white;
        border-radius: 8px;
    }

    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .info-item {
            padding: 10px;
            margin-bottom: 10px;
        }

        .photo-item img {
            height: 150px;
        }

        .sticky-sidebar {
            position: static;
        }

        .deadline-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }

    @media (max-width: 991.98px) {
        .sticky-sidebar {
            margin-top: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Open camera for response photos
function openResponseCamera() {
    const input = document.getElementById('responsePhotos');
    const cameraInput = document.createElement('input');
    cameraInput.type = 'file';
    cameraInput.accept = 'image/*';
    cameraInput.capture = 'environment';
    cameraInput.multiple = true;

    cameraInput.onchange = function(e) {
        const dataTransfer = new DataTransfer();
        const existingFiles = Array.from(input.files);
        const newFiles = Array.from(e.target.files);

        [...existingFiles, ...newFiles].forEach(file => {
            dataTransfer.items.add(file);
        });

        input.files = dataTransfer.files;

        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    };

    cameraInput.click();
}

// Response photo preview
document.getElementById('responsePhotos')?.addEventListener('change', function(e) {
    const photoList = document.getElementById('responsePhotoList');
    photoList.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    if (files.length === 0) return;
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const fileItem = document.createElement('div');
                fileItem.className = 'border rounded p-2 mb-2 d-flex align-items-center gap-2';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '60px';
                img.style.height = '60px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex-grow-1';
                fileInfo.innerHTML = `
                    <div class="fw-bold small">${file.name}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${(file.size / 1024).toFixed(1)} KB</div>
                `;

                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'btn btn-danger btn-sm';
                deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
                deleteBtn.onclick = function() {
                    removeResponsePhoto(index);
                };
                
                fileItem.appendChild(img);
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(deleteBtn);
                photoList.appendChild(fileItem);
            };
            
            reader.readAsDataURL(file);
        }
    });
});

// Remove photo from response
function removeResponsePhoto(index) {
    const input = document.getElementById('responsePhotos');
    const dataTransfer = new DataTransfer();
    const files = Array.from(input.files);

    files.forEach((file, i) => {
        if (i !== index) {
            dataTransfer.items.add(file);
        }
    });

    input.files = dataTransfer.files;

    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

// Form validation
document.getElementById('responseForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('submitResponseBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
});
</script>
@endpush