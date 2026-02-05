@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Laporan
                    </h5>
                    <small class="opacity-75">Anda dapat mengedit laporan dengan status "Terkirim" atau "Ditolak"</small>
                </div>
                
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle fs-5 me-2"></i>
                            <div>
                                <strong>Informasi Penting:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Hanya laporan dengan status <strong>"Terkirim"</strong> atau <strong>"Ditolak"</strong> yang dapat diedit</li>
                                    <li>Foto yang sudah ada akan tetap tersimpan kecuali Anda upload foto baru</li>
                                    <li>Pastikan semua informasi yang diubah sudah benar sebelum menyimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Section 1: Informasi Audit -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-clipboard-check"></i> Informasi Audit
                                </h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="audit_type_id" class="form-label fw-semibold">
                                        <i class="bi bi-clipboard-check"></i> Tipe Audit <span class="text-danger">*</span>
                                    </label>
                                    <select name="audit_type_id" id="audit_type_id" class="form-select form-select-lg @error('audit_type_id') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe Audit</option>
                                        @foreach($auditTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('audit_type_id', $report->audit_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('audit_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="department_id" class="form-label fw-semibold">
                                        <i class="bi bi-building"></i> Departemen <span class="text-danger">*</span>
                                    </label>
                                    <select name="department_id" id="department_id" class="form-select form-select-lg @error('department_id') is-invalid @enderror" required>
                                        <option value="">Pilih Departemen</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id', $report->department_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 2: Lokasi & Masalah -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-geo-alt"></i> Lokasi & Jenis Masalah
                                </h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt"></i> Lokasi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control form-control-lg @error('location') is-invalid @enderror" 
                                           placeholder="Contoh: Ruang Meeting Lt. 2" 
                                           value="{{ old('location', $report->location) }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="issue_type" class="form-label fw-semibold">
                                        <i class="bi bi-exclamation-triangle"></i> Jenis Masalah <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="issue_type" id="issue_type" 
                                           class="form-control form-control-lg @error('issue_type') is-invalid @enderror" 
                                           placeholder="Contoh: Keran Bocor, Lampu Mati" 
                                           value="{{ old('issue_type', $report->issue_type) }}" required>
                                    @error('issue_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 3: Deskripsi -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-file-text"></i> Deskripsi
                                </h6>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi Masalah <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" rows="5" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Jelaskan masalah secara detail..." required>{{ old('description', $report->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 20 karakter untuk deskripsi yang jelas</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 4: Foto -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-camera"></i> Dokumentasi Foto
                                </h6>
                            </div>

                            <!-- Existing Photos -->
                            @if($report->photos && count($report->photos) > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-images"></i> Foto Saat Ini ({{ count($report->photos) }})
                                    </label>
                                    <div class="row g-3">
                                        @foreach($report->photos as $index => $photo)
                                            <div class="col-6 col-md-4">
                                                <div class="existing-photo-item">
                                                    <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Foto {{ $index + 1 }}">
                                                    <div class="photo-number">{{ $index + 1 }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="alert alert-info mt-3 py-2 small">
                                        <i class="bi bi-info-circle"></i> Foto di atas akan tetap tersimpan. Upload foto baru di bawah untuk menambahkan foto tambahan.
                                    </div>
                                </div>
                            @endif

                            <!-- Add More Photos -->
                            <div class="mb-3">
                                <label for="photos" class="form-label fw-semibold">
                                    <i class="bi bi-camera-fill"></i> Tambah Foto Baru (Opsional)
                                </label>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary w-100 btn-upload" onclick="openCamera()">
                                            <i class="bi bi-camera-fill fs-5 d-block mb-1"></i>
                                            <span class="d-block small">Buka Kamera</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary w-100 btn-upload" onclick="document.getElementById('photos').click()">
                                            <i class="bi bi-folder2-open fs-5 d-block mb-1"></i>
                                            <span class="d-block small">Pilih dari Galeri</span>
                                        </button>
                                    </div>
                                </div>

                                <input type="file" name="photos[]" id="photos" 
                                       class="d-none" 
                                       accept="image/*" 
                                       multiple>
                                <small class="text-muted d-block mb-2">Maksimal 5MB per foto. Kosongkan jika tidak ingin menambah foto.</small>
                                @error('photos.*')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                
                                <div id="photoPreview" class="row g-3 mt-3"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="{{ route('reports.show', $report) }}" class="btn btn-lg btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-lg btn-warning px-5" id="submitBtn">
                                <i class="bi bi-save"></i> Update Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .section-header {
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .form-control-lg, .form-select-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border-radius: 8px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #fda085;
        box-shadow: 0 0 0 0.2rem rgba(253, 160, 133, 0.25);
    }

    .btn-upload {
        padding: 1.2rem;
        border: 2px dashed #dee2e6;
        transition: all 0.3s;
    }

    .btn-upload:hover {
        border-color: #fda085;
        background: rgba(253, 160, 133, 0.05);
        transform: translateY(-2px);
    }

    .existing-photo-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .existing-photo-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .photo-number {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 32px;
        height: 32px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.875rem;
    }

    .photo-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .photo-preview-item:hover {
        transform: scale(1.05);
    }

    .photo-preview-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .photo-delete-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .photo-delete-btn:hover {
        background: rgb(220, 53, 69);
        transform: scale(1.1);
    }

    @media (max-width: 767.98px) {
        .btn-upload {
            padding: 1rem;
        }

        .existing-photo-item img,
        .photo-preview-item img {
            height: 150px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi untuk membuka kamera
    function openCamera() {
        const input = document.getElementById('photos');
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

    // Preview foto baru
    document.getElementById('photos')?.addEventListener('change', function(e) {
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '';
        
        const files = Array.from(e.target.files);
        
        if (files.length === 0) {
            return;
        }
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4';
                    
                    const previewItem = document.createElement('div');
                    previewItem.className = 'photo-preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview ' + (index + 1);
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'photo-delete-btn';
                    deleteBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                    deleteBtn.onclick = function() {
                        removePhoto(index);
                    };
                    
                    previewItem.appendChild(img);
                    previewItem.appendChild(deleteBtn);
                    col.appendChild(previewItem);
                    preview.appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
        });
    });

    // Fungsi untuk menghapus foto
    function removePhoto(index) {
        const input = document.getElementById('photos');
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

    // Form submission
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
</script>
@endpush