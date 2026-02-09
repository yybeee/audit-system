<!-- Loading Screen Component - Fixed Version -->
<div id="page-loading-screen">
    <!-- Logo -->
    <div class="loading-logo-container">
        <div class="loading-logo-circle">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="PT Putra Taro Paloma">
            @else
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='70' font-size='70' fill='%23065F46'%3EPT%3C/text%3E%3C/svg%3E" alt="PT Putra Taro Paloma">
            @endif
        </div>
    </div>

    <!-- Company Name -->
    <div class="loading-company-name">
        <h1>PT Putra Taro Paloma</h1>
        <p>Audit Management System</p>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner-container">
        <div class="loading-spinner-circle"></div>
    </div>

    <!-- Loading Text -->
    <div class="loading-text-container">
        Loading<span class="loading-dots-animation"></span>
    </div>

    <!-- Progress Bar -->
    <div class="loading-progress-container">
        <div class="loading-progress-bar"></div>
    </div>
</div>

<style>
    /* Loading Screen - Scoped dengan ID untuk menghindari konflik */
    #page-loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: linear-gradient(135deg, #065F46 0%, #047857 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 99999; /* Super tinggi agar menutupi semua */
        transition: opacity 0.5s ease-out;
    }

    #page-loading-screen.fade-out {
        opacity: 0;
        pointer-events: none;
    }

    /* Logo Container */
    .loading-logo-container {
        margin-bottom: 40px;
        animation: loadingFadeInDown 0.8s ease-out;
    }

    .loading-logo-circle {
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: loadingPulse 2s ease-in-out infinite;
    }

    .loading-logo-circle img {
        width: 70%;
        height: 70%;
        object-fit: contain;
    }

    /* Company Name */
    .loading-company-name {
        color: white;
        text-align: center;
        margin-bottom: 10px;
        animation: loadingFadeInUp 0.8s ease-out 0.2s both;
    }

    .loading-company-name h1 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .loading-company-name p {
        font-size: 14px;
        font-weight: 400;
        margin: 8px 0 0 0;
        opacity: 0.9;
    }

    /* Loading Spinner */
    .loading-spinner-container {
        margin-top: 30px;
        animation: loadingFadeIn 0.8s ease-out 0.4s both;
    }

    .loading-spinner-circle {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: loadingSpin 1s linear infinite;
    }

    /* Loading Text */
    .loading-text-container {
        color: white;
        margin-top: 20px;
        font-size: 14px;
        font-weight: 500;
        opacity: 0.9;
        animation: loadingFadeIn 0.8s ease-out 0.6s both;
    }

    /* Loading Dots Animation */
    .loading-dots-animation {
        display: inline-block;
    }

    .loading-dots-animation::after {
        content: '';
        animation: loadingDots 1.5s steps(4, end) infinite;
    }

    /* Progress Bar */
    .loading-progress-container {
        width: 200px;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        margin-top: 20px;
        overflow: hidden;
        animation: loadingFadeIn 0.8s ease-out 0.8s both;
    }

    .loading-progress-bar {
        height: 100%;
        background: white;
        border-radius: 2px;
        animation: loadingProgress 2s ease-in-out infinite;
    }

    /* Animations - dengan prefix unik */
    @keyframes loadingFadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes loadingFadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes loadingFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes loadingPulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }
    }

    @keyframes loadingSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes loadingDots {
        0%, 20% {
            content: '';
        }
        40% {
            content: '.';
        }
        60% {
            content: '..';
        }
        80%, 100% {
            content: '...';
        }
    }

    @keyframes loadingProgress {
        0% {
            width: 0%;
        }
        50% {
            width: 70%;
        }
        100% {
            width: 100%;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .loading-logo-circle {
            width: 100px;
            height: 100px;
        }

        .loading-company-name h1 {
            font-size: 20px;
        }

        .loading-company-name p {
            font-size: 13px;
        }

        .loading-spinner-circle {
            width: 40px;
            height: 40px;
            border-width: 3px;
        }

        .loading-text-container {
            font-size: 13px;
        }

        .loading-progress-container {
            width: 160px;
        }
    }
</style>

<script>
    // Hide loading screen after page loads
    window.addEventListener('load', function() {
        setTimeout(function() {
            const loadingScreen = document.getElementById('page-loading-screen');
            if (loadingScreen) {
                loadingScreen.classList.add('fade-out');
                
                // Remove from DOM after animation
                setTimeout(function() {
                    loadingScreen.remove();
                }, 500);
            }
        }, 500);
    });

    // Fallback: Hide after 5 seconds
    setTimeout(function() {
        const loadingScreen = document.getElementById('page-loading-screen');
        if (loadingScreen) {
            loadingScreen.classList.add('fade-out');
            setTimeout(function() {
                loadingScreen.remove();
            }, 500);
        }
    }, 5000);
</script>