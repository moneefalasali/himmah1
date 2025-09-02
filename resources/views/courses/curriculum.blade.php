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
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
            العودة إلى الصفحة الرئيسية
        </a>
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
            @endif
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">العودة للوحة التحكم</a>
    </div>
@endsection