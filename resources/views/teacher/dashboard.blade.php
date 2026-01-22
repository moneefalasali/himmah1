@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0">مرحباً بك، {{ auth()->user()->name }}</h2>
            <p class="text-muted small mb-0">إليك ملخص لأداء دوراتك التعليمية.</p>
        </div>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إنشاء دورة جديدة
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">طلابك المسجلين</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="mb-0">{{ $totalStudents ?? 0 }}</h3>
                                <span class="small {{ isset($studentsChangePercent) && $studentsChangePercent < 0 ? 'text-danger' : 'text-success' }}">
                                    @if(isset($studentsChangePercent))
                                        <i class="fas fa-arrow-{{ $studentsChangePercent < 0 ? 'down' : 'up' }}"></i> {{ abs($studentsChangePercent) }}%
                                    @endif
                                </span>
                            </div>
                            <div class="text-muted small mt-2">هذا الشهر</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">إجمالي الأرباح</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="text-primary mb-0">{{ number_format($totalEarnings ?? 0, 2) }} {{ $currency ?? 'ر.س' }}</h3>
                            </div>
                            <div class="text-muted small mt-2">{{ $netEarningsLabel ?? 'صافي الأرباح بعد العمولات' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">تقييم دوراتك</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="text-warning mb-0">{{ $ratingAverage ?? '—' }} / 5</h3>
                            </div>
                            <div class="text-muted small mt-2">@if(isset($ratingCount)) بناءً على {{ $ratingCount }} تقييم @else لا توجد تقييمات بعد @endif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card content-card">
                <div class="card-body">
                    <h5 class="card-title">دوراتك التعليمية</h5>
                    <hr>
                    @if(!empty($courses) && $courses->count())
                        <div class="list-group">
                            @foreach($courses as $course)
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $course->thumbnail_url }}" class="rounded me-3" style="width:56px;height:56px;object-fit:cover;">
                                        <div>
                                            <div class="fw-bold">{{ $course->title }}</div>
                                            <div class="small text-muted">{{ $course->students_count ?? $course->studentsCount() }} طالب مسجل</div>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('chat.course', $course) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-comments"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-end">
                            <a href="{{ route('teacher.courses.index') }}" class="btn btn-sm btn-outline-secondary">عرض كل الدورات</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4 mb-0">لم تقم بإنشاء أي دورات بعد.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card content-card">
                <div class="card-body">
                    <h5 class="card-title">آخر المبيعات</h5>
                    <hr>
                    @if(!empty($recentSales) && $recentSales->count())
                        <ul class="list-unstyled mb-0">
                            @foreach($recentSales as $sale)
                                <li class="d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <div class="fw-bold">{{ $sale->user->name }}</div>
                                        <div class="small text-muted">{{ $sale->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-success fw-bold">+{{ $sale->amount }} ريال</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center py-4 mb-0">لا توجد مبيعات حديثة.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
