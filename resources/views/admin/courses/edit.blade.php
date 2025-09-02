@extends('layouts.app')

@section('title', 'تعديل الدورة: ' . $course->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تعديل الدورة: {{ $course->title }}</h2>
        <div>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i> العودة إلى القائمة
            </a>
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i> عرض الدورة
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="mb-0">تعديل معلومات الدورة</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
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
                                <label for="instructor_name" class="form-label">اسم المدرب <span class="text-danger">*</span></label>
                                <input type="text" name="instructor_name" id="instructor_name" class="form-control" 
                                       value="{{ old('instructor_name', $course->instructor_name) }}" required>
                                @error('instructor_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="duration" class="form-label">مدة الدورة (بالدقائق)</label>
                                <input type="number" name="duration" id="duration" class="form-control" 
                                       value="{{ old('duration', $course->duration) }}" min="0">
                                @error('duration')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label for="course_size" class="form-label">حجم الدورة <span class="text-danger">*</span></label>
                                <select name="course_size" id="course_size" class="form-select" required>
                                    <option value="normal" {{ old('course_size', $course->course_size) == 'normal' ? 'selected' : '' }}>عادية</option>
                                    <option value="large" {{ old('course_size', $course->course_size) == 'large' ? 'selected' : '' }}>كبيرة</option>
                                </select>
                                @error('course_size')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="includes_summary" id="includes_summary" class="form-check-input" 
                                           value="1" {{ old('includes_summary', $course->includes_summary) ? 'checked' : '' }}>
                                    <label for="includes_summary" class="form-check-label">تتضمن ملخص</label>
                                </div>
                                @error('includes_summary')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="includes_tajmeeat" id="includes_tajmeeat" class="form-check-input" 
                                           value="1" {{ old('includes_tajmeeat', $course->includes_tajmeeat) ? 'checked' : '' }}">
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
                                <h5 class="mb-0">الصور والفيديو</h5>
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
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">المناهج الدراسية</h4>
            <a href="{{ route('admin.courses.lessons', $course) }}" class="btn btn-outline-info">
                <i class="bi bi-journal-text me-2"></i> إدارة المناهج
            </a>
        </div>
        <div class="card-body">
            @if($course->sections->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> هذه الدورة لا تحتوي على مناهج دراسية بعد.
                </div>
            @else
                <div class="accordion" id="sectionsAccordion">
                    @foreach($course->sections as $section)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $section->id }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $section->id }}" aria-expanded="true" 
                                        aria-controls="collapse-{{ $section->id }}">
                                    {{ $section->title }} 
                                    <span class="ms-2 text-muted">({{ $section->lessons_count }} درس)</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $section->id }}" class="accordion-collapse collapse 
                                {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $section->id }}" 
                                 data-bs-parent="#sectionsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        @foreach($section->lessons as $lesson)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-play-circle me-2"></i> {{ $lesson->title }}
                                                    <small class="d-block text-muted ms-4">{{ $lesson->duration }} دقيقة</small>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.lessons.edit', $lesson) }}" 
                                                       class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.lessons.destroy', $lesson) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection