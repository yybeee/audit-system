@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-lock-fill"></i> Change Password
                        </h5>
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Password Requirements -->
                    <div class="password-requirements mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle"></i> Password Requirements
                        </h6>
                        <ul class="requirements-list">
                            <li id="req-length" class="requirement-item">
                                <i class="bi bi-circle"></i>
                                <span>Minimum 8 characters</span>
                            </li>
                            <li id="req-letter" class="requirement-item">
                                <i class="bi bi-circle"></i>
                                <span>Contains letters (a-z or A-Z)</span>
                            </li>
                            <li id="req-number" class="requirement-item">
                                <i class="bi bi-circle"></i>
                                <span>Contains numbers (0-9)</span>
                            </li>
                            <li id="req-match" class="requirement-item">
                                <i class="bi bi-circle"></i>
                                <span>Passwords match</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('profile.update-password') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="form-control form-control-lg @error('current_password') is-invalid @enderror" 
                                       placeholder="Enter your current password"
                                       required>
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password', 'currentIcon')">
                                    <i class="bi bi-eye" id="currentIcon"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       placeholder="Enter new password (min. 8 characters)"
                                       required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password', 'passwordIcon')">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="password-strength mt-2">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthBar"></div>
                                </div>
                                <small class="strength-text" id="strengthText">Enter password to check strength</small>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="form-control form-control-lg" 
                                       placeholder="Re-enter new password"
                                       required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'confirmIcon')">
                                    <i class="bi bi-eye" id="confirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('profile.index') }}" class="btn btn-lg btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle-fill"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-shield-check text-success"></i> Security Tips
                    </h6>
                    <ul class="security-tips">
                        <li>Use a unique password for this account</li>
                        <li>Don't share your password with anyone</li>
                        <li>Change your password regularly</li>
                        <li>Avoid using personal information (birthdate, name)</li>
                        <li>Use a password manager for better security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #065F46 0%, #047857 100%);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    /* Password Input Group */
    .password-input-group {
        position: relative;
    }

    .password-input-group .form-control {
        padding-right: 50px;
        border-radius: 10px;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6B7280;
        cursor: pointer;
        padding: 8px;
        font-size: 1.2rem;
        transition: color 0.2s;
    }

    .password-toggle:hover {
        color: #065F46;
    }

    /* Password Requirements */
    .password-requirements {
        background: #F0FDF4;
        border-left: 4px solid #065F46;
        border-radius: 8px;
        padding: 1rem;
    }

    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .requirement-item {
        padding: 0.5rem 0;
        color: #6B7280;
        transition: color 0.3s;
    }

    .requirement-item i {
        margin-right: 8px;
        font-size: 0.8rem;
    }

    .requirement-item.valid {
        color: #059669;
    }

    .requirement-item.valid i::before {
        content: "\f26b"; /* bi-check-circle-fill */
    }

    /* Password Strength */
    .password-strength {
        margin-top: 10px;
    }

    .strength-bar {
        height: 6px;
        background: #E5E7EB;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 5px;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        transition: all 0.3s;
        border-radius: 3px;
    }

    .strength-fill.weak {
        width: 33%;
        background: #EF4444;
    }

    .strength-fill.medium {
        width: 66%;
        background: #F59E0B;
    }

    .strength-fill.strong {
        width: 100%;
        background: #10B981;
    }

    .strength-text {
        font-size: 0.875rem;
        color: #6B7280;
        font-weight: 500;
    }

    .strength-text.weak {
        color: #EF4444;
    }

    .strength-text.medium {
        color: #F59E0B;
    }

    .strength-text.strong {
        color: #10B981;
    }

    /* Security Tips */
    .security-tips {
        padding-left: 20px;
        margin: 0;
    }

    .security-tips li {
        padding: 0.25rem 0;
        color: #6B7280;
        font-size: 0.9rem;
    }

    /* Form Control */
    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #065F46;
        box-shadow: 0 0 0 0.2rem rgba(6, 95, 70, 0.15);
    }

    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1.5rem !important;
        }

        .password-requirements h6 {
            font-size: 0.95rem;
        }

        .requirement-item {
            font-size: 0.85rem;
            padding: 0.4rem 0;
        }

        .security-tips {
            font-size: 0.85rem;
        }
    }
</style>

<script>
// Toggle Password Visibility
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

// Password Validation & Strength Checker
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    // Requirements elements
    const reqLength = document.getElementById('req-length');
    const reqLetter = document.getElementById('req-letter');
    const reqNumber = document.getElementById('req-number');
    const reqMatch = document.getElementById('req-match');

    // Check password on input
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Check length
        if (password.length >= 8) {
            reqLength.classList.add('valid');
        } else {
            reqLength.classList.remove('valid');
        }
        
        // Check letters
        if (/[a-zA-Z]/.test(password)) {
            reqLetter.classList.add('valid');
        } else {
            reqLetter.classList.remove('valid');
        }
        
        // Check numbers
        if (/[0-9]/.test(password)) {
            reqNumber.classList.add('valid');
        } else {
            reqNumber.classList.remove('valid');
        }
        
        // Check strength
        checkPasswordStrength(password);
        
        // Check match
        checkPasswordMatch();
    });

    // Check confirm password
    confirmInput.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        if (passwordInput.value && confirmInput.value) {
            if (passwordInput.value === confirmInput.value) {
                reqMatch.classList.add('valid');
            } else {
                reqMatch.classList.remove('valid');
            }
        } else {
            reqMatch.classList.remove('valid');
        }
    }

    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        // Update strength bar
        strengthBar.className = 'strength-fill';
        strengthText.className = 'strength-text';
        
        if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.classList.add('weak');
            strengthText.textContent = 'Weak password';
        } else if (strength <= 4) {
            strengthBar.classList.add('medium');
            strengthText.classList.add('medium');
            strengthText.textContent = 'Medium password';
        } else {
            strengthBar.classList.add('strong');
            strengthText.classList.add('strong');
            strengthText.textContent = 'Strong password';
        }
    }

    // Form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Changing...';
    });
});
</script>
@endsection