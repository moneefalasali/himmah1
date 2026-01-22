@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="mb-4">
                        @if($result->percentage >= 50)
                            <i class="fas fa-check-circle text-success fa-5x"></i>
                            <h2 class="mt-3">تهانينا! لقد نجحت</h2>
                        @else
                            <i class="fas fa-times-circle text-danger fa-5x"></i>
                            <h2 class="mt-3">للأسف، لم تتجاوز الاختبار</h2>
                        @endif
                    </div>

                    <h4 class="text-muted mb-4">{{ $result->quiz->title }}</h4>

                    <div class="row mb-4">
                        <div class="col-6 border-right">
                            <h3>{{ $result->earned_points }} / {{ $result->total_points }}</h3>
                            <p class="text-muted">النقاط</p>
                        </div>
                        <div class="col-6">
                            <h3>{{ number_format($result->percentage, 1) }}%</h3>
                            <p class="text-muted">النسبة المئوية</p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        تم إكمال الاختبار في: {{ $result->completed_at->format('Y-m-d H:i') }}
                    </div>

                    <div class="mt-5 text-right">
                        <h5>مراجعة الإجابات:</h5>
                        <hr>
                        @foreach($result->answers as $answer)
                        <div class="mb-4 p-3 border rounded {{ $answer->is_correct ? 'bg-light-success' : 'bg-light-danger' }}">
                            <p class="font-weight-bold">س: {{ $answer->question->question_text }}</p>
                            <p>
                                إجابتك: 
                                <span class="{{ $answer->is_correct ? 'text-success' : 'text-danger' }}">
                                    {{ $answer->option ? $answer->option->option_text : ($answer->answer_text ?? 'لا توجد إجابة') }}
                                </span>
                            </p>
                            @if(!$answer->is_correct)
                                @php
                                    $correctOption = $answer->question->options->where('is_correct', true)->first();
                                @endphp
                                @if($correctOption)
                                <p class="text-success">الإجابة الصحيحة: {{ $correctOption->option_text }}</p>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <a href="{{ route('teacher.courses.index') }}" class="btn btn-primary mt-4">العودة للدورة</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-success { background-color: #e8f5e9; }
.bg-light-danger { background-color: #ffebee; }
</style>
@endsection
