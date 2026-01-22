<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المعلم - @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; }
        .sidebar { min-height: calc(100vh - 76px); background-color: #fff; border-left: 1px solid #e2e8f0; }
        .sidebar .nav-link {
            color: #475569;
            padding: .6rem 1rem;
            border-radius: .5rem;
            margin-bottom: .35rem;
        }
        .sidebar .nav-link:hover { background-color: var(--bs-primary); color: #fff; }
        .sidebar .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .sidebar h4 { color: var(--bs-primary); font-weight: 600; }
        .content-card { box-shadow: 0 4px 12px rgba(16,24,40,0.06); border-radius: .75rem; border: 1px solid #e6edf3; }
        .content-card .card-body { background: #fff; }
        @media (max-width: 767px) {
            .sidebar { display:none; }
        }
    </style>

    @yield('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-ux.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Use main layout container styles -->
    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 sidebar py-4">
                <div class="px-3 mb-3">
                    <h4 class="mb-0">منصة همة</h4>
                </div>
                <nav class="nav flex-column px-3">
                    <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">الرئيسية</a>
                    <a href="{{ route('teacher.courses.index') }}" class="nav-link {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">دوراتي</a>
                    <a href="{{ route('teacher.sales') }}" class="nav-link {{ request()->routeIs('teacher.sales') ? 'active' : '' }}">المبيعات</a>
                    <a href="{{ route('teacher.earnings.index') }}" class="nav-link {{ request()->routeIs('teacher.earnings.*') ? 'active' : '' }}">إحصائيات الأرباح</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4 px-3">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100">تسجيل الخروج</button>
                    </form>
                </nav>
            </aside>

            <main class="col-md-9 py-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h4 mb-0">@yield('title')</h1>
                    <div class="text-end">
                        <div class="text-muted">{{ auth()->user()->name }} <small class="text-muted">(معلم)</small></div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="content-card card">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
