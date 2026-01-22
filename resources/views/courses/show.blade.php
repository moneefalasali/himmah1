@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- تفاصيل الكورس -->
        <div class="lg:col-span-2">
            <div class="card mb-4">
                <img src="{{ $course->thumbnail_url }}" class="card-img-top" style="height:420px;object-fit:cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-1">{{ $course->title }}</h1>
                            <div class="text-muted small">{{ $course->subject?->name }} • {{ $course->university?->name }}</div>
                        </div>
                        <div>
                            <span class="badge bg-info text-dark">{{ $course->type === 'recorded' ? 'مسجل' : 'أونلاين' }}</span>
                        </div>
                    </div>

                    <p class="text-muted mb-4">{{ $course->description }}</p>

                    <h5 class="mb-3">محتوى الدورة</h5>

                    @if($course->sections && $course->sections->count() > 0)
                        @foreach($course->sections as $section)
                            <div class="mb-3">
                                <div class="fw-bold">{{ $section->title }} <span class="text-muted small">({{ $section->lessons->count() }} درس)</span></div>
                                @if($section->lessons->count() > 0)
                                    <ul class="list-group mt-2">
                                        @foreach($section->lessons as $lesson)
                                            <a href="{{ route('lessons.show', $lesson) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none text-reset">
                                                <div>
                                                    <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                                    <strong class="me-2">{{ $lesson->title }}</strong>
                                                    <span class="text-muted small">{{ Str::limit($lesson->description ?? '', 80) }}</span>
                                                </div>
                                                <span class="text-muted small">{{ $lesson->duration }} دقيقة</span>
                                            </a>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted small">لا توجد دروس في هذا القسم بعد.</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        @if($course->lessons->count() > 0)
                            <ul class="list-group">
                                @foreach($course->lessons as $lesson)
                                    <a href="{{ route('lessons.show', $lesson) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none text-reset">
                                        <div>
                                            <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                            <strong>{{ $lesson->title }}</strong>
                                        </div>
                                        <span class="text-muted small">{{ $lesson->duration }} دقيقة</span>
                                    </a>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-secondary">لا توجد دروس لهذه الدورة بعد.</div>
                        @endif
                    @endif

                </div>
            </div>
        </div>

        <!-- الشريط الجانبي (Sidebar) -->
        <div class="lg:col-span-1">
            <div class="card sticky-top" style="top:24px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $course->teacher?->avatar_url ?? url('assets/images/default-avatar.png') }}" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
                        <div>
                            <div class="fw-bold">{{ $course->teacher?->name }}</div>
                            <div class="text-muted small">{{ $course->teacher?->role ?? 'المعلم' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="h4 mb-0">{{ $course->price > 0 ? $course->price . ' ريال' : 'مجاني' }}</div>
                        <div class="text-muted small">{{ $course->students_count ?? '' }} طالب</div>
                    </div>

                    @if(auth()->check() && auth()->user()->isEnrolledIn($course))
                    <div class="space-y-4">
                        @php $firstLesson = $course->lessons->first(); @endphp
                        @if($firstLesson)
                            <a href="{{ route('lessons.show', $firstLesson) }}" class="block w-full bg-blue-600 text-white text-center font-bold py-4 rounded-xl hover:bg-blue-700 transition">
                                متابعة التعلم
                            </a>
                        @endif
                        <a href="{{ route('chat.course', $course) }}" class="block w-full bg-green-600 text-white text-center font-bold py-4 rounded-xl hover:bg-green-700 transition">
                            <i class="fas fa-comments mr-2"></i> دردشة الكورس الجماعية
                        </a>
                    </div>
                    <div class="mt-3">
                        @php
                            $courseQuizzes = $course->quizzes()->where('status', 'published')->get();
                        @endphp
                        @if($courseQuizzes->isNotEmpty())
                            <hr>
                            <h6 class="fw-bold">الاختبارات</h6>
                            <ul class="list-unstyled">
                                @foreach($courseQuizzes as $q)
                                    <li class="mb-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $q->title }}</strong>
                                            <div class="text-muted small">{{ $q->questions()->count() }} سؤال</div>
                                        </div>
                                        <div>
                                            <a href="{{ route('student.quizzes.show', $q) }}" class="btn btn-sm btn-outline-primary ms-2">عرض</a>
                                            <a href="{{ route('student.quizzes.take', $q) }}" class="btn btn-sm btn-primary">ابدأ</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @else
                        @if(auth()->check())
                            <a href="{{ route('payment.form', $course) }}" class="btn btn-primary w-100 mb-3">اشترك الآن</a>
                        @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(url()->full()) }}" class="btn btn-primary w-100 mb-3">اشترك الآن</a>
                        @endif
                @endif
                    <hr>
                    <h6 class="fw-bold">هل تحتاج مساعدة؟</h6>
                    @if(auth()->check() && auth()->user()->isEnrolledIn($course))
                        <a href="{{ route('student.courses.ai.show', $course) }}" class="btn btn-outline-indigo w-100 mb-2">اسأل المساعد الذكي</a>
                    @endif
                    <a href="{{ env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com' }}" target="_blank" rel="noopener" class="btn btn-outline-primary w-100">تواصل مع الدعم الفني</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
