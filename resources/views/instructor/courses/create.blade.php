@extends('layouts.app')

@section('title', 'إضافة دورة جديدة')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إضافة دورة جديدة</h1>
        <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>العودة للقائمة
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h4 class="mb-0">معلومات الدورة الأساسية</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="4" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="duration" class="form-label">مدة الدورة (بالدقائق)</label>
                                <input type="number" name="duration" id="duration" class="form-control" 
                                       value="{{ old('duration', 0) }}" min="0">
                                @error('duration')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="course_size" class="form-label">حجم الدورة <span class="text-danger">*</span></label>
                                <select name="course_size" id="course_size" class="form-select" required>
                                    <option value="normal" {{ old('course_size') == 'normal' ? 'selected' : '' }}>عادية</option>
                                    <option value="large" {{ old('course_size') == 'large' ? 'selected' : '' }}>كبيرة</option>
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
                                           value="1" {{ old('includes_summary', true) ? 'checked' : '' }}>
                                    <label for="includes_summary" class="form-check-label">تتضمن ملخص</label>
                                </div>
                                @error('includes_summary')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="includes_tajmeeat" id="includes_tajmeeat" class="form-check-input" 
                                           value="1" {{ old('includes_tajmeeat', true) ? 'checked' : '' }}>
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
                                           value="{{ old('price', 0) }}" min="0" step="0.01" required>
                                    @error('price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">حالة الدورة <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>منشور</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير منشور</option>
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
                                <li>يمكنك إضافة الدروس والاختبارات بعد إنشاء الدورة</li>
                                <li>الدورة ستظهر للطلاب عند نشرها</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>إنشاء الدورة
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