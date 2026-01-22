@extends('layouts.student')

@section('content')
<div class="container">
    <h3>جدول الحصص المباشرة</h3>
    <div class="row mt-4">
        @forelse($sessions as $session)
            <div class="col-md-6 mb-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">{{ $session->topic }}</h5>
                        <p class="text-muted">الكورس: {{ $session->course->title }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>الموعد:</strong> {{ $session->start_time->format('Y-m-d h:i A') }}<br>
                                <strong>المدة:</strong> {{ $session->duration }} دقيقة
                            </div>
                            <div>
                                @if($session->isLive())
                                    <a href="{{ route('student.live-sessions.join', $session) }}" class="btn btn-success btn-lg">
                                        دخول الحصة الآن
                                    </a>
                                @else
                                    <button class="btn btn-secondary disabled">
                                        غير متاحة الآن
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">لا توجد حصص مباشرة مجدولة حالياً.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
