@extends('layouts.app')

@section('title', 'الدورات التدريبية - منصة همة التعليمية')

@push('styles')
<style>
.course-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
.rating-stars { color: #fbbf24; }
.rating-stars .far { color: #e5e7eb; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold mb-3">الدورات التدريبية</h1>
            @if(isset($user) && $user->university_id)
                <p class="lead text-muted">
                    دورات {{ $user->university->name }}
                    @if($user->university->city)
                        - {{ $user->university->city }}
                    @endif
                </p>
                <div class="alert alert-info d-inline-block">
                    <i class="fas fa-university me-2"></i>
                    عرض المقررات المخصصة لجامعتك مع ترتيب الدروس المناسب لمنهجك
                </div>
            @else
                <p class="lead text-muted">اكتشف مجموعة واسعة من الدورات التدريبية عالية الجودة</p>
            @endif
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('courses.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="ابحث عن دورة...">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="sort">
                                    <option value="">ترتيب حسب</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر شعبية</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-search me-1"></i> بحث
                                    </button>
                                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Info -->
    @if(request()->hasAny(['search', 'sort']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    تم العثور على {{ $courses->total() }} دورة
                    @if(request('search'))
                        للبحث عن "{{ request('search') }}"
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Courses Grid -->
    <div class="row g-4">
        @if(isset($uniCourses))
            @forelse($uniCourses as $uniCourse)
                @php $course = $uniCourse->course; @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100 position-relative">
                        <!-- Course Image -->
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}"
                                 class="card-img-top"
                                 alt="{{ $uniCourse->display_name }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-book text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif

                        <!-- University Badge -->
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-success">
                                <i class="fas fa-university me-1"></i>
                                {{ $user->university->name }}
                            </span>
                        </div>

                        <!-- Course Content -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $uniCourse->display_name }}</h5>
                            @if($uniCourse->custom_name && $uniCourse->custom_name !== $course->title)
                                <small class="text-muted mb-2">المقرر الأصلي: {{ $course->title }}</small>
                            @endif
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit($course->description, 100) }}
                            </p>

                            <!-- Meta -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $course->instructor_name }}</small>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $uniCourse->total_lessons }} درس</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
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
                            </div>

                            <!-- Price + Action -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-primary mb-0">{{ $course->formatted_price }}</h5>
                                    <small class="text-success"><i class="fas fa-gift me-1"></i>يشمل الملخص والتجميعات مجاناً</small>
                                </div>

                                @auth
                                    @if(Auth::user()->hasPurchased($course->id))
                                        <a href="{{ route('courses.curriculum', $course) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-play me-1"></i> متابعة
                                        </a>
                                    @else
                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> عرض
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> عرض
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">لا توجد مقررات متاحة</h4>
                        <p class="text-muted">لم يتم إضافة أي مقررات لجامعتك بعد</p>
                    </div>
                </div>
            @endforelse
        @else
            @forelse($courses as $course)
                <div class="col-lg-4 col-md-6">
                    <div class="card course-card h-100">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}"
                                 class="card-img-top"
                                 alt="{{ $course->title }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-book text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit($course->description, 100) }}</p>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $course->instructor_name }}</small>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $course->total_lessons }} درس</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
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
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="text-primary mb-0">{{ $course->formatted_price }}</h5>

                                @auth
                                    @if(Auth::user()->hasPurchased($course->id))
                                        <a href="{{ route('courses.curriculum', $course) }}" class="btn btn-success">
                                            <i class="fas fa-play me-1"></i> ابدأ التعلم
                                        </a>
                                    @else
                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
                                            عرض التفاصيل
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
                                        عرض التفاصيل
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search text-muted" style="font-size: 5rem;"></i>
                        <h3 class="mt-3 text-muted">لا توجد دورات</h3>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'sort']))
                                لم يتم العثور على دورات تطابق معايير البحث
                            @else
                                لا توجد دورات متاحة حالياً
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'sort']))
                            <a href="{{ route('courses.index') }}" class="btn btn-primary">عرض جميع الدورات</a>
                        @endif
                    </div>
                </div>
            @endforelse
        @endif
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
        <div class="row mt-5">
            <div class="col-12">
                <nav aria-label="صفحات الدورات">
                    {{ $courses->appends(request()->query())->links() }}
                </nav>
            </div>
        </div>
    @endif
</div>
@endsection
