<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة الإدارة')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="admin-topbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    منصة همة
                </a>

                <div class="d-flex align-items-center gap-3">
                    <form class="d-none d-md-block admin-search" role="search" action="{{ url()->current() }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="ابحث..." value="{{ request('q') }}">
                            <button class="btn btn-sm" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <span class="text-muted-xs d-none d-md-inline">{{ auth()->check() ? auth()->user()->name : '' }}</span>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="adminUserMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="admin-avatar me-2">
                                <img src="{{ auth()->check() ? auth()->user()->avatar_url : asset('assets/images/default-avatar.png') }}" alt="avatar">
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserMenu">
                            @if(Route::has('admin.profile'))
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">الملف الشخصي</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">تسجيل الخروج</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="row gx-4">
            <div class="col-12 col-lg-3 mb-4">
                @include('layouts.partials.admin_sidebar')
            </div>
            <div class="col-12 col-lg-9">
                <div class="admin-content">
                    <div class="admin-breadcrumbs">
                        @yield('breadcrumbs')
                    </div>

                    {{-- Flash messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-admin">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-admin">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <footer class="admin-footer mt-4">
        <div class="container footer-inner">
            <div class="footer-col">
                <strong>نظام همة</strong>
                <small>حقوق النشر © {{ date('Y') }} جميع الحقوق محفوظة</small>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="footer-col">
                    <a href="/docs">الوثائق</a>
                    <a href="/contact">تواصل معنا</a>
                </div>

                <div class="socials d-flex align-items-center">
                    <a href="#" aria-label="twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" aria-label="facebook"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" aria-label="linkedin"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>

                <small class="text-muted-xs">v1.0.0</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
