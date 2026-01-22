@extends('layouts.app')

@section('title', 'دوراتي')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">دوراتي</h1>
        <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>إضافة دورة جديدة
        </a>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-book text-primary fs-1"></i>
                    <h4 class="mt-2">{{ $courses->total() }}</h4>
                    <p class="text-muted mb-0">إجمالي الدورات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h4 class="mt-2">{{ $courses->where('status', 'active')->count() }}</h4>
                    <p class="text-muted mb-0">دورات منشورة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1"></i>
                            <h4 class="mt-2">{{ $courses->sum('students_count') }}</h4>
                    <p class="text-muted mb-0">إجمالي الطلاب</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle text-warning fs-1"></i>
                    <h4 class="mt-2">{{ $courses->sum('lessons_count') }}</h4>
                    <p class="text-muted mb-0">إجمالي الدروس</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الدورات -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">قائمة الدورات</h5>
        </div>
        <div class="card-body">
            @if($courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الدورة</th>
                                <th>الحالة</th>
                                <th>السعر</th>
                                <th>عدد الدروس</th>
                                <th>عدد الطلاب</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($course->image)
                                                <img src="{{ Storage::url($course->image) }}" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-book text-muted fs-4"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $course->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $course->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $course->status === 'active' ? 'منشور' : 'غير منشور' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($course->price, 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->lessons_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $course->students_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $course->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('teacher.courses.show', $course) }}" 
                                               class="btn btn-sm btn-outline-primary" title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.courses.edit', $course) }}" 
                                               class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('teacher.courses.lessons', $course) }}" 
                                               class="btn btn-sm btn-outline-info" title="إدارة الدروس">
                                                <i class="bi bi-play-circle"></i>
                                            </a>
                                            <a href="{{ route('teacher.quizzes.index', ['course' => $course->id]) }}" 
                                               class="btn btn-sm btn-outline-warning" title="إدارة الاختبارات">
                                                <i class="bi bi-question-circle"></i>
                                            </a>
                                            <a href="{{ route('chat.course', $course) }}" 
                                               class="btn btn-sm btn-outline-success" title="دردشة الكورس">
                                                <i class="bi bi-chat-dots"></i>
                                            </a>
                                            <a href="{{ route('teacher.courses.ai.show', $course) }}"
                                               class="btn btn-sm btn-outline-primary" title="المساعد الذكي">
                                                <i class="bi bi-robot"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ترقيم الصفحات -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted fs-1"></i>
                    <h4 class="mt-3 text-muted">لا توجد دورات بعد</h4>
                    <p class="text-muted">ابدأ بإنشاء دورة جديدة لمشاركة معرفتك مع الطلاب</p>
                    <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>إنشاء دورة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 