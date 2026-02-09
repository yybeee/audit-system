@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('profile.index') }}" class="btn btn-outline-primary me-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <h4 class="mb-1 fw-bold">Ubah Password</h4>
                    <p class="text-muted mb-0">Perbarui password akun Anda</p>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update-password') }}">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill text-primary me-2"></i>
                                Password Saat Ini
                            </label>
                            <input 
                                type="password" 
                                class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" 
                                name="current_password" 
                                required
                                placeholder="Masukkan password saat ini"
                            >
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-shield-lock-fill text-success me-2"></i>
                                Password Baru
                            </label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="Masukkan password baru (min. 8 karakter)"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Password minimal 8 karakter
                            </small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="bi bi-shield-check-fill text-success me-2"></i>
                                Konfirmasi Password Baru
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                placeholder="Ketik ulang password baru"
                            >
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Tips -->
            <div class="card mt-3" style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border: none;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb-fill text-warning me-2"></i>
                        Tips Password yang Aman
                    </h6>
                    <ul class="mb-0 small">
                        <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                        <li>Hindari menggunakan informasi pribadi yang mudah ditebak</li>
                        <li>Jangan gunakan password yang sama untuk akun lain</li>
                        <li>Ganti password secara berkala untuk keamanan maksimal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection