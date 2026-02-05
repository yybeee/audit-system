@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Progress Steps -->
            <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="step-item active">
                            <div class="step-circle">
                                <i class="bi bi-camera"></i>
                            </div>
                            <small class="step-label d-none d-md-block">Foto</small>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item">
                            <div class="step-circle">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <small class="step-label d-none d-md-block">Lokasi</small>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item">
                            <div class="step-circle">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <small class="step-label d-none d-md-block">Detail</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle-fill"></i> Buat Laporan Audit Baru
                    </h5>
                    <small class="opacity-75">Lengkapi semua informasi yang diperlukan</small>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf

                        <!-- Section 1: Foto -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-camera-fill"></i> 1. Dokumentasi Foto
                                </h6>
                                <small class="text-muted">Upload foto yang menunjukkan masalah dengan jelas</small>
                            </div>

                            <div class="photo-upload-area">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary w-100 btn-upload" onclick="openCamera()">
                                            <i class="bi bi-camera-fill fs-4 d-block mb-2"></i>
                                            <span class="d-block small">Buka Kamera</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary w-100 btn-upload" onclick="document.getElementById('photos').click()">
                                            <i class="bi bi-folder2-open fs-4 d-block mb-2"></i>
                                            <span class="d-block small">Pilih dari Galeri</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="file" name="photos[]" id="photos" 
                                       class="d-none" 
                                       accept="image/*" 
                                       multiple required>
                                
                                <div class="alert alert-info alert-sm mb-0">
                                    <i class="bi bi-info-circle"></i> 
                                    <small>Anda bisa memilih beberapa foto. Maksimal 5MB per foto.</small>
                                </div>
                                
                                @error('photos')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('photos.*')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                
                                <!-- Preview Foto -->
                                <div id="photoPreview" class="row g-3 mt-3"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 2: Lokasi & Jenis Masalah -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-geo-alt-fill"></i> 2. Informasi Lokasi & Masalah
                                </h6>
                                <small class="text-muted">Tentukan lokasi dan jenis masalah yang ditemukan</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt"></i> Lokasi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control form-control-lg @error('location') is-invalid @enderror" 
                                           placeholder="Contoh: Ruang Meeting Lt. 2" 
                                           value="{{ old('location') }}" required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Sebutkan lokasi spesifik temuan masalah</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="issue_type" class="form-label fw-semibold">
                                        <i class="bi bi-exclamation-triangle"></i> Jenis Masalah <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="issue_type" id="issue_type" 
                                           class="form-control form-control-lg @error('issue_type') is-invalid @enderror" 
                                           placeholder="Contoh: Keran Bocor, Lampu Mati" 
                                           value="{{ old('issue_type') }}" required>
                                    @error('issue_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kategorikan masalah yang ditemukan</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 3: Detail Laporan -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h6 class="fw-bold text-primary mb-1">
                                    <i class="bi bi-file-text-fill"></i> 3. Detail Laporan
                                </h6>
                                <small class="text-muted">Berikan deskripsi lengkap dan tentukan departemen</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="bi bi-file-text"></i> Deskripsi Masalah <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description" id="description" rows="5" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              placeholder="Jelaskan masalah secara detail:&#10;- Apa yang terjadi?&#10;- Kapan ditemukan?&#10;- Seberapa parah masalahnya?&#10;- Dampak yang ditimbulkan?" 
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimal 20 karakter untuk deskripsi yang jelas</small>
                                </div>

                                <div class="col-12">
                                    <label for="department_id" class="form-label fw-semibold">
                                        <i class="bi bi-building"></i> Departemen Penanggung Jawab <span class="text-danger">*</span>
                                    </label>
                                    <select name="department_id" id="department_id" 
                                            class="form-select form-select-lg @error('department_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Departemen yang Bertanggung Jawab --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pilih departemen yang sesuai untuk menangani masalah ini</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="{{ route('reports.index') }}" class="btn btn-lg btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary px-5" id="submitBtn">
                                <i class="bi bi-send-fill"></i> Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb text-warning"></i> Tips Membuat Laporan yang Baik</h6>
                    <ul class="mb-0 small">
                        <li>Ambil foto dari berbagai sudut untuk dokumentasi yang lengkap</li>
                        <li>Pastikan foto jelas dan terang, tidak blur atau gelap</li>
                        <li>Sebutkan lokasi dengan spesifik (gedung, lantai, ruangan)</li>
                        <li>Jelaskan masalah secara detail dan dampaknya</li>
                        <li>Pilih departemen yang tepat agar ditangani dengan cepat</li>
                    </ul>
                </div>
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

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 0 0 auto;
    }

    .step-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .step-item.active .step-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #e9ecef;
        margin: 0 10px;
        margin-top: 22px;
    }

    .step-label {
        margin-top: 8px;
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 500;
    }

    .section-header {
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .btn-upload {
        padding: 1.5rem;
        border: 2px dashed #dee2e6;
        transition: all 0.3s;
    }

    .btn-upload:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        transform: translateY(-2px);
    }

    .form-control-lg, .form-select-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border-radius: 8px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
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
        .step-circle {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }

        .step-line {
            margin-top: 17px;
        }

        .btn-upload {
            padding: 1rem;
        }

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

    // Preview Foto
    document.getElementById('photos').addEventListener('change', function(e) {
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

    // Validasi Form
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const photos = document.getElementById('photos').files;
        const submitBtn = document.getElementById('submitBtn');
        
        if (photos.length === 0) {
            e.preventDefault();
            alert('Silakan unggah minimal satu foto');
            return false;
        }
        
        // Cek ukuran file
        for (let i = 0; i < photos.length; i++) {
            if (photos[i].size > 5242880) { // 5MB
                e.preventDefault();
                alert('File ' + photos[i].name + ' melebihi batas 5MB');
                return false;
            }
        }

        // Disable button dan tampilkan loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
    });

    // Character counter untuk description
    const descTextarea = document.getElementById('description');
    descTextarea.addEventListener('input', function() {
        const charCount = this.value.length;
        const minChars = 20;
        
        if (charCount < minChars) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
</script>
@endpush