@extends('layouts.app')

@section('title', 'لوحة تحكم المعلم')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">لوحة تحكم المعلم</h1>
        <div>
            <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle me-2"></i>إضافة دورة جديدة
            </a>
            <a href="{{ route('instructor.earnings') }}" class="btn btn-success">
                <i class="bi bi-graph-up me-2"></i>تقارير الأرباح
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-book text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الدورات</h6>
                            <h4 class="mb-0">{{ $totalCourses }}</h4>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>{{ $activeCourses }} منشورة
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-play-circle text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الدروس</h6>
                            <h4 class="mb-0">{{ $totalLessons }}</h4>
                            <small class="text-info">
                                <i class="bi bi-info-circle me-1"></i>محتوى تعليمي
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الطلاب</h6>
                            <h4 class="mb-0">{{ $totalStudents }}</h4>
                            <small class="text-success">
                                <i class="bi bi-arrow-up me-1"></i>طلاب مسجلين
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-question-circle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الاختبارات</h6>
                            <h4 class="mb-0">{{ $totalQuizzes }}</h4>
                            <small class="text-warning">
                                <i class="bi bi-clipboard-check me-1"></i>اختبارات نشطة
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الأرباح -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-success"></i>
                        إحصائيات الأرباح
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-success mb-1">{{ number_format($totalRevenue, 2) }} ر.س</h3>
                                <p class="text-muted mb-0">إجمالي الإيرادات</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-primary mb-1">{{ number_format($instructorProfit, 2) }} ر.س</h3>
                            <p class="text-muted mb-0">أرباحك (40%)</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">نسبة الأرباح</span>
                            <span class="badge bg-success">40%</span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2 text-info"></i>
                        ملخص الأداء
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $activeCourses }}</h4>
                                <small class="text-muted">دورات نشطة</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-info mb-1">{{ $totalLessons }}</h4>
                                <small class="text-muted">درس</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success mb-1">{{ $totalStudents }}</h4>
                            <small class="text-muted">طالب</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الدورات الأخيرة -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-book me-2 text-primary"></i>
                        الدورات الأخيرة
                    </h5>
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recentCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الدورة</th>
                                        <th>الحالة</th>
                                        <th>عدد الدروس</th>
                                        <th>عدد الطلاب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCourses as $course)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($course->image)
                                                        <img src="{{ Storage::url($course->image) }}" 
                                                             class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $course->title }}</h6>
                                                        <small class="text-muted">{{ $course->instructor_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $course->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $course->status === 'active' ? 'منشور' : 'غير منشور' }}
                                                </span>
                                            </td>
                                            <td>{{ $course->lessons_count ?? 0 }}</td>
                                            <td>{{ $course->purchases_count ?? 0 }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('instructor.courses.show', $course) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('instructor.courses.edit', $course) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-book text-muted fs-1"></i>
                            <p class="text-muted mt-2">لا توجد دورات بعد</p>
                            <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>إضافة دورة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check me-2 text-success"></i>
                        آخر المبيعات
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentSales->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentSales as $sale)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $sale->course->title }}</h6>
                                            <small class="text-muted">{{ $sale->user->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success">{{ number_format($sale->amount, 2) }} ر.س</span>
                                            <br>
                                            <small class="text-muted">{{ $sale->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart text-muted fs-1"></i>
                            <p class="text-muted mt-2">لا توجد مبيعات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 