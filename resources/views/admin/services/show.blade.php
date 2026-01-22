@extends('layouts.admin')

@section('title', 'تفاصيل طلب الخدمة #' . $serviceRequest->id)

@section('content')
<div class="container mt-4">
    <h2>تفاصيل طلب الخدمة رقم #{{ $serviceRequest->id }}</h2>
    <hr>

    <div class="mb-3">
        <strong>العنوان:</strong>
        <p>{{ $serviceRequest->title }}</p>
    </div>

    <div class="mb-3">
        <strong>الوصف:</strong>
        <p>{{ $serviceRequest->description }}</p>
    </div>

    <div class="mb-3">
        <strong>نوع الخدمة:</strong>
        <p>{{ $serviceRequest->serviceType->name ?? 'غير محدد' }}</p>
    </div>

    <div class="mb-3">
        <strong>حالة الطلب:</strong>
        <p>{{ ucfirst($serviceRequest->status) }}</p>
    </div>

    <div class="mb-3">
        <strong>مقدم الطلب:</strong>
        <p>{{ $serviceRequest->user->name ?? 'غير معروف' }} ({{ $serviceRequest->user->email ?? '' }})</p>
    </div>

    <div class="mb-3">
        <strong>تاريخ الطلب:</strong>
        <p>{{ $serviceRequest->created_at->format('Y-m-d H:i') }}</p>
    </div>

    {{-- الملفات المرفقة --}}
    @if($serviceRequest->files && $serviceRequest->files->count() > 0)
        <div class="mb-3">
            <strong>الملفات المرفقة:</strong>
            <ul>
                @foreach($serviceRequest->files as $file)
                    <li><a href="{{ route('services.download-file', $file->id) }}" target="_blank">{{ $file->filename }}</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- الرسائل المرتبطة --}}
    <div class="mb-3">
        <strong>الرسائل:</strong>
        @if($serviceRequest->messages && $serviceRequest->messages->count() > 0)
            <ul class="list-group">
                @foreach($serviceRequest->messages as $message)
                    <li class="list-group-item">
                        <strong>{{ $message->sender->name ?? 'مستخدم' }}:</strong>
                        <p>{{ $message->message }}</p>
                        <small class="text-muted">{{ $message->created_at->format('Y-m-d H:i') }}</small>

                        @if($message->files && $message->files->count() > 0)
                            <div>
                                <strong>مرفقات الرسالة:</strong>
                                <ul>
                                    @foreach($message->files as $msgFile)
                                        <li><a href="{{ route('messages.download-file', $msgFile->id) }}" target="_blank">{{ $msgFile->filename }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p>لا توجد رسائل بعد.</p>
        @endif
    </div>

</div>
@endsection
