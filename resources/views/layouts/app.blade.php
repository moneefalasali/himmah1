<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'منصة همة التعليمية')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--light-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .navbar {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            margin-top: auto;
        }

        .course-card {
            transition: transform 0.2s ease-in-out;
        }

        .course-card:hover {
            transform: translateY(-5px);
        }

        .rating-stars {
            color: #fbbf24;
        }

        .sidebar {
            min-height: calc(100vh - 76px);
            background-color: white;
            border-left: 1px solid #e2e8f0;
        }

        .sidebar .nav-link {
            color: var(--secondary-color);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-left: 0.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            padding: 4rem 0;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
        }

        .message-bubble {
            max-width: 70%;
            margin-bottom: 1rem;
        }

        .message-bubble.own {
            margin-right: auto;
            background-color: var(--primary-color);
            color: white;
        }

        .message-bubble.other {
            margin-left: auto;
            background-color: #e2e8f0;
            color: var(--dark-color);
        }

        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 0.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 76px;
                right: -100%;
                width: 280px;
                z-index: 1000;
                transition: right 0.3s ease-in-out;
            }

            .sidebar.show {
                right: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset("css/custom-ux.css") }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                منصة همة
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courses.index') }}">الدورات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services.index') }}">الخدمات التعليمية</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="{{ route('register') }}">إنشاء حساب</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>لوحة الإدارة
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                @if(Auth::user()->isTeacher())
                                    <li><a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>لوحة تحكم المعلم
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-home me-2"></i>لوحة التحكم
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('my-courses') }}">
                                    <i class="fas fa-book me-2"></i>دوراتي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user-edit me-2"></i>الملف الشخصي
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @if(Auth::check() && Auth::user()->isTeacher())
        @include('partials.teacher_nav')
    @endif

    <!-- Main Content -->
    <main class="flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>منصة همة التعليمية</h5>
                    <p class="mb-0">منصة تعليمية متكاملة تهدف إلى تقديم أفضل الدورات والخدمات التعليمية.</p>
                </div>
                <div class="col-md-3">
                    <h6>روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('courses.index') }}" class="text-light text-decoration-none">الدورات</a></li>
                        <li><a href="{{ route('services.index') }}" class="text-light text-decoration-none">الخدمات</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>تواصل معنا</h6>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>hemmah.platform.app@gmail.com</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i> </p>
                    <p class="mb-0"><a href="{{ env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com' }}" target="_blank" rel="noopener" class="text-light text-decoration-none fw-bold">فتح دردشة مباشرة مع الإدارة</a></p>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} منصة همة التعليمية. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }

        // Close sidebar when clicking overlay
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('sidebar-overlay')) {
                toggleSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

