@extends('layouts.app')

@section('title', 'تعديل الاختبار: ' . $quiz->title)

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تعديل الاختبار: {{ $quiz->title }}</h1>
        <div>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للاختبارات
            </a>
            <a href="{{ route('teacher.courses.show', $quiz->course) }}" class="btn btn-outline-primary">
                <i class="bi bi-book me-2"></i>عرض الدورة
            </a>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h4 class="mb-0">تعديل معلومات الاختبار</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.quizzes.update', $quiz) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الاختبار <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title', $quiz->title) }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الاختبار</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $quiz->description) }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="time_limit" class="form-label">الوقت المحدد (بالدقائق)</label>
                                <input type="number" name="time_limit" id="time_limit" class="form-control" 
                                       value="{{ old('time_limit', $quiz->time_limit) }}" min="1" placeholder="غير محدد">
                                <div class="form-text">اترك فارغاً إذا كنت لا تريد تحديد وقت</div>
                                @error('time_limit')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="passing_score" class="form-label">درجة النجاح (%) <span class="text-danger">*</span></label>
                                <input type="number" name="passing_score" id="passing_score" class="form-control" 
                                       value="{{ old('passing_score', $quiz->passing_score) }}" min="1" max="100" required>
                                @error('passing_score')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">تفعيل الاختبار</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">معلومات الاختبار</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>عدد الأسئلة:</strong> {{ $quiz->questions ? $quiz->questions->count() : 0 }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>إجمالي النقاط:</strong> {{ $quiz->total_points ?? ($quiz->questions ? $quiz->questions->sum('points') : 0) }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>عدد المحاولات:</strong> {{ $quiz->results ? $quiz->results->count() : 0 }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>تاريخ الإنشاء:</strong> {{ $quiz->created_at->format('Y-m-d') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>آخر تحديث:</strong> {{ $quiz->updated_at->format('Y-m-d') }}
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">إدارة الأسئلة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="addQuestions()">
                            <i class="bi bi-plus-circle me-2"></i>إضافة أسئلة
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="editQuestions()">
                            <i class="bi bi-pencil me-2"></i>تعديل الأسئلة
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="viewResults()">
                            <i class="bi bi-graph-up me-2"></i>عرض النتائج
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">معلومات الدورة</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($quiz->course->image)
                            <img src="{{ Storage::url($quiz->course->image) }}" 
                                 class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-book text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1">{{ $quiz->course->title }}</h6>
                            <small class="text-muted">{{ $quiz->course->instructor_name }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>عدد الدروس:</strong> {{ $quiz->course->lessons()->count() }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>عدد الطلاب:</strong> {{ $quiz->course->studentsCount() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function addQuestions() {
        // يمكن إضافة منطق إضافة الأسئلة هنا
        alert('سيتم إضافة ميزة إضافة الأسئلة قريباً');
    }
    
    function editQuestions() {
        // يمكن إضافة منطق تعديل الأسئلة هنا
        alert('سيتم إضافة ميزة تعديل الأسئلة قريباً');
    }
    
    function viewResults() {
        // يمكن إضافة منطق عرض النتائج هنا
        alert('سيتم إضافة ميزة عرض النتائج قريباً');
    }
    
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