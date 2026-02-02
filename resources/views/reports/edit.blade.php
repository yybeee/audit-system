@extends('layouts.app')

@section('title', 'Edit Report')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Report
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You can only edit reports with status "Submitted" or "Rejected"
                    </div>

                    <form action="{{ route('reports.update', $report) }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Audit Type -->
                        <div class="mb-3">
                            <label for="audit_type_id" class="form-label">
                                <i class="bi bi-clipboard-check"></i> Audit Type *
                            </label>
                            <select name="audit_type_id" id="audit_type_id" class="form-select @error('audit_type_id') is-invalid @enderror" required>
                                <option value="">Select Audit Type</option>
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

                        <!-- Department -->
                        <div class="mb-3">
                            <label for="department_id" class="form-label">
                                <i class="bi bi-building"></i> Department Responsible *
                            </label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
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

                        <!-- Location -->
                        <div class="mb-3">
                            <label for="location" class="form-label">
                                <i class="bi bi-geo-alt"></i> Location *
                            </label>
                            <input type="text" name="location" id="location" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   placeholder="e.g., Ruang Meeting Lt. 2" 
                                   value="{{ old('location', $report->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Issue Type -->
                        <div class="mb-3">
                            <label for="issue_type" class="form-label">
                                <i class="bi bi-exclamation-triangle"></i> Issue Type *
                            </label>
                            <input type="text" name="issue_type" id="issue_type" 
                                   class="form-control @error('issue_type') is-invalid @enderror" 
                                   placeholder="e.g., Keran Bocor, Lampu Mati, dll" 
                                   value="{{ old('issue_type', $report->issue_type) }}" required>
                            @error('issue_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-file-text"></i> Description *
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Describe the issue in detail..." required>{{ old('description', $report->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Existing Photos -->
                        @if($report->photos && count($report->photos) > 0)
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-images"></i> Current Photos</label>
                                <div class="row g-2">
                                    @foreach($report->photos as $photo)
                                        <div class="col-6 col-md-4">
                                            <img src="{{ Storage::url($photo) }}" class="img-fluid rounded" alt="Photo">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add More Photos -->
                        <div class="mb-3">
                            <label for="photos" class="form-label">
                                <i class="bi bi-camera"></i> Add More Photos (Optional)
                            </label>
                            <input type="file" name="photos[]" id="photos" 
                                   class="form-control @error('photos.*') is-invalid @enderror" 
                                   accept="image/*" 
                                   multiple>
                            <small class="text-muted">Max 5MB per photo. Leave empty to keep existing photos only.</small>
                            @error('photos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div id="photoList" class="mt-3"></div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning flex-fill">
                                <i class="bi bi-save"></i> Update Report
                            </button>
                            <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
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
    // Photo List
    document.getElementById('photos')?.addEventListener('change', function(e) {
        const photoList = document.getElementById('photoList');
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
                    
                    fileItem.appendChild(img);
                    fileItem.appendChild(fileInfo);
                    photoList.appendChild(fileItem);
                };
                
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush