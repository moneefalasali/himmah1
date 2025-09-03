@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="mb-3">{{ $course->title }}</h1>
                    <p class="text-muted fs-5">{{ $course->description }}</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>المدة:</strong> {{ $course->formatted_duration }}</p>
                            <p><strong>المدرب:</strong> {{ $course->instructor_name }}</p>
                            <p><strong>نوع المقرر:</strong> 
                                @if($course->isLarge())
                                    <span class="badge bg-warning">مقرر موسع</span>
                                @else
                                    <span class="badge bg-info">مقرر عادي</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>السعر:</strong> <span class="fw-bold fs-4 text-primary">{{ $course->formatted_price }}</span></p>
                            @if($course->features_description)
                                <p class="text-success mb-0">
                                    <i class="fas fa-gift me-1"></i>
                                    {{ $course->features_description }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    @if($course->includes_summary || $course->includes_tajmeeat)
                        <div class="alert alert-success mb-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-star me-2"></i>
                                مميزات إضافية مجانية
                            </h6>
                            <ul class="mb-0">
                                @if($course->includes_summary)
                                    <li>ملخص شامل للمقرر بصيغة PDF</li>
                                @endif
                                @if($course->includes_tajmeeat)
                                    <li>تجميعات أسئلة الاختبارات السابقة</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    
                    <div class="d-flex gap-2 mb-4">
                        @auth
                            @if($hasPurchased)
                                <a href="{{ route('courses.curriculum', $course) }}" class="btn btn-success btn-lg">
                                    <i class="bi bi-play-circle me-2"></i> متابعة الدراسة
                                </a>
                            @else
                                <a href="{{ route('payment.form', $course) }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cart me-2"></i> شراء الدورة
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-lock me-2"></i> سجل أولاً لشراء الدورة
                            </a>
                        @endauth
                        
                        <button class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-bookmark me-2"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">المحتوى</h4>
                </div>
                <div class="card-body">
                    <p>{{ $course->description }}</p>
                </div>
            </div>
            
            @if($freeLessons->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">الدروس المجانية - معاينة</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($freeLessons as $lesson)
                            <a href="{{ route('lessons.show', $lesson) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-play-circle text-success me-2"></i>
                                        <strong>{{ $lesson->title }}</strong>
                                        <span class="badge bg-success ms-2">مجاني</span>
                                    </div>
                                    <small class="text-muted">{{ $lesson->formatted_duration }}</small>
                                </div>
                                @if($lesson->description)
                                    <p class="mb-0 mt-2 text-muted">{{ Str::limit($lesson->description, 100) }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            يمكنك مشاهدة هذه الدروس مجاناً للحصول على فكرة عن محتوى الدورة
                        </small>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0">المراجعات</h4>
                </div>
                <div class="card-body">
                    @forelse($course->reviews as $review)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mt-2">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-muted">لا توجد مراجعات بعد.</p>
                    @endforelse
                    
                    @auth
                        @if($hasPurchased && !$course->reviews->where('user_id', auth()->id())->first())
                            <form action="{{ route('courses.review', $course) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">تقييمك:</label>
                                    <div class="d-flex gap-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="btn btn-sm {{ $i <= 3 ? 'btn-outline-warning' : 'btn-warning' }}">
                                                <input type="radio" name="rating" value="{{ $i }}" class="d-none" required>
                                                <i class="bi bi-star"></i> {{ $i }}
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">مراجعتك:</label>
                                    <textarea name="content" id="content" class="form-control" rows="3" 
                                              placeholder="اكتب مراجعتك..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال المراجعة</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-body">
                    <h5 class="card-title">عن المعلم</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="/instructor-default.jpg" class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-0">{{ $course->instructor_name }}</h6>
                        </div>
                    </div>
                    <p>مدرب معتمد ذو خبرة في المجال التقني والتعليم الإلكتروني.</p>
                    <a href="#" class="btn btn-outline-primary w-100" disabled>عرض الملف الشخصي</a>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">المواصفات</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-clock me-2"></i> {{ $course->duration }} ساعة فيديو</li>
                        <li class="mb-2"><i class="bi bi-file-earmark-text me-2"></i> {{ $course->resources_count }} موارد</li>
                        <li class="mb-2"><i class="bi bi-phone me-2"></i> متوافق مع الجوال</li>
                        <li class="mb-2"><i class="bi bi-laptop me-2"></i> متطلبات: {{ $course->requirements }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection