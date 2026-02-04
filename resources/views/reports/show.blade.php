@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Report Detail Card -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text"></i> Detail Laporan
                    </h5>
                    {!! $report->status_badge !!}
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="200" class="d-none d-md-table-cell">Nomor Laporan:</th>
                                <th class="d-md-none small">No. Laporan:</th>
                                <td class="fw-bold">{{ $report->report_number }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Tipe Audit:</th>
                                <th class="d-md-none small">Tipe:</th>
                                <td>
                                    <span class="badge bg-info" style="font-size: 0.75rem;">{{ $report->auditType->name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Departemen:</th>
                                <th class="d-md-none small">Dept:</th>
                                <td class="small">{{ $report->department->name }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Lokasi:</th>
                                <th class="d-md-none small">Lok:</th>
                                <td class="small">{{ $report->location }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Jenis Masalah:</th>
                                <th class="d-md-none small">Masalah:</th>
                                <td class="small">{{ $report->issue_type }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Deskripsi:</th>
                                <th class="d-md-none small">Desk:</th>
                                <td class="small">{{ $report->description }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Dilaporkan Oleh:</th>
                                <th class="d-md-none small">Oleh:</th>
                                <td class="small">{{ $report->auditor->name }}</td>
                            </tr>
                            <tr>
                                <th class="d-none d-md-table-cell">Tanggal Laporan:</th>
                                <th class="d-md-none small">Tanggal:</th>
                                <td class="small">{{ $report->submitted_at ? $report->submitted_at->format('d M Y H:i') : '-' }}</td>
                            </tr>
                            @if($report->fixed_at)
                                <tr>
                                    <th class="d-none d-md-table-cell">Diperbaiki Pada:</th>
                                    <th class="d-md-none small">Diperbaiki:</th>
                                    <td class="small">{{ $report->fixed_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($report->approved_at)
                                <tr>
                                    <th class="d-none d-md-table-cell">Disetujui Pada:</th>
                                    <th class="d-md-none small">Disetujui:</th>
                                    <td class="small">{{ $report->approved_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($report->status === 'rejected' && $report->rejection_reason)
                                <tr>
                                    <th class="d-none d-md-table-cell">Alasan Penolakan:</th>
                                    <th class="d-md-none small">Alasan:</th>
                                    <td class="text-danger small">{{ $report->rejection_reason }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <!-- Photos from Auditor -->
                    @php
                        $photos = is_array($report->photos) ? $report->photos : (is_string($report->photos) ? json_decode($report->photos, true) : []);
                    @endphp
                    @if($photos && count($photos) > 0)
                        <h6 class="mt-4 mb-3"><i class="bi bi-camera"></i> Foto Masalah:</h6>
                        <div class="row g-2">
                            @foreach($photos as $photo)
                                <div class="col-6 col-md-4">
                                    <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Foto Masalah" style="cursor: pointer;" onclick="openImageModal('{{ Storage::url($photo) }}')">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Alert Deadline -->
                    @if($report->deadline)
                        @php
                            $deadline = \Carbon\Carbon::parse($report->deadline);
                            $today = \Carbon\Carbon::today();
                            $daysLeft = $today->diffInDays($deadline, false);
                            $isFixed = in_array($report->status, ['fixed', 'approved']);
                        @endphp
                        
                        <div class="alert {{ $isFixed ? 'alert-success' : ($daysLeft < 0 ? 'alert-danger' : 'alert-info') }} mt-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <strong><i class="bi bi-calendar-event"></i> Deadline:</strong> 
                                    {{ $deadline->locale('id')->format('d F Y') }}
                                    
                                    @if($isFixed)
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-check-circle-fill"></i> Selesai Tepat Waktu
                                        </span>
                                    @else
                                        @if($daysLeft > 0)
                                            <span class="badge bg-success ms-2">{{ $daysLeft }} hari tersisa</span>
                                        @elseif($daysLeft == 0)
                                            <span class="badge bg-warning text-dark ms-2">Deadline hari ini!</span>
                                        @else
                                            <span class="badge bg-danger ms-2">Terlambat {{ abs($daysLeft) }} hari</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <!-- Keterangan Deadline -->
                            @if($report->deadline_reason)
                                <div class="mt-2 pt-2 border-top border-opacity-25">
                                    <small><i class="bi bi-chat-left-text"></i> <strong>Keterangan:</strong> {{ $report->deadline_reason }}</small>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Response Section -->
            @if($report->responses && $report->responses->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Respons Departemen</h5>
                    </div>
                    <div class="card-body">
                        @foreach($report->responses as $response)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $response->user->name }}</h6>
                                    <small class="text-muted">{{ $response->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mb-2">{{ $response->description }}</p>
                                
                                @php
                                    $responsePhotos = is_array($response->photos) ? $response->photos : (is_string($response->photos) ? json_decode($response->photos, true) : []);
                                @endphp
                                @if($responsePhotos && count($responsePhotos) > 0)
                                    <h6 class="mb-2"><i class="bi bi-camera"></i> Foto Perbaikan:</h6>
                                    <div class="row g-2">
                                        @foreach($responsePhotos as $photo)
                                            <div class="col-6 col-md-4">
                                                <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Foto Perbaikan" style="cursor: pointer;" onclick="openImageModal('{{ Storage::url($photo) }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightning-charge"></i> Aksi</h6>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role === 'staff_departemen' && auth()->user()->department_id === $report->department_id)
                        @if($report->status === 'submitted')
                            <!-- Tombol Start Progress - Hanya untuk status submitted -->
                            <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#startProgressModal">
                                <i class="bi bi-play-circle"></i> Mulai Perbaikan
                            </button>
                            
                            <!-- Peringatan untuk klik Start Progress dulu -->
                            <div class="alert alert-warning py-2 small mb-2">
                                <i class="bi bi-info-circle"></i> Klik "Mulai Perbaikan" terlebih dahulu untuk mengisi respons
                            </div>
                        @endif

                        @if($report->status === 'in_progress')
                            <!-- Tombol Submit Response - Hanya muncul setelah in_progress -->
                            <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#responseModal">
                                <i class="bi bi-check-circle"></i> Kirim Respons Perbaikan
                            </button>
                        @endif

                        @if($report->status === 'rejected')
                            <!-- Untuk status rejected, bisa langsung kirim respons ulang -->
                            <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#responseModal">
                                <i class="bi bi-check-circle"></i> Kirim Respons Perbaikan
                            </button>
                            
                            <div class="alert alert-danger py-2 small mb-2">
                                <i class="bi bi-exclamation-triangle"></i> Laporan ditolak, perbaiki dan kirim respons baru
                            </div>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'auditor' && auth()->user()->id === $report->auditor_id)
                        @if($report->status === 'fixed')
                            <form action="{{ route('reports.approve', $report) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui laporan ini?')">
                                    <i class="bi bi-check-circle"></i> Setujui Laporan
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Tolak Laporan
                            </button>
                        @endif

                        @if(in_array($report->status, ['submitted', 'rejected']))
                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-pencil"></i> Edit Laporan
                            </a>

                            <form action="{{ route('reports.destroy', $report) }}" method="POST" class="mb-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Hapus laporan ini? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="bi bi-trash"></i> Hapus Laporan
                                </button>
                            </form>
                        @endif
                    @endif

                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal (for Staff Departemen) -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reports.respond', $report) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Respons Perbaikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Perbaikan *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan apa yang telah diperbaiki..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (Setelah Perbaikan) *</label>
                        <!-- Button Kamera dan Galeri -->
                        <div class="btn-group w-100 mb-2" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openResponseCamera()">
                                <i class="bi bi-camera-fill"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('responsePhotos').click()">
                                <i class="bi bi-folder2-open"></i> Pilih dari Galeri
                            </button>
                        </div>
                        <input type="file" name="photos[]" id="responsePhotos" class="form-control d-none" accept="image/*" multiple required>
                        <small class="text-muted d-block">Unggah foto yang menunjukkan kondisi setelah diperbaiki</small>
                        
                        <div id="responsePhotoList" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Respons</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Start Progress -->
<div class="modal fade" id="startProgressModal" tabindex="-1" aria-labelledby="startProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reports.start-progress', $report->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="startProgressModalLabel">Mulai Perbaikan Audit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Setelah memulai perbaikan, Anda dapat mengirim respons dengan foto bukti perbaikan.
                    </div>
                    <!-- Tanggal Deadline -->
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Tanggal Deadline <span class="text-danger">*</span></label>
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
                        <small class="text-muted">Pilih tanggal kapan masalah ini harus diselesaikan.</small>
                    </div>
                    <!-- Keterangan Deadline -->
                    <div class="mb-3">
                        <label for="deadline_reason" class="form-label">Keterangan Deadline <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deadline_reason') is-invalid @enderror"
                                  id="deadline_reason"
                                  name="deadline_reason"
                                  rows="3"
                                  required
                                  placeholder="Contoh: Menunggu pengiriman suku cadang dari vendor, estimasi tiba hari X..."
                                  >{{ old('deadline_reason') }}</textarea>
                        @error('deadline_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Jelaskan alasan dan rencana untuk menyelesaikan perbaikan ini.</small>
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

<!-- Reject Modal (for Supervisor) -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reports.reject', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Tolak Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Laporan ini akan dikembalikan ke departemen untuk diperbaiki ulang.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan *</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Jelaskan mengapa laporan ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="img-fluid w-100" alt="Gambar Penuh">
            </div>
        </div>
    </div>
</div>

@endsection

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
                img.style.width = '50px';
                img.style.height = '50px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex-grow-1';
                fileInfo.innerHTML = `
                    <div class="fw-bold small">${file.name}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${(file.size / 1024).toFixed(1)} KB</div>
                `;

                // Tombol hapus foto
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

// Hapus foto dari response
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
</script>
@endpush