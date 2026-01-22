@extends('layouts.admin')

@section('title', 'محادثات')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $room->name ?? ($room->course->title ?? 'محادثة') }}</h4>
            <small class="text-muted">نوع: {{ $room->type }}</small>
        </div>
        <div>
            <a href="{{ route('admin.customer-chats.index') }}" class="btn btn-outline-secondary">عودة</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body" style="max-height:60vh;overflow:auto;">
            @if($messages->count())
                @foreach($messages as $message)
                    <div class="mb-3">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <strong>{{ $message->user?->name ?? 'مستخدم' }}</strong>
                                <div class="text-muted small">{{ $message->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="p-3 bg-light rounded">{{ $message->message }}</div>
                                @if($message->attachments && $message->attachments->count())
                                    <div class="mt-2">
                                        @foreach($message->attachments as $att)
                                            <a href="{{ asset($att->path) }}" target="_blank">مرفق</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">لا توجد رسائل بعد.</p>
            @endif
        </div>
    </div>

    {{-- Optional: reply form (not functional without controller route) --}}
    <div class="card">
        <div class="card-body">
            <form action="#" method="POST">
                @csrf
                <div class="mb-2">
                    <textarea name="message" class="form-control" rows="3" placeholder="اكتب رسالة..."></textarea>
                </div>
                <div>
                    <button class="btn btn-primary">إرسال</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
