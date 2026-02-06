<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Login</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            min-height: 100vh;
            overflow-x: hidden;
            /* PERBAIKAN: Hapus overflow: hidden agar bisa scroll */
        }
        
        .login-wrapper {
            display: flex;
            min-height: 100vh; /* PERBAIKAN: Ubah dari height ke min-height */
            position: relative;
        }
        
        /* Left Side - Full Image Slider */
        .left-side {
            flex: 1;
            background: #059669;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }
        
        .slider-container {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Overlay untuk kontras teks */
        .slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
            padding: 4rem 3rem 3rem;
            color: white;
            z-index: 2;
        }
        
        .slide-overlay h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .slide-overlay p {
            font-size: 1.1rem;
            opacity: 0.95;
            line-height: 1.6;
            max-width: 600px;
        }
        
        /* Slider Dots */
        .slider-dots {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.75rem;
            z-index: 10;
        }
        
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .dot.active {
            background: white;
            width: 30px;
            border-radius: 5px;
        }
        
        .dot:hover {
            background: rgba(255,255,255,0.7);
        }
        
        /* Curved Divider */
        .curved-divider {
            position: absolute;
            right: -2px;
            top: 0;
            bottom: 0;
            width: 100px;
            background: white;
            z-index: 5;
            clip-path: ellipse(100% 50% at 100% 50%);
        }
        
        /* Right Side - Login Form */
        .right-side {
            flex: 0 0 45%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: white;
            position: relative;
            z-index: 10;
            min-height: 100vh; /* PERBAIKAN: Tambahkan min-height */
        }
        
        .login-container {
            width: 100%;
            max-width: 480px;
        }
        
        /* Logo */
        .logo-container {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .logo-container img {
            max-width: 100px;
            max-height: 60px;
            height: auto;
            object-fit: contain;
        }
        
        /* Title */
        .login-title {
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .login-title h3 {
            font-weight: 800;
            font-size: 1.5rem;
            color: #047857;
            margin-bottom: 0.5rem;
        }
        
        .login-title p {
            color: #6B7280;
            font-size: 0.8rem;
        }
        
        /* Form */
        .form-label {
            font-weight: 600;
            color: #059669;
            margin-bottom: 0.6rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #D1FAE5;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #F0FDF4;
        }
        
        .form-control:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
            background: white;
            outline: none;
        }
        
        .form-control::placeholder {
            color: #9CA3AF;
        }
        
        .input-group-text {
            background: #F0FDF4;
            border: 2px solid #D1FAE5;
            border-left: none;
            border-radius: 0 12px 12px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #6B7280;
        }
        
        .input-group-text:hover {
            background: #DCFCE7;
            color: #059669;
        }
        
        .input-group .form-control {
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: #059669;
            background: white;
        }
        
        /* Remember & Forgot */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0 2rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
            border: 2px solid #D1FAE5;
        }
        
        .form-check-input:checked {
            background-color: #059669;
            border-color: #059669;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }
        
        .form-check-label {
            font-size: 0.95rem;
            color: #4B5563;
            cursor: pointer;
        }
        
        .forgot-link {
            font-size: 0.95rem;
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: #047857;
        }
        
        /* Login Button */
        .btn-login {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border: none;
            padding: 1.1rem;
            font-weight: 700;
            font-size: 1.05rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login i {
            margin-right: 0.6rem;
        }
        
        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
            animation: shake 0.5s ease;
            margin-bottom: 1.5rem;
            padding: 1rem 1.2rem;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        /* Footer */
        .footer-text {
            text-align: center;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid #E5E7EB;
            color: #9CA3AF;
            font-size: 0.85rem;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .right-side {
                flex: 0 0 50%;
            }
        }
        
        @media (max-width: 991px) {
            .login-wrapper {
                flex-direction: column;
                min-height: auto; /* PERBAIKAN: Biarkan tinggi menyesuaikan konten */
            }
            
            .left-side {
                display: none;
            }
            
            .right-side {
                flex: 1;
                background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
                min-height: 100vh; /* PERBAIKAN: Minimal full screen tapi bisa lebih */
                padding: 2rem 1.5rem; /* PERBAIKAN: Kurangi padding untuk mobile */
            }
            
            .mobile-header {
                width: 100%;
                margin-bottom: 1.5rem; /* PERBAIKAN: Kurangi margin */
            }
            
            .mobile-slider {
                width: 100%;
                height: 220px; /* PERBAIKAN: Kurangi tinggi slider */
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                margin-bottom: 1rem; /* PERBAIKAN: Kurangi margin */
                position: relative;
            }
            
            .mobile-slide {
                width: 100%;
                height: 100%;
                position: relative;
            }
            
            .mobile-slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .mobile-slide-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
                padding: 1.5rem 1.2rem 1rem; /* PERBAIKAN: Kurangi padding */
                color: white;
            }
            
            .mobile-slide-overlay h2 {
                font-size: 1.2rem; /* PERBAIKAN: Kurangi ukuran font */
                font-weight: 800;
                margin-bottom: 0.3rem;
            }
            
            .mobile-slide-overlay p {
                font-size: 0.85rem; /* PERBAIKAN: Kurangi ukuran font */
                opacity: 0.95;
            }
            
            .mobile-dots {
                display: flex;
                gap: 0.5rem;
                justify-content: center;
                margin-bottom: 1rem; /* PERBAIKAN: Tambahkan margin bawah */
            }
            
            .login-container {
                padding: 0 0.5rem; /* PERBAIKAN: Kurangi padding */
            }
            
            .logo-container {
                margin-bottom: 1rem; /* PERBAIKAN: Kurangi margin */
            }
            
            .logo-container img {
                max-width: 120px; /* PERBAIKAN: Kurangi ukuran logo */
                max-height: 45px;
            }
            
            .login-title h3 {
                font-size: 1.4rem; /* PERBAIKAN: Kurangi ukuran font */
            }
            
            .login-title p {
                font-size: 0.8rem;
            }
            
            /* PERBAIKAN: Kurangi spacing form di mobile */
            .mb-4 {
                margin-bottom: 1rem !important;
            }
            
            .form-options {
                margin: 1rem 0 1.5rem;
            }
            
            .footer-text {
                margin-top: 1.5rem;
                padding-top: 1.5rem;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .right-side {
                padding: 1.5rem 1rem; /* PERBAIKAN: Padding lebih kecil */
            }
            
            .login-title h3 {
                font-size: 1.3rem;
            }
            
            .mobile-slider {
                height: 200px; /* PERBAIKAN: Lebih kecil lagi */
            }
            
            .form-control {
                padding: 0.875rem 1rem; /* PERBAIKAN: Padding lebih kecil */
                font-size: 0.95rem;
            }
            
            .btn-login {
                padding: 1rem;
                font-size: 1rem;
            }
            
            .form-label {
                font-size: 0.9rem;
            }
            
            .form-check-label,
            .forgot-link {
                font-size: 0.85rem;
            }
        }
        
        /* PERBAIKAN: Untuk device yang sangat kecil */
        @media (max-width: 380px) {
            .right-side {
                padding: 1rem 0.75rem;
            }
            
            .mobile-slider {
                height: 180px;
            }
            
            .login-title h3 {
                font-size: 1.2rem;
            }
            
            .mobile-slide-overlay h2 {
                font-size: 1.1rem;
            }
            
            .mobile-slide-overlay p {
                font-size: 0.8rem;
            }
        }
        
        /* PERBAIKAN: Pastikan touch target cukup besar di mobile */
        @media (hover: none) and (pointer: coarse) {
            .btn-login {
                min-height: 48px; /* Minimum touch target size */
            }
            
            .form-check-input {
                min-width: 20px;
                min-height: 20px;
            }
            
            .dot {
                min-width: 30px;
                min-height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side - Full Image Slider (Desktop) -->
        <div class="left-side">
            <div class="slider-container">
                <!-- Slide 1 -->
                <div class="slide active">
                    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1200&h=1080&fit=crop&q=80" alt="Keselamatan Kerja">
                    <div class="slide-overlay">
                        <h2>Audit Profesional & Terpercaya</h2>
                        <p>Sistem audit terintegrasi yang memudahkan monitoring dan evaluasi kinerja dengan standar internasional untuk keamanan pangan</p>
                    </div>
                </div>
                
                <!-- Slide 2 -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&h=1080&fit=crop&q=80" alt="Manajemen Audit">
                    <div class="slide-overlay">
                        <h2>Keselamatan & Kesehatan Kerja</h2>
                        <p>Memastikan lingkungan kerja yang aman dan sehat untuk seluruh karyawan dengan standar K3 yang komprehensif</p>
                    </div>
                </div>
                
                <!-- Slide 3 -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=1200&h=1080&fit=crop&q=80" alt="Kebersihan">
                    <div class="slide-overlay">
                        <h2>Kebersihan & Sanitasi</h2>
                        <p>Menjaga kebersihan dan sanitasi area produksi untuk menciptakan lingkungan yang produktif dan higienis</p>
                    </div>
                </div>
                
                <!-- Slide 4 -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&h=1080&fit=crop&q=80" alt="Tim Profesional">
                    <div class="slide-overlay">
                        <h2>Kolaborasi Tim Yang Solid</h2>
                        <p>Bekerja sama dengan tim profesional yang berpengalaman dalam manajemen audit dan quality control</p>
                    </div>
                </div>
                
                <!-- Slide 5 -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1200&h=1080&fit=crop&q=80" alt="Compliance">
                    <div class="slide-overlay">
                        <h2>Compliance & Reporting</h2>
                        <p>Laporan real-time dan dokumentasi lengkap untuk memenuhi standar regulasi dan compliance industri pangan</p>
                    </div>
                </div>
                
                <!-- Dots Navigation -->
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                    <span class="dot" data-slide="3"></span>
                    <span class="dot" data-slide="4"></span>
                </div>
            </div>
            
            <!-- Curved Divider -->
            <div class="curved-divider"></div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="right-side">
            <div class="login-container">
                <!-- Mobile Slider -->
                <div class="mobile-header d-lg-none">
                    <div class="mobile-slider">
                        <div class="mobile-slide">
                            <img id="mobile-img" src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&h=600&fit=crop&q=80" alt="Audit">
                            <div class="mobile-slide-overlay">
                                <h2 id="mobile-title">Audit Profesional & Terpercaya</h2>
                                <p id="mobile-desc">Sistem audit terintegrasi dengan standar internasional</p>
                            </div>
                        </div>
                    </div>
                    <div class="mobile-dots">
                        <span class="dot active" data-mobile-slide="0"></span>
                        <span class="dot" data-mobile-slide="1"></span>
                        <span class="dot" data-mobile-slide="2"></span>
                        <span class="dot" data-mobile-slide="3"></span>
                        <span class="dot" data-mobile-slide="4"></span>
                    </div>
                </div>
                
                <!-- Logo -->
                <div class="logo-container">
                    <img src="{{ asset('logo.png') }}" alt="FKS Food Logo" id="company-logo">
                </div>
                
                <!-- Title -->
                <div class="login-title">
                    <h3>Sistem Audit Management</h3>
                    <p>Silakan masuk untuk melanjutkan ke sistem</p>
                </div>
                
                <!-- Error Alert -->
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-fill"></i> Username
                        </label>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-control @error('username') is-invalid @enderror" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan username Anda"
                               required 
                               autofocus>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Masukkan password Anda"
                                   required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Remember & Forgot -->
                    <div class="form-options">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="#" class="forgot-link">Lupa Password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
                
                <!-- Footer -->
                <div class="footer-text">
                    © 2026 FKS Food - Audit Management System. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }
        
        // Desktop Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.left-side .slide');
        const dots = document.querySelectorAll('.slider-dots .dot');
        
        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (n + slides.length) % slides.length;
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }
        
        function nextSlide() {
            showSlide(currentSlide + 1);
        }
        
        // Auto slide every 5 seconds
        let slideInterval = setInterval(nextSlide, 5000);
        
        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            });
        });
        
        // Mobile Slider Data
        const mobileSlides = [
            {
                title: "Audit Profesional & Terpercaya",
                desc: "Sistem audit terintegrasi dengan standar internasional",
                img: "https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&h=600&fit=crop&q=80"
            },
            {
                title: "Keselamatan & Kesehatan Kerja",
                desc: "Lingkungan kerja yang aman dengan standar K3",
                img: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop&q=80"
            },
            {
                title: "Kebersihan & Sanitasi",
                desc: "Area produksi yang higienis dan produktif",
                img: "https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=800&h=600&fit=crop&q=80"
            },
            {
                title: "Kolaborasi Tim Yang Solid",
                desc: "Tim profesional dalam manajemen audit",
                img: "https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&h=600&fit=crop&q=80"
            },
            {
                title: "Compliance & Reporting",
                desc: "Laporan real-time untuk standar industri pangan",
                img: "https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&h=600&fit=crop&q=80"
            }
        ];
        
        let currentMobileSlide = 0;
        const mobileDots = document.querySelectorAll('.mobile-dots .dot');
        
        function showMobileSlide(n) {
            const mobileTitle = document.getElementById('mobile-title');
            const mobileDesc = document.getElementById('mobile-desc');
            const mobileImg = document.getElementById('mobile-img');
            
            if (!mobileTitle) return;
            
            currentMobileSlide = (n + mobileSlides.length) % mobileSlides.length;
            
            mobileTitle.textContent = mobileSlides[currentMobileSlide].title;
            mobileDesc.textContent = mobileSlides[currentMobileSlide].desc;
            mobileImg.src = mobileSlides[currentMobileSlide].img;
            
            mobileDots.forEach(dot => dot.classList.remove('active'));
            mobileDots[currentMobileSlide].classList.add('active');
        }
        
        // Auto slide for mobile
        let mobileSlideInterval = setInterval(() => {
            showMobileSlide(currentMobileSlide + 1);
        }, 5000);
        
        // Mobile dot navigation
        mobileDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showMobileSlide(index);
                clearInterval(mobileSlideInterval);
                mobileSlideInterval = setInterval(() => {
                    showMobileSlide(currentMobileSlide + 1);
                }, 5000);
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Logo error handler
        const logo = document.getElementById('company-logo');
        if (logo) {
            logo.onerror = function() {
                this.src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="80" viewBox="0 0 200 80"><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="%23059669" text-anchor="middle" dominant-baseline="middle">FKS Food</text></svg>';
            };
        }
    </script>
</body>
</html>