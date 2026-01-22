@extends('layouts.teacher')

@section('title', 'أسئلة الاختبار')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">أسئلة: {{ $quiz->title }}</h1>
        <a href="{{ route('teacher.quizzes.questions.create', $quiz) }}" class="btn btn-primary">إضافة سؤال</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($questions->count())
                <ol>
                    @foreach($questions as $q)
                        <li class="mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $q->question_text }}</strong>
                                    <div class="text-muted">نوع: {{ $q->type }} — نقاط: {{ $q->points }}</div>
                                </div>
                                <div>
                                    <a href="{{ route('teacher.questions.edit', $q) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                    <form action="{{ route('teacher.questions.destroy', $q) }}" method="post" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                </div>
                            </div>
                            @if($q->options->count())
                                <ul class="mt-2">
                                    @foreach($q->options as $opt)
                                        <li>{{ $opt->option_text }} @if($opt->is_correct) <span class="badge bg-success">صحيح</span> @endif</li>
                                    @endforeach
                                </ul>
                            @endif
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
