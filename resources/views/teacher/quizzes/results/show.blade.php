@extends('layouts.teacher')

@section('title', 'تفاصيل المحاولة')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">محاولة الطالب: {{ $result->user->name ?? $result->user->email }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>الاختبار:</strong> {{ $result->quiz->title }}</p>
            <p><strong>النقاط:</strong> {{ $result->earned_points }} / {{ $result->total_points }}</p>
            <p><strong>النسبة:</strong> {{ number_format($result->percentage, 2) }}%</p>
            <p><strong>أُنجز في:</strong> {{ $result->completed_at?->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>تفاصيل الأسئلة</h5>
            <ol>
                @foreach($result->answers as $ans)
                    <li class="mb-3">
                        <div><strong>{{ $ans->question->question_text }}</strong></div>
                        <div class="mt-1">
                            @if($ans->option)
                                <div>إجابة الطالب: {{ $ans->option->option_text }}</div>
                                <div>صحيح؟ @if($ans->is_correct) نعم @else لا @endif</div>
                            @else
                                <div>إجابة الطالب: {{ $ans->answer_text }}</div>
                            @endif
                            <div class="text-muted">نقاط السؤال: {{ $ans->question->points }}</div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
@endsection
