@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Profil</h5>
                    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Avatar -->
                    <div class="text-center mb-4">
                        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, #10B981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 2rem; margin: 0 auto;">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        <h5 class="mt-3 mb-1">{{ $user->name ?? 'User' }}</h5>
                        <span class="badge bg-primary">
                            @if($user->role === 'super_admin') 
                                Super Admin
                            @elseif($user->role === 'auditor') 
                                Auditor
                            @elseif($user->role === 'staff_departemen') 
                                Staff Departemen
                            @else
                                {{ ucfirst($user->role ?? 'User') }}
                            @endif
                        </span>
                    </div>

                    <hr>

                    <!-- Form Edit Profil -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email ?? '') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $user->username ?? 'N/A' }}" 
                                   disabled>
                            <small class="text-muted">Username tidak dapat diubah.</small>
                        </div>

                        @if($user->role === 'staff_departemen')
                            <div class="mb-3">
                                <label class="form-label">Departemen</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $user->department->name ?? 'Belum ada departemen' }}" 
                                       disabled>
                                <small class="text-muted">Departemen diatur oleh admin.</small>
                            </div>
                        @endif

                        @if($user->role === 'auditor')
                            <div class="mb-3">
                                <label class="form-label">Tipe Audit</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $user->auditType->name ?? 'Belum ada tipe audit' }}" 
                                       disabled>
                                <small class="text-muted">Tipe audit diatur oleh admin.</small>
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive untuk tombol */
    @media (max-width: 576px) {
        .d-flex.gap-2 {
            flex-direction: column;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
        }
    }
</style>
@endsection