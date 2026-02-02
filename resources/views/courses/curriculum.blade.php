@extends('layouts.app')

@section('title', 'منهج الدورة: ' . $course->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>منهج الدورة: {{ $course->title }}</h2>
            @if(isset($uniCourse))
                <p class="text-muted mb-0">
                    <i class="fas fa-university me-1"></i>
                    ترتيب مخصص لجامعة {{ $uniCourse->university->name }}
                </p>
            @endif
        </div>
        <div class="d-flex gap-2">
            @auth
                @if(auth()->user()->isSubscribedTo($course))
                    <a href="{{ route('student.courses.ai.show', $course) }}" class="btn btn-primary">
                        <i class="fas fa-robot me-1"></i> فتح مساعد همّه الذكي
                    </a>
                @endif
                @if(auth()->user()->isEnrolledIn($course))
                    <a href="{{ route('chat.course', $course) }}" class="btn btn-outline-primary">
                        <i class="fas fa-comments me-1"></i> دردشة الكورس
                    </a>
                @endif
            @endauth
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                العودة إلى الصفحة الرئيسية
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="mb-0">المناهج الدراسية</h4>
        </div>
        <div class="card-body">
            @if(isset($lessons))
                {{-- University-specific lesson order --}}
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    الدروس مرتبة حسب منهج جامعتك
                </div>
                
                <ul class="list-group">
                    @foreach($lessons as $lesson)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('lessons.show', $lesson) }}" class="text-decoration-none">
                                    <i class="fas fa-play-circle me-2"></i> {{ $lesson->title }}
                                </a>
                                <small class="d-block text-muted ms-4">
                                    @if($lesson->duration)
                                        {{ $lesson->formatted_duration }}
                                    @endif
                                </small>
                            </div>
                            <div>
                                @if($lesson->is_free)
                                    <span class="badge bg-success me-1">مجاني</span>
                                @endif

                                @if($lesson->progress && $lesson->progress->completed)
                                    <span class="badge bg-success">مكتمل</span>
                                @else
                                    <span class="badge bg-secondary">غير مكتمل</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                {{-- Default section-based layout --}}
                @forelse($course->sections as $section)
                    <div class="mb-4">
                        <h5 class="mb-3">{{ $section->title }}</h5>
                        
                        <ul class="list-group">
                            @foreach($section->lessons as $lesson)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('lessons.show', $lesson) }}" class="text-decoration-none">
                                            <i class="fas fa-play-circle me-2"></i> {{ $lesson->title }}
                                        </a>
                                        <small class="d-block text-muted ms-4">
                                            @if($lesson->duration)
                                                {{ $lesson->formatted_duration }}
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        @if($lesson->is_free)
                                            <span class="badge bg-success me-1">مجاني</span>
                                        @endif

                                        @if($lesson->progress && $lesson->progress->completed)
                                            <span class="badge bg-success">مكتمل</span>
                                        @else
                                            <span class="badge bg-secondary">غير مكتمل</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> لم يتم إضافة مناهج دراسية لهذه الدورة بعد.
                    </div>
                @endforelse
                {{-- دروس بدون قسم --}}
                @if(isset($lessonsWithoutSection) && $lessonsWithoutSection->count() > 0)
                    <div class="mt-4">
                        <h5 class="mb-3">دروس بدون قسم</h5>
                        <ul class="list-group">
                            @foreach($lessonsWithoutSection as $lesson)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('lessons.show', $lesson) }}" class="text-decoration-none">
                                            <i class="fas fa-play-circle me-2"></i> {{ $lesson->title }}
                                        </a>
                                        <small class="d-block text-muted ms-4">
                                            @if($lesson->duration)
                                                {{ $lesson->formatted_duration }}
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        @if($lesson->is_free)
                                            <span class="badge bg-success me-1">مجاني</span>
                                        @endif

                                        @if($lesson->progress && $lesson->progress->completed)
                                            <span class="badge bg-success">مكتمل</span>
                                        @else
                                            <span class="badge bg-secondary">غير مكتمل</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    @if(isset($course->quizzes) && $course->quizzes->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h4 class="mb-0">الاختبارات</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($course->quizzes as $quiz)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $quiz->title }}</strong>
                                <div class="text-muted small">{{ $quiz->questions_count }} سؤال • {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' دقيقة' : '' }}</div>
                            </div>
                            <div>
                                <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-primary">ابدأ الاختبار</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">العودة للوحة التحكم</a>
    </div>
@endsection