@extends('layouts.app')

@section('title', 'طلب الخدمة #'. $serviceRequest->id)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h3 class="mb-0">طلب #{{ $serviceRequest->id }} - {{ $serviceRequest->type_label }}</h3>
                        <span class="badge 
                            @if($serviceRequest->status == 'completed') bg-success
                            @elseif($serviceRequest->status == 'in_progress') bg-info
                            @elseif($serviceRequest->status == 'pending') bg-warning
                            @elseif($serviceRequest->status == 'canceled') bg-danger
                            @else bg-secondary @endif">
                            {{ $serviceRequest->status_label }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted">تاريخ الطلب: {{ $serviceRequest->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">التفاصيل</h5>
                            <p class="bg-light p-3 rounded">{{ $serviceRequest->details }}</p>
                            
                            @if($serviceRequest->additional_notes)
                                <h5 class="mt-4 mb-3">ملاحظات إضافية</h5>
                                <p class="bg-light p-3 rounded">{{ $serviceRequest->additional_notes }}</p>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">المعلومات</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>الحالة:</span>
                                    <span class="fw-bold 
                                        @if($serviceRequest->status == 'completed') text-success
                                        @elseif($serviceRequest->status == 'in_progress') text-info
                                        @elseif($serviceRequest->status == 'pending') text-warning
                                        @elseif($serviceRequest->status == 'canceled') text-danger
                                        @endif">
                                        {{ $serviceRequest->status_label }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>السعر:</span>
                                    <span class="fw-bold">{{ number_format($serviceRequest->total_price, 2) }} ر.س</span>
                                </li>
<li class="list-group-item d-flex justify-content-between">
    <span>التاريخ المتوقع:</span>
    <span>
        {{ optional($serviceRequest->expected_deadline)->format('Y-m-d') ?? '—' }}
    </span>
</li>

<li class="list-group-item d-flex justify-content-between">
    <span>الأولوية:</span>
    <span class="{{ $serviceRequest->is_urgent ? 'text-danger fw-bold' : 'text-muted' }}">
        {{ $serviceRequest->is_urgent ? 'عاجل (+20%)' : 'عادي' }}
    </span>
</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            @if($serviceRequest->file_path)
                                <div class="mb-3">
                                    <a href="{{ route('services.download-file', $serviceRequest->file) }}" 
                                       class="btn btn-info w-100">
                                        <i class="bi bi-download me-2"></i> تحميل الملف المرفق
                                    </a>
                                </div>
                            @endif
                            
                            @if($serviceRequest->result_file_path)
                                <div class="mb-3">
                                    <a href="{{ route('services.download-file', $serviceRequest->result_file) }}" 
                                       class="btn btn-success w-100">
                                        <i class="bi bi-file-earmark-arrow-down me-2"></i> تحميل نتيجة الخدمة
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            @if($serviceRequest->status == 'pending' || $serviceRequest->status == 'in_progress')
                                <form action="{{ route('services.cancel', $serviceRequest) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')">
                                        <i class="bi bi-x-circle me-2"></i> إلغاء الطلب
                                    </button>
                                </form>
                            @endif
                            
                            @if($serviceRequest->status == 'completed' && !$serviceRequest->is_rated)
                                <a href="{{ route('services.rate', $serviceRequest) }}" class="btn btn-warning w-100">
                                    <i class="bi bi-star me-2"></i> تقييم الخدمة
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">المراسلة</h5>
        <small class="text-muted">
            آخر تحديث: {{ optional($serviceRequest->last_message_at)->diffForHumans() ?? '—' }}
        </small>
    </div>

    <div class="card-body" id="messages-container" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
        @forelse($serviceRequest->messages as $message)
            <div class="mb-3 p-3 rounded
                {{ $message->sender_id == auth()->id() ? 'bg-primary text-white ms-auto' : 'bg-light me-auto' }}"
                style="max-width: 70%;">
                <div class="d-flex justify-content-between mb-1">
                    <strong>{{ $message->sender->name }}</strong>
                    <small>{{ $message->created_at->format('H:i') }}</small>
                </div>
                <p class="mb-0">{{ $message->content }}</p>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                <p>لا توجد رسائل بعد. يمكنك بدء المحادثة الآن.</p>
            </div>
        @endforelse
    </div>

    <div class="card-footer">
        <form action="{{ route('messages.store', $serviceRequest) }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="اكتب رسالة..." required>
                <button type="submit" class="btn btn-primary">إرسال</button>
            </div>
        </form>
    </div>
</div>
        
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">معلومات المقدم</h5>
                </div>
                <div class="card-body">
                    @if($serviceRequest->provider)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $serviceRequest->provider->avatar_url ?? '/default-avatar.png' }}" 
                                 class="rounded-circle me-3" width="60" height="60">
                            <div>
                                <h6 class="mb-0">{{ $serviceRequest->provider->name }}</h6>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $serviceRequest->provider->average_rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-1">({{ $serviceRequest->provider->completed_requests_count }})</span>
                                </div>
                            </div>
                        </div>
                        <p>{{ Str::limit($serviceRequest->provider->bio, 100) }}</p>
                        <a href="#" class="btn btn-outline-primary w-100">عرض الملف الشخصي</a>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> 
                            لم يتم تعيين مقدم خدمة بعد.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الإجراءات السريعة</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-plus-circle me-2"></i> طلب خدمة جديدة
                    </a>
                    <a href="{{ route('my-service-requests') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-list me-2"></i> جميع طلباتي
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // تحميل الرسائل تلقائياً
        function loadMessages() {
            fetch("{{ route('messages.get', $serviceRequest) }}")
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('messages-container');
                    let messagesHTML = '';
                    
                    data.messages.forEach(message => {
                        const isCurrentUser = message.sender_id == {{ auth()->id() }};
                        const classes = isCurrentUser ? 
                            'bg-primary text-white ms-auto' : 
                            'bg-light me-auto';
                            
                        messagesHTML += `
                            <div class="mb-3 p-3 rounded ${classes}" style="max-width: 70%;">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong>${message.sender}</strong>
                                    <small>${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                                </div>
                                <p class="mb-0">${message.content}</p>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = messagesHTML;
                    container.scrollTop = container.scrollHeight;
                });
        }
        
        // تحديث الرسائل كل 10 ثوانٍ
        setInterval(loadMessages, 10000);
        loadMessages();
    </script>
@endsection