@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">استكشف مستقبلك التعليمي</h1>
        <p class="text-muted">اختر من بين مئات الدورات المتخصصة في المناهج الجامعية والمهارات العامة مع أفضل الخبراء.</p>
    </div>

    <!-- Filters (simple row) -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">نوع التعليم</label>
                    <select name="classification" class="form-select">
                        <option value="" {{ request('classification') == '' ? 'selected' : '' }}>الكل</option>
                        <option value="university" {{ request('classification') == 'university' ? 'selected' : '' }}>جامعي</option>
                        <option value="general" {{ request('classification') == 'general' ? 'selected' : '' }}>دورات عامة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الجامعة</label>
                    <select name="university_id" class="form-select">
                        <option value="">اختر الجامعة...</option>
                        @foreach($universities as $uni)
                            <option value="{{ $uni->id }}" {{ request('university_id') == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الفئة</label>
                    <select name="category_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">نوع الدورة</label>
                    <select name="type" class="form-select">
                        <option value="" {{ request('type') == '' ? 'selected' : '' }}>الكل</option>
                        <option value="recorded" {{ request('type') == 'recorded' ? 'selected' : '' }}>مسجل</option>
                        <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>أونلاين</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">بحث سريع</label>
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="ابحث عن دورة...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="col-12 mt-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">تطبيق الفلتر</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Course Grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    @if($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" class="card-img-top" alt="{{ $course->title }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-light text-primary border">{{ $course->category->name ?? 'عام' }}</span>
                            <small class="text-muted"><i class="far fa-clock"></i> {{ $course->total_duration ?? '0' }} ساعة</small>
                        </div>
                        <h5 class="card-title">{{ $course->title }}</h5>
                        <p class="card-text text-muted mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 120) }}</p>

                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <small class="d-block text-uppercase text-muted">السعر</small>
                                <div class="h5 mb-0">{{ $course->price }} <small class="text-muted">ريال</small></div>
                            </div>
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">اشترك الآن</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">لم يتم العثور على دورات.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $courses->links() }}
    </div>
</div>
@endsection
