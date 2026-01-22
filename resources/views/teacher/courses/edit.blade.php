@extends('layouts.app')

@section('title', 'تعديل الدورة: ' . $course->title)

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تعديل الدورة: {{ $course->title }}</h1>
        <div>
            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للدورة
            </a>
            <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list me-2"></i>قائمة الدورات
            </a>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h4 class="mb-0">تعديل معلومات الدورة</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('teacher.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">المرحلة التعليمية <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">اختر المرحلة</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type" class="form-label">نوع الدورة <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="recorded" {{ old('type', $course->type) == 'recorded' ? 'selected' : '' }}>مسجل</option>
                                    <option value="online" {{ old('type', $course->type) == 'online' ? 'selected' : '' }}>أونلاين</option>
                                </select>
                                @error('type')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label for="subject_id" class="form-label">المقرر</label>
                                <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
                                    <option value="">اختر المقرر (أو اكتب اسمًا أدناه)</option>
                                    @foreach($subjects ?? [] as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subject_id', $course->subject_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror

                                <small class="text-muted d-block mt-2">أو اكتب اسم مقرر جديد هنا:</small>
                                <input type="text" name="subject_name" id="subject_name" class="form-control mt-1 @error('subject_name') is-invalid @enderror" value="{{ old('subject_name', '') }}">
                                @error('subject_name')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="instructor_name" class="form-label">اسم المدرب <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('instructor_name') is-invalid @enderror" id="instructor_name" name="instructor_name" value="{{ old('instructor_name', $course->instructor_name ?? auth()->user()->name ?? '') }}" required>
                                @error('instructor_name')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="university_id" class="form-label">الجامعة (اختياري)</label>
                                <select class="form-select @error('university_id') is-invalid @enderror" id="university_id" name="university_id">
                                    <option value="">بدون جامعة</option>
                                    @foreach($universities ?? [] as $uni)
                                        <option value="{{ $uni->id }}" {{ old('university_id', $course->university_id) == $uni->id ? 'selected' : '' }}>{{ $uni->name }}</option>
                                    @endforeach
                                </select>
                                @error('university_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $course->title) }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="4" 
                                      required>{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="duration" class="form-label">مدة الدورة (بالدقائق)</label>
                                <input type="number" name="duration" id="duration" class="form-control" 
                                       value="{{ old('duration', $course->duration) }}" min="0">
                                @error('duration')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="course_size" class="form-label">حجم الدورة <span class="text-danger">*</span></label>
                                <select name="course_size" id="course_size" class="form-select" required>
                                    <option value="normal" {{ old('course_size', $course->course_size) == 'normal' ? 'selected' : '' }}>عادية</option>
                                    <option value="large" {{ old('course_size', $course->course_size) == 'large' ? 'selected' : '' }}>كبيرة</option>
                                </select>
                                @error('course_size')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="includes_summary" id="includes_summary" class="form-check-input" 
                                           value="1" {{ old('includes_summary', $course->includes_summary) ? 'checked' : '' }}>
                                    <label for="includes_summary" class="form-check-label">تتضمن ملخص</label>
                                </div>
                                @error('includes_summary')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="includes_tajmeeat" id="includes_tajmeeat" class="form-check-input" 
                                           value="1" {{ old('includes_tajmeeat', $course->includes_tajmeeat) ? 'checked' : '' }}>
                                    <label for="includes_tajmeeat" class="form-check-label">تتضمن تجميع</label>
                                </div>
                                @error('includes_tajmeeat')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">الصور</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="image" class="form-label">صورة الدورة</label>
                                    @if($course->image)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($course->image) }}" class="img-fluid rounded" 
                                                 style="max-height: 150px;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" id="image" class="form-control" 
                                           accept="image/*">
                                    <div class="form-text">الصيغ المسموحة: JPG, PNG - الحد الأقصى: 2 ميجابايت</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">التفاصيل المالية</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="price" class="form-label">السعر (ر.س) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" class="form-control" 
                                           value="{{ old('price', $course->price) }}" min="0" step="0.01" required>
                                    @error('price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">حالة الدورة <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>منشور</option>
                                        <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>غير منشور</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>معلومات مهمة:</strong>
                            <ul class="mb-0 mt-2">
                                <li>ستحصل على 40% من أرباح كل دورة</li>
                                <li>يمكنك تعديل الدروس والاختبارات من صفحات إدارتها</li>
                                <li>تغيير حالة الدورة سيؤثر على ظهورها للطلاب</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">
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
@endsection

@section('scripts')
<script>
    // تحقق من صحة البيانات قبل الإرسال
    document.querySelector('form').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const price = parseFloat(document.getElementById('price').value);
        
        if (title.length < 5) {
            alert('يجب أن يكون عنوان الدورة على الأقل 5 أحرف');
            e.preventDefault();
            return false;
        }
        
        if (description.length < 20) {
            alert('يجب أن يكون وصف الدورة على الأقل 20 حرفاً');
            e.preventDefault();
            return false;
        }
        
        if (price < 0) {
            alert('يجب أن يكون السعر أكبر من أو يساوي صفر');
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection 