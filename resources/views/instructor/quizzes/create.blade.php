@extends('layouts.app')

@section('title', 'إنشاء اختبار جديد')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إنشاء اختبار جديد</h1>
        <div>
            <a href="{{ route('instructor.courses.quizzes', $course) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للاختبارات
            </a>
            <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-outline-primary">
                <i class="bi bi-book me-2"></i>عرض الدورة
            </a>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h4 class="mb-0">معلومات الاختبار الأساسية</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.quizzes.store', $course) }}" method="POST">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الاختبار <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الاختبار</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="time_limit" class="form-label">الوقت المحدد (بالدقائق)</label>
                                <input type="number" name="time_limit" id="time_limit" class="form-control" 
                                       value="{{ old('time_limit') }}" min="1" placeholder="غير محدد">
                                <div class="form-text">اترك فارغاً إذا كنت لا تريد تحديد وقت</div>
                                @error('time_limit')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="passing_score" class="form-label">درجة النجاح (%) <span class="text-danger">*</span></label>
                                <input type="number" name="passing_score" id="passing_score" class="form-control" 
                                       value="{{ old('passing_score', 70) }}" min="1" max="100" required>
                                @error('passing_score')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">تفعيل الاختبار فوراً</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">معلومات الدورة</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if($course->image)
                                        <img src="{{ Storage::url($course->image) }}" 
                                             class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="bi bi-book text-muted fs-4"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $course->title }}</h6>
                                        <small class="text-muted">{{ $course->instructor_name }}</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>عدد الدروس:</strong> {{ $course->lessons()->count() }}
                                </div>
                                
                                <div class="mb-3">
                                    <strong>عدد الطلاب:</strong> {{ $course->studentsCount() }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>نصائح لإنشاء اختبار فعال:</strong>
                            <ul class="mb-0 mt-2">
                                <li>اكتب أسئلة واضحة ومفهومة</li>
                                <li>استخدم مجموعة متنوعة من أنواع الأسئلة</li>
                                <li>حدد وقتاً مناسباً للاختبار</li>
                                <li>ضع درجة نجاح معقولة</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('instructor.courses.quizzes', $course) }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>إنشاء الاختبار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تحقق من صحة البيانات قبل الإرسال
    document.querySelector('form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const passingScore = parseInt(document.getElementById('passing_score').value);
        
        if (title.length < 5) {
            alert('يجب أن يكون عنوان الاختبار على الأقل 5 أحرف');
            e.preventDefault();
            return false;
        }
        
        if (passingScore < 1 || passingScore > 100) {
            alert('يجب أن تكون درجة النجاح بين 1 و 100');
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection 