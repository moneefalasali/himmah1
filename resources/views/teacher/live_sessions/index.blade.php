@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>حصصي المباشرة</h3>
        <a href="{{ route('teacher.live-sessions.create') }}" class="btn btn-primary">إنشاء حصة جديدة</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @foreach($sessions as $session)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $session->topic }}</h5>
                    <p class="text-muted mb-1">الكورس: {{ $session->course->title }}</p>
                    <p class="mb-1"><strong>الموعد:</strong> {{ $session->start_time->format('Y-m-d H:i') }}</p>
                    <p class="mb-3"><strong>المدة:</strong> {{ $session->duration }} دقيقة</p>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ $session->start_url }}" target="_blank" class="btn btn-success flex-grow-1">بدء الحصة (Zoom)</a>
                        <form action="{{ route('teacher.live-sessions.destroy', $session) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الحصة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{ $sessions->links() }}
</div>
@endsection
