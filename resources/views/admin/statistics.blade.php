@extends('layouts.admin')

@section('title', 'الإحصائيات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>الإحصائيات والتحليلات</h2>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dateRangeDropdown" 
                    data-bs-toggle="dropdown">
                هذا الشهر
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">هذا الشهر</a></li>
                <li><a class="dropdown-item" href="#">هذا الربع</a></li>
                <li><a class="dropdown-item" href="#">هذا العام</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">تخصيص</a></li>
            </ul>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الدخل</h6>
<h3 class="mb-0">{{ number_format($stats['sales']['total_revenue'], 2) }} ر.س</h3>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 text-success"></i>
                    </div>
                    <div class="mt-2">
                        <span class="text-success fw-bold">+12.5%</span> 
                        <span class="text-muted">مقارنة بالشهر الماضي</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي المستخدمين</h6>
<h3 class="mb-0">{{ number_format($stats['users']['total']) }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 text-primary"></i>
                    </div>
                    <div class="mt-2">
                        <span class="text-success fw-bold">+8.2%</span> 
                        <span class="text-muted">مقارنة بالشهر الماضي</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الدورات</h6>
                            <h3 class="mb-0">{{ number_format($stats['courses']['total']) }}</h3>
                        </div>
                        <i class="bi bi-journal fs-1 text-info"></i>
                    </div>
                    <div class="mt-2">
                        <span class="text-success fw-bold">+5.7%</span> 
                        <span class="text-muted">مقارنة بالشهر الماضي</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">معدل التحويل</h6>
                            <h3 class="mb-0">{{ number_format($conversionRate, 2) }}%</h3>
                        </div>
                        <i class="bi bi-graph-up fs-1 text-warning"></i>
                    </div>
                    <div class="mt-2">
                        <span class="text-success fw-bold">+2.3%</span> 
                        <span class="text-muted">مقارنة بالشهر الماضي</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الإيرادات الشهرية</h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active">سنة</button>
                        <button class="btn btn-sm btn-outline-secondary">ربع</button>
                        <button class="btn btn-sm btn-outline-secondary">شهر</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أداء الدورات</h5>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الدورة</th>
                                    <th>المبيعات</th>
                                    <th>الإيرادات</th>
                                    <th>معدل الإكمال</th>
                                    <th>التقييم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coursesPerformance as $course)
                                    <tr>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->sales_count }}</td>
                                        <td>{{ number_format($course->revenue, 2) }} ر.س</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $course->completion_rate }}%">
                                                </div>
                                            </div>
                                            <small>{{ $course->completion_rate }}%</small>
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $course->average_rating)
                                                        <i class="bi bi-star-fill"></i>
                                                    @else
                                                        <i class="bi bi-star"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-1">{{ number_format($course->average_rating, 1) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">مصدر المستخدمين</h5>
                </div>
                <div class="card-body">
                    <canvas id="userSourceChart" height="200"></canvas>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">حالة الطلبات</h5>
                </div>
                <div class="card-body">
                    <canvas id="serviceStatusChart" height="200"></canvas>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">التنزيلات</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.export') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i> تصدير بيانات المستخدمين
                        </a>
                        <a href="{{ route('admin.services.export') }}" class="btn btn-outline-info">
                            <i class="bi bi-download me-2"></i> تصدير طلبات الخدمات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // رسم بياني للإيرادات
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {{ json_encode($revenueLabels) }},
                datasets: [{
                    label: 'الإيرادات (ر.س)',
                    data: {{ json_encode($revenueData) }},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.3,
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // رسم بياني لمصدر المستخدمين
        const userSourceCtx = document.getElementById('userSourceChart').getContext('2d');
        new Chart(userSourceCtx, {
            type: 'doughnut',
            data: {
                labels: ['الفيسبوك', 'جوجل', 'إنستجرام', 'البريد الإلكتروني', 'مباشر'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        'rgb(59, 89, 152)',
                        'rgb(221, 75, 57)',
                        'rgb(225, 48, 108)',
                        'rgb(204, 0, 51)',
                        'rgb(100, 100, 100)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // رسم بياني لحالة الطلبات
        const serviceStatusCtx = document.getElementById('serviceStatusChart').getContext('2d');
        new Chart(serviceStatusCtx, {
            type: 'pie',
            data: {
                labels: ['مكتملة', 'قيد التنفيذ', 'معلقة', 'ملغاة'],
                datasets: [{
                    data: [45, 30, 20, 5],
                    backgroundColor: [
                        'rgb(40, 167, 69)',
                        'rgb(23, 162, 184)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection