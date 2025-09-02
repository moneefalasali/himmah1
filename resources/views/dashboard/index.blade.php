@extends('layouts.app')

@section('title', 'لوحة التحكم - منصة همة التعليمية')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar p-3">
                <div class="text-center mb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        الرئيسية
                    </a>
                    <a class="nav-link" href="{{ route('my-courses') }}">
                        <i class="fas fa-book"></i>
                        دوراتي
                    </a>
                    <a class="nav-link" href="{{ route('my-service-requests') }}">
                        <i class="fas fa-headset"></i>
                        طلبات الخدمات
                    </a>
                    <a class="nav-link" href="{{ route('payment-history') }}">
                        <i class="fas fa-credit-card"></i>
                        سجل المدفوعات
                    </a>
                    <a class="nav-link" href="{{ route('profile') }}">
                        <i class="fas fa-user-edit"></i>
                        الملف الشخصي
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4 class="card-title">مرحباً بك، {{ Auth::user()->name }}!</h4>
                            <p class="card-text mb-0">
                                نتمنى لك تجربة تعليمية ممتعة ومفيدة في منصة همة التعليمية
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
                                <i class="fas fa-book"></i>
                            </div>
                            <h3 class="fw-bold text-primary">{{ $totalCourses }}</h3>
                            <p class="text-muted mb-0">دورة مشتراة</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-success text-white mb-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="fw-bold text-success">{{ $completedLessons }}</h3>
                            <p class="text-muted mb-0">درس مكتمل</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-warning text-white mb-3">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3 class="fw-bold text-warning">{{ $totalServiceRequests }}</h3>
                            <p class="text-muted mb-0">طلب خدمة</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-info text-white mb-3">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h3 class="fw-bold text-info">{{ Auth::user()->created_at->diffInDays() }}</h3>
                            <p class="text-muted mb-0">يوم معنا</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Courses -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-book me-2"></i>
                                آخر الدورات المشتراة
                            </h5>
                            <a href="{{ route('my-courses') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentCourses as $purchase)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    @if($purchase->course->image)
                                        <img src="{{ Storage::url($purchase->course->image) }}" 
                                             alt="{{ $purchase->course->title }}" 
                                             class="rounded me-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-book text-white"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.show', $purchase->course) }}" 
                                               class="text-decoration-none">
                                                {{ $purchase->course->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            تم الشراء في {{ $purchase->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    
                                    <a href="{{ route('courses.curriculum', $purchase->course) }}" 
                                       class="btn btn-sm btn-primary">
                                        ابدأ التعلم
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-book text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">لم تشترِ أي دورة بعد</p>
                                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-2">
                                        تصفح الدورات
                                    </a>
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
                            <a href="{{ route('my-service-requests') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($recentServiceRequests as $request)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="bg-{{ $request->status_color }} rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-headset text-white"></i>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('services.show', $request) }}" 
                                               class="text-decoration-none">
                                                {{ $request->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $request->serviceType->name }} - 
                                            {{ $request->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    
                                    <span class="badge bg-{{ $request->status_color }}">
                                        {{ $request->status_in_arabic }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-headset text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">لم تطلب أي خدمة بعد</p>
                                    <a href="{{ route('services.index') }}" class="btn btn-primary mt-2">
                                        طلب خدمة
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                إجراءات سريعة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search me-2"></i>
                                        تصفح الدورات
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('services.index') }}" class="btn btn-outline-success w-100">
                                        <i class="fas fa-plus me-2"></i>
                                        طلب خدمة
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('profile') }}" class="btn btn-outline-info w-100">
                                        <i class="fas fa-user-edit me-2"></i>
                                        تحديث الملف
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('payment-history') }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-history me-2"></i>
                                        سجل المدفوعات
                                    </a>
                                </div>
                            </div>
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
<button class="btn btn-primary d-lg-none position-fixed" 
        style="top: 100px; right: 20px; z-index: 1001;" 
        onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>
@endsection

