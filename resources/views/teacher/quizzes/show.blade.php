@extends('layouts.teacher')

@section('title', $quiz->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $quiz->title }}</h1>
        <div>
            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-outline-primary">تعديل</a>
            <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="btn btn-outline-secondary">إدارة الأسئلة</a>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <p>{{ $quiz->description }}</p>
            <p><strong>دورة:</strong> {{ $quiz->course?->title }}</p>
            <p><strong>المدة:</strong> {{ $quiz->duration_minutes ?? 'غير محدد' }} دقيقة</p>
            <p><strong>الحالة:</strong> {{ $quiz->status }}</p>

            <hr>
            <h5>الأسئلة</h5>
            @if($quiz->questions->count())
                <ol>
                    @foreach($quiz->questions as $q)
                        <li>
                            <div class="mb-2"><strong>{{ $q->text }}</strong></div>
                            <ul>
                                @foreach($q->options as $opt)
                                    <li>{{ $opt->text }} @if($opt->is_correct) <span class="badge bg-success">صحيح</span> @endif</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="text-muted">لا توجد أسئلة بعد.</p>
            @endif
        </div>
    </div>
</div>
@endsection
