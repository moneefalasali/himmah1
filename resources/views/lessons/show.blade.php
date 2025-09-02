@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h4 class="mb-0">{{ $lesson->title }}</h4>
                        <small class="text-muted">الدورة: {{ $lesson->course->title }}</small>
                    </div>
                    <span class="badge bg-primary">{{ $lesson->duration }} دقيقة</span>
                </div>
                
                <div class="card-body">
                    @if($lesson->vimeo_video_id && $lesson->video_platform === 'vimeo')
                        <!-- Vimeo Player -->
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="https://player.vimeo.com/video/{{ $lesson->vimeo_video_id }}" 
                                    title="{{ $lesson->title }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                    @elseif($lesson->video_url)
                        <!-- External Video URL -->
                        <div class="ratio ratio-16x9 mb-4">
                            @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                <!-- YouTube Video -->
                                @php
                                    $videoId = null;
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $lesson->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                @if($videoId)
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                            title="{{ $lesson->title }}" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen></iframe>
                                @else
                                    <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                                @endif
                            @elseif(str_contains($lesson->video_url, 'vimeo.com'))
                                <!-- Vimeo Video from URL -->
                                @php
                                    $videoId = null;
                                    if (preg_match('/vimeo\.com\/(\d+)/', $lesson->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                @if($videoId)
                                    <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                            title="{{ $lesson->title }}" 
                                            frameborder="0" 
                                            allow="autoplay; fullscreen; picture-in-picture" 
                                            allowfullscreen></iframe>
                                @else
                                    <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                                @endif
                            @else
                                <!-- Generic Video URL -->
                                <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                            @endif
                        </div>
                    @else
                        <!-- No Video Available -->
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            لا يوجد فيديو متاح لهذا الدرس حالياً.
                        </div>
                    @endif
                    
                    <div class="lesson-content">
                        {!! $lesson->content !!}
                    </div>
                    
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            @if($previousLesson)
                                <a href="{{ route('lessons.show', $previousLesson) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-chevron-right me-2"></i> الدرس السابق
                                </a>
                            @else
                                <div></div>
                            @endif
                            
                            <form action="{{ route('lessons.complete', $lesson) }}" method="POST" class="ms-auto">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    تم الدرس <i class="bi bi-check-circle ms-2"></i>
                                </button>
                            </form>
                            
                            @if($nextLesson)
                                <a href="{{ route('lessons.show', $nextLesson) }}" class="btn btn-primary ms-2">
                                    الدرس التالي <i class="bi bi-chevron-left ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($lesson->quiz)
                <div class="card">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">الاختبار</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ $lesson->quiz->title }}</p>
                        
                        <form action="{{ route('lessons.quiz.submit', $lesson) }}" method="POST">
                            @csrf
                            @foreach($lesson->quiz->questions as $question)
                                <div class="mb-4">
                                    <h5>{{ $question->question }}</h5>
                                    @foreach($question->answers as $answer)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" 
                                                   value="{{ $answer->id }}" id="answer-{{ $answer->id }}" required>
                                            <label class="form-check-label" for="answer-{{ $answer->id }}">
                                                {{ $answer->answer }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            
                            <button type="submit" class="btn btn-primary">إرسال الاختبار</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">محتويات الدورة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($lesson->course->sections as $section)
                            <div class="list-group-item bg-light fw-bold">
                                {{ $section->title }}
                            </div>
                            @foreach($section->lessons as $courseLesson)
                                <a href="{{ route('lessons.show', $courseLesson) }}" 
                                   class="list-group-item list-group-item-action 
                                   {{ $courseLesson->id == $lesson->id ? 'active' : '' }}">
                                    @php
                                        $lessonProgress = auth()->user() ? $courseLesson->progressForUser(auth()->id()) : null;
                                        $isCompleted = $lessonProgress && $lessonProgress->completed;
                                    @endphp
                                    <i class="bi {{ $isCompleted ? 'bi-check-circle text-success' : 'bi-circle' }} me-2"></i>
                                    {{ $courseLesson->title }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // تأكيد إكمال الدرس
        document.querySelector('form[action*="complete"]').addEventListener('submit', function(e) {
            if (!confirm('هل أنت متأكد من إكمال هذا الدرس؟')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
