@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="container">
    <h1 class="mt-3">{{ $quiz->title }}</h1>
    <p>{{ $quiz->description }}</p>
    <p>المدة: {{ $quiz->duration_minutes ?? 'غير محددة' }} دقيقة</p>
    <p>الحالة: {{ $quiz->status }}</p>

    @if($existingResult)
        <div class="alert alert-info">لقد حللت هذا الاختبار مسبقاً. <a href="{{ route('student.quizzes.result', $existingResult->id) }}">عرض النتيجة</a></div>
    @else
        <a href="{{ route('student.quizzes.take', $quiz) }}" class="btn btn-primary">ابدأ الاختبار</a>
    @endif
</div>
@endsection
