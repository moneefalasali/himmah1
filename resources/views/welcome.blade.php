@extends('layouts.app')

@section('title', 'منصة همة التعليمية - الرئيسية')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    ابدأ رحلتك التعليمية مع منصة همة
                </h1>
                <p class="lead mb-4">
                    اكتشف مجموعة واسعة من الدورات التدريبية عالية الجودة والخدمات التعليمية المتخصصة 
                    التي تساعدك على تطوير مهاراتك وتحقيق أهدافك المهنية.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('courses.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-book me-2"></i>
                        تصفح الدورات
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-headset me-2"></i>
                        الخدمات التعليمية
                    </a>
                    @guest
                        <a href="{{ route('register.teacher') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            كن معلماً
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image mt-4 mt-lg-0">
                    <i class="fas fa-graduation-cap" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">لماذا تختار منصة همة؟</h2>
                <p class="text-muted">نقدم لك تجربة تعليمية متميزة ومتكاملة</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h5 class="fw-bold">دورات فيديو عالية الجودة</h5>
                    <p class="text-muted">
                        محتوى تعليمي مصور بجودة عالية مع إمكانية المعاينة المجانية لثلاثة دروس من كل دورة
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5 class="fw-bold">شهادات معتمدة</h5>
                    <p class="text-muted">
                        احصل على شهادات إتمام معتمدة عند انتهائك من الدورات التدريبية
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="fw-bold">خدمات تعليمية متخصصة</h5>
                    <p class="text-muted">
                        خدمات البحث الأكاديمي والتصميم التعليمي والمشاريع البرمجية
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h5 class="fw-bold">تعلم في أي مكان</h5>
                    <p class="text-muted">
                        منصة متجاوبة تعمل على جميع الأجهزة للتعلم في أي وقت ومكان
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5 class="fw-bold">دفع آمن</h5>
                    <p class="text-muted">
                        نظام دفع إلكتروني آمن ومتكامل مع PayTabs لضمان أمان معاملاتك
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h5 class="fw-bold">دعم فني متميز</h5>
                    <p class="text-muted">
                        فريق دعم فني متاح للمساعدة والإجابة على استفساراتك
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card p-4 text-center">
                    <h3 class="fw-bold mb-2">{{ \App\Models\Course::count() }}+</h3>
                    <p class="mb-0">دورة تدريبية</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card p-4 text-center">
                    <h3 class="fw-bold mb-2">{{ \App\Models\User::where('role', 'user')->count() }}+</h3>
                    <p class="mb-0">طالب مسجل</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card p-4 text-center">
                    <h3 class="fw-bold mb-2">{{ \App\Models\Purchase::where('payment_status', 'completed')->count() }}+</h3>
                    <p class="mb-0">عملية شراء</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card p-4 text-center">
                    <h3 class="fw-bold mb-2">{{ \App\Models\ServiceRequest::where('status', 'completed')->count() }}+</h3>
                    <p class="mb-0">خدمة مكتملة</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Courses Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3">الدورات الأكثر شعبية</h2>
                <p class="text-muted">اكتشف أفضل الدورات التدريبية المتاحة على المنصة</p>
            </div>
        </div>
        
        <div class="row g-4">
            @php
                $popularCourses = \App\Models\Course::where('status', 'active')
                    ->withCount(['purchases' => function($query) {
                        $query->where('payment_status', 'completed');
                    }])
                    ->orderBy('purchases_count', 'desc')
                    ->limit(3)
                    ->get();
            @endphp
            
            @forelse($popularCourses as $course)
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-book text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit($course->description, 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $course->averageRating())
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <small class="text-muted ms-1">({{ $course->reviews()->count() }})</small>
                                </div>
                                <span class="badge bg-primary">{{ $course->studentsCount() }} طالب</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="text-primary mb-0">{{ $course->formatted_price }}</h5>
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">لا توجد دورات متاحة حالياً</p>
                </div>
            @endforelse
        </div>
        
        @if($popularCourses->count() > 0)
            <div class="text-center mt-4">
                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-lg">
                    عرض جميع الدورات
                    <i class="fas fa-arrow-left ms-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">ابدأ رحلتك التعليمية اليوم</h2>
        <p class="lead mb-4">انضم إلى آلاف الطلاب الذين يطورون مهاراتهم معنا</p>
        
        @guest
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>
                    إنشاء حساب مجاني
                </a>
                <a href="{{ route('register.teacher') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    إنشاء حساب معلم
                </a>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-search me-2"></i>
                    تصفح الدورات
                </a>
            </div>
        @else
            <a href="{{ route('courses.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-book me-2"></i>
                تصفح الدورات المتاحة
            </a>
        @endguest
    </div>
</section>
@endsection

