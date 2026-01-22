@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $quiz->title }}</h5>
                    @if($quiz->duration_minutes)
                    <div id="timer" class="badge badge-light p-2" data-seconds="{{ $quiz->duration_minutes * 60 }}">
                        الوقت المتبقي: <span id="time-display">--:--</span>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <form id="quiz-form" action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST">
                        @csrf
                        @foreach($questions as $index => $question)
                        <div class="question-block mb-4 {{ $index > 0 ? 'd-none' : '' }}" id="question-{{ $index }}">
                            <h6>السؤال {{ $index + 1 }}: {{ $question->question_text }}</h6>
                            <hr>
                            
                            @if($question->type == 'multiple_choice' || $question->type == 'true_false')
                                @foreach($question->options as $option)
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="opt-{{ $option->id }}" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="custom-control-input">
                                    <label class="custom-control-label" for="opt-{{ $option->id }}">{{ $option->option_text }}</label>
                                </div>
                                @endforeach
                            @else
                                <div class="form-group">
                                    <textarea name="answers[{{ $question->id }}]" class="form-control" rows="3" placeholder="اكتب إجابتك هنا..."></textarea>
                                </div>
                            @endif

                            <div class="mt-4 d-flex justify-content-between">
                                @if($index > 0)
                                <button type="button" class="btn btn-outline-secondary prev-question" data-current="{{ $index }}">السابق</button>
                                @else
                                <div></div>
                                @endif

                                @if($index < count($questions) - 1)
                                <button type="button" class="btn btn-primary next-question" data-current="{{ $index }}">التالي</button>
                                @else
                                <button type="submit" class="btn btn-success">إنهاء وإرسال</button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // التنقل بين الأسئلة
    const nextBtns = document.querySelectorAll('.next-question');
    const prevBtns = document.querySelectorAll('.prev-question');

    nextBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const current = parseInt(this.dataset.current);
            document.getElementById('question-' + current).classList.add('d-none');
            document.getElementById('question-' + (current + 1)).classList.remove('d-none');
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const current = parseInt(this.dataset.current);
            document.getElementById('question-' + current).classList.add('d-none');
            document.getElementById('question-' + (current - 1)).classList.remove('d-none');
        });
    });

    // عداد الوقت
    const timerElement = document.getElementById('timer');
    if (timerElement) {
        let seconds = parseInt(timerElement.dataset.seconds);
        const display = document.getElementById('time-display');

        const timer = setInterval(function() {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            display.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

            if (seconds <= 0) {
                clearInterval(timer);
                alert('انتهى الوقت! سيتم إرسال إجاباتك تلقائياً.');
                document.getElementById('quiz-form').submit();
            }
            seconds--;
        }, 1000);
    }
});
</script>
@endsection
