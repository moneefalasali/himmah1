@extends('layouts.admin')

@section('title', 'تفاصيل الدورة: ' . $course->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل الدورة</h2>
        <div>
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-2"></i>تعديل
            </a>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>العودة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">معلومات الدورة</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ $course->title }}</h5>
                            <p class="text-muted">{{ $course->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                @if($course->image)
                                    <img src="{{ Storage::url($course->image) }}" 
                                         alt="{{ $course->title }}" 
                                         class="img-fluid rounded" 
                                         style="max-width: 200px;">
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <strong>المدرب:</strong>
                            <p>{{ $course->instructor_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>السعر:</strong>
                            <p>{{ number_format($course->price, 2) }} ر.س</p>
                        </div>
                        <div class="col-md-3">
                            <strong>الحالة:</strong>
                            <span class="badge {{ $course->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $course->status === 'active' ? 'منشور' : 'غير منشور' }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>المدة:</strong>
                            <p>{{ $course->formatted_duration }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">المناهج الدراسية</h4>
                    <a href="{{ route('admin.courses.lessons', $course) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus me-1"></i>إضافة درس
                    </a>
                </div>
                <div class="card-body">
                    @if($course->lessons->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>العنوان</th>
                                        <th>المدة</th>
                                        <th>النوع</th>
                                        <th>الترتيب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->lessons as $lesson)
                                        <tr>
                                            <td>{{ $lesson->id }}</td>
                                            <td>{{ $lesson->title }}</td>
                                            <td>{{ $lesson->duration }} دقيقة</td>
                                            <td>
                                                <span class="badge {{ $lesson->is_free ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $lesson->is_free ? 'مجاني' : 'مدفوع' }}
                                                </span>
                                            </td>
                                            <td>{{ $lesson->order }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">لا توجد دروس لهذه الدورة بعد.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">الإحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ $stats['total_students'] }}</h3>
                                <small class="text-muted">الطلاب المسجلين</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ number_format($stats['total_revenue'], 2) }}</h3>
                                <small class="text-muted">إجمالي الإيرادات</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-warning mb-1">{{ number_format($stats['average_rating'], 1) }}</h3>
                                <small class="text-muted">متوسط التقييم</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-1">{{ $stats['total_reviews'] }}</h3>
                                <small class="text-muted">عدد المراجعات</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">أحدث المراجعات</h5>
                </div>
                <div class="card-body">
                    @if($course->reviews->count() > 0)
                        @foreach($course->reviews->take(5) as $review)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $review->user->name }}</strong>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-muted small mb-1">{{ $review->comment }}</p>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">لا توجد مراجعات بعد.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 