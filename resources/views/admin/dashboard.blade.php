@extends('layouts.app')

@section('title', 'لوحة الإدارة - منصة همة التعليمية')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Admin Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar p-3">
                <div class="text-center mb-4">
                    <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-shield text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
                    <small class="text-muted">مدير النظام</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        لوحة التحكم
                    </a>
                    <a class="nav-link" href="{{ route('admin.courses.index') }}">
                        <i class="fas fa-book"></i>
                        إدارة الدورات
                    </a>
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        إدارة المستخدمين
                    </a>
                    <a class="nav-link" href="{{ route('admin.services.index') }}">
                        <i class="fas fa-headset"></i>
                        طلبات الخدمات
                    </a>
                    <a class="nav-link" href="{{ route('admin.service-types.index') }}">
                        <i class="fas fa-cogs"></i>
                        أنواع الخدمات
                    </a>
                                        <a class="nav-link" href="{{ route('admin.universities.index') }}">
                        <i class="fas fa-users"></i>
                        إدارة الجامعات
                    </a>
                                                            <a class="nav-link" href="{{ route('admin.uni_courses.index') }}">
                        <i class="fas fa-users"></i>
ادارة مقررات مخصصه  
                    </a>


                    <a class="nav-link" href="{{ route('admin.statistics') }}">
                        <i class="fas fa-chart-bar"></i>
                        الإحصائيات
                    </a>

                    <hr>
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        العودة للموقع
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h4 class="card-title">مرحباً بك في لوحة الإدارة، {{ Auth::user()->name }}!</h4>
                            <p class="card-text mb-0">
                                إدارة شاملة لمنصة همة التعليمية
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-primary text-white mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="fw-bold text-primary">{{ $totalUsers }}</h3>
                            <p class="text-muted mb-0">مستخدم مسجل</p>
                            <small class="text-success">+{{ $newUsersThisMonth }} هذا الشهر</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-success text-white mb-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <h3 class="fw-bold text-success">{{ $totalCourses }}</h3>
                            <p class="text-muted mb-0">دورة تدريبية</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-warning text-white mb-3">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3 class="fw-bold text-warning">{{ number_format($totalRevenue, 2) }}</h3>
                            <p class="text-muted mb-0">إجمالي الإيرادات</p>
                            <small class="text-success">+{{ number_format($revenueThisMonth, 2) }} هذا الشهر</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-info text-white mb-3">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3 class="fw-bold text-info">{{ $pendingServiceRequests }}</h3>
                            <p class="text-muted mb-0">طلب خدمة معلق</p>
                            <small class="text-info">+{{ $newServiceRequestsThisMonth }} هذا الشهر</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Purchases -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                آخر المشتريات
                            </h5>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentPurchases as $purchase)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="bg-success rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-shopping-cart text-white"></i>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $purchase->course->title }}</h6>
                                        <small class="text-muted">
                                            {{ $purchase->user->name }} - 
                                            {{ $purchase->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    <span class="badge bg-success">
                                        {{ number_format($purchase->amount, 2) }} ريال
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">لا توجد مشتريات بعد</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Recent Service Requests -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-headset me-2"></i>
                                آخر طلبات الخدمات
                            </h5>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentServiceRequests as $request)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'completed' ? 'success' : 'info') }} rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-headset text-white"></i>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $request->title }}</h6>
                                        <small class="text-muted">
                                            {{ $request->user->name }} - 
                                            {{ $request->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'completed' ? 'success' : 'info') }}">
                                        {{ $request->status == 'pending' ? 'معلق' : ($request->status == 'completed' ? 'مكتمل' : 'قيد التنفيذ') }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-headset text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">لا توجد طلبات خدمات بعد</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Courses -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                الدورات الأكثر مبيعاً
                            </h5>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-primary">
                                إدارة الدورات
                            </a>
                        </div>
                        <div class="card-body">
                            @if($topCourses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>الدورة</th>
                                                <th>المدرب</th>
                                                <th>عدد المبيعات</th>
                                                <th>السعر</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topCourses as $course)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($course->image)
                                                                <img src="{{ Storage::url($course->image) }}" 
                                                                     alt="{{ $course->title }}" 
                                                                     class="rounded me-3" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                                                                     style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-book text-white"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-0">{{ $course->title }}</h6>
                                                                <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $course->instructor_name }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $course->purchases_count }}</span>
                                                    </td>
                                                    <td>{{ number_format($course->price, 2) }} ريال</td>
                                                    <td>
                                                        <a href="{{ route('admin.courses.show', $course) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            عرض
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">لا توجد دورات بعد</p>
                                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary mt-2">
                                        إضافة دورة جديدة
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Mobile Sidebar Toggle Button -->
<button class="btn btn-danger d-lg-none position-fixed" 
        style="top: 100px; right: 20px; z-index: 1001;" 
        onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>
@endsection

