<!DOCTYPE html>
<html lang="id">
<head>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Audit Management System">
    <meta name="theme-color" content="#2563EB">
    <title>@yield('title', 'Audit System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">
    
    <style>
        :root {
            --primary: #10B981;
            --primary-dark: #059669;
            --secondary: #34D399;
            --danger: #EF4444;
            --warning: #F59E0B;
            --info: #3B82F6;
            --dark: #1F2937;
            --light: #F9FAFB;
            --border: #E5E7EB;
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #F9FAFB 0%, #EFF6FF 100%);
            color: var(--dark);
        }
        
        /* Navbar Redesign */
        .navbar-custom {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
            padding: 0.75rem 0;
            border-bottom: 3px solid rgba(255, 255, 255, 0.1);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.25rem;
            color: white !important;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: translateY(-2px);
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .logo-icon i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background: var(--light);
            color: var(--primary);
            transform: translateX(5px);
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            border-bottom: 2px solid var(--border);
            padding: 1.25rem 1.5rem;
            background: white !important;
            border-radius: 16px 16px 0 0 !important;
        }
        
        .card-header h5 {
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }
        
        /* Stat Cards */
        .stat-card {
            background: white;
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            border-left-width: 6px;
        }
        
        /* Buttons */
        .btn {
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Tables */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--light);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--dark);
            border: none;
            padding: 1rem;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--border);
        }
        
        .table tbody tr:hover {
            background: var(--light);
            transform: scale(1.01);
        }
        
        /* Mobile Menu */
        @media (max-width: 768px) {
            .mobile-menu {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
                z-index: 1040;
                padding: 0.5rem 0;
                border-top: 3px solid var(--primary);
            }
            
            .mobile-menu a {
                flex: 1;
                text-align: center;
                padding: 0.5rem;
                color: #6B7280;
                text-decoration: none;
                transition: all 0.2s ease;
                border-radius: 8px;
                margin: 0 0.25rem;
            }
            
            .mobile-menu a.active {
                color: var(--primary);
                background: rgba(16, 185, 129, 0.1);
            }
            
            .mobile-menu i {
                font-size: 1.25rem;
                display: block;
                margin-bottom: 0.25rem;
            }
            
            .mobile-menu small {
                font-size: 0.7rem;
                font-weight: 600;
            }
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--light);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <div class="logo-icon">
                    @if(file_exists(public_path('logo.png')))
                        <img src="{{ asset('logo.png') }}" alt="Logo">
                    @else
                        <i class="bi bi-clipboard-check-fill"></i>
                    @endif
                </div>
                <span>Audit System</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->user()->role === 'auditor')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.create') ? 'active' : '' }}" href="{{ route('reports.create') }}">
                                    <i class="bi bi-plus-circle-fill"></i> New Report
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') && !request()->routeIs('reports.create') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="bi bi-file-earmark-text-fill"></i> Reports
                            </a>
                        </li>
                        
                        @if(auth()->user()->role === 'super_admin')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear-fill"></i> Management
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.departments') }}">
                                        <i class="bi bi-building"></i> Departments
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.audit-types') }}">
                                        <i class="bi bi-clipboard-check"></i> Audit Types
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">
                                        <i class="bi bi-people-fill"></i> Users
                                    </a></li>
                                </ul>
                            </li>
                        @endif
                        
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 32px; height: 32px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary);">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="px-3 py-2 border-bottom">
                                    <small class="text-muted d-block">Signed in as</small>
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <br>
                                    <span class="badge bg-primary mt-1">{{ auth()->user()->role_label }}</span>
                                </li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-person"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-gear"></i> Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4 pb-md-4" style="padding-bottom: 90px !important;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
        
        <!-- Mobile Spacer -->
        <div class="d-md-none" style="height: 20px;"></div>
    </main>

    <!-- Mobile Bottom Menu -->
    @auth
    <div class="mobile-menu d-md-none">
        <div class="d-flex justify-content-around">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <small class="d-block">Home</small>
            </a>
            
            @if(auth()->user()->role === 'auditor')
                <a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i>
                    <small class="d-block">New</small>
                </a>
            @endif
            
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') && !request()->routeIs('reports.create') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <small class="d-block">Reports</small>
            </a>
            
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <small class="d-block">Admin</small>
                </a>
            @endif
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registered'))
                .catch(err => console.log('Service Worker registration failed'));
        }
    </script>
    
    @stack('scripts')
</body>
</html>