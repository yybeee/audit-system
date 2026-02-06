@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Ubah Password</h5>
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

                    <!-- Info -->
                    <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
                        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                        <div>
                            <strong>Ketentuan Password:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Minimal 8 karakter</li>
                                <li>Kombinasi huruf dan angka lebih aman</li>
                                <li>Jangan gunakan password yang mudah ditebak</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Ubah Password -->
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Password Lama -->
                        <div class="mb-3">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       placeholder="Masukkan password lama"
                                       required>
                                <span class="input-group-text" onclick="togglePassword('current_password', 'currentIcon')" style="cursor: pointer;">
                                    <i class="bi bi-eye" id="currentIcon"></i>
                                </span>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Masukkan password baru (min. 8 karakter)"
                                       required>
                                <span class="input-group-text" onclick="togglePassword('password', 'passwordIcon')" style="cursor: pointer;">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="form-control" 
                                       placeholder="Ulangi password baru"
                                       required>
                                <span class="input-group-text" onclick="togglePassword('password_confirmation', 'confirmIcon')" style="cursor: pointer;">
                                    <i class="bi bi-eye" id="confirmIcon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Ubah Password
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
    .input-group-text {
        background: #F0FDF4;
        border-left: none;
    }
    
    .input-group .form-control {
        border-right: none;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #059669;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .d-flex.gap-2 {
            flex-direction: column;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
        }
    }
</style>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection