@extends('layouts.app')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Buat Laporan Audit Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf

                        <!-- Lokasi -->
                        <div class="mb-3">
                            <label for="location" class="form-label">
                                <i class="bi bi-geo-alt"></i> Lokasi *
                            </label>
                            <input type="text" name="location" id="location" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   placeholder="Contoh: Ruang Meeting Lt. 2" 
                                   value="{{ old('location') }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jenis Masalah -->
                        <div class="mb-3">
                            <label for="issue_type" class="form-label">
                                <i class="bi bi-exclamation-triangle"></i> Jenis Masalah *
                            </label>
                            <input type="text" name="issue_type" id="issue_type" 
                                   class="form-control @error('issue_type') is-invalid @enderror" 
                                   placeholder="Contoh: Keran Bocor, Lampu Mati, dll" 
                                   value="{{ old('issue_type') }}" required>
                            @error('issue_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-file-text"></i> Deskripsi *
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Jelaskan masalah secara detail..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Departemen -->
                        <div class="mb-3">
                            <label for="department_id" class="form-label">
                                <i class="bi bi-building"></i> Departemen Penanggung Jawab *
                            </label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Pilih Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto -->
                        <div class="mb-3">
                            <label for="photos" class="form-label">
                                <i class="bi bi-camera"></i> Foto *
                            </label>
                            
                            <!-- Button untuk memilih metode upload -->
                            <div class="btn-group w-100 mb-2" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="openCamera()">
                                    <i class="bi bi-camera-fill"></i> Buka Kamera
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('photos').click()">
                                    <i class="bi bi-folder2-open"></i> Pilih dari Galeri
                                </button>
                            </div>
                            
                            <input type="file" name="photos[]" id="photos" 
                                   class="form-control @error('photos.*') is-invalid @enderror d-none" 
                                   accept="image/*" 
                                   multiple required>
                            <small class="text-muted">Anda bisa memilih beberapa foto. Maksimal 5MB per foto.</small>
                            @error('photos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('photos.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            
                            <!-- Preview Foto -->
                            <div id="photoPreview" class="row g-2 mt-2"></div>
                        </div>

                        <!-- Tombol -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-send"></i> Kirim Laporan
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk membuka kamera
    function openCamera() {
        const input = document.getElementById('photos');
        // Membuat input file baru dengan capture camera
        const cameraInput = document.createElement('input');
        cameraInput.type = 'file';
        cameraInput.accept = 'image/*';
        cameraInput.capture = 'environment'; // Gunakan kamera belakang
        cameraInput.multiple = true;
        
        cameraInput.onchange = function(e) {
            // Transfer files ke input asli
            const dataTransfer = new DataTransfer();
            const existingFiles = Array.from(input.files);
            const newFiles = Array.from(e.target.files);
            
            // Gabungkan file yang sudah ada dengan file baru
            [...existingFiles, ...newFiles].forEach(file => {
                dataTransfer.items.add(file);
            });
            
            input.files = dataTransfer.files;
            
            // Trigger event change untuk preview
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
                    col.className = 'col-6 col-md-4 position-relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '100%';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    img.alt = 'Preview ' + (index + 1);
                    
                    // Tombol hapus foto
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1';
                    deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
                    deleteBtn.onclick = function() {
                        removePhoto(index);
                    };
                    
                    col.appendChild(img);
                    col.appendChild(deleteBtn);
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
        
        // Trigger event change untuk update preview
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }

    // Validasi Form
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const photos = document.getElementById('photos').files;
        
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
    });
</script>
@endpush