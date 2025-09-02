@extends('layouts.admin')

@section('title', 'إضافة مقرر جامعة')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إضافة مقرر جامعة جديد</h2>
        <a href="{{ route('admin.uni_courses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> العودة للقائمة
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">بيانات مقرر الجامعة</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.uni_courses.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="university_id" class="form-label">الجامعة <span class="text-danger">*</span></label>
                            <select class="form-control @error('university_id') is-invalid @enderror" 
                                    id="university_id" 
                                    name="university_id" 
                                    required>
                                <option value="">اختر الجامعة</option>
                                @foreach($universities as $university)
                                    <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>
                                        {{ $university->name }}
                                        @if($university->city)
                                            - {{ $university->city }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('university_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">المقرر <span class="text-danger">*</span></label>
                            <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" 
                                    name="course_id" 
                                    required>
                                <option value="">اختر المقرر</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} - {{ $course->instructor_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="custom_name" class="form-label">الاسم المخصص (اختياري)</label>
                    <input type="text" 
                           class="form-control @error('custom_name') is-invalid @enderror" 
                           id="custom_name" 
                           name="custom_name" 
                           value="{{ old('custom_name') }}"
                           placeholder="اسم مخصص للمقرر في هذه الجامعة">
                    <small class="form-text text-muted">
                        إذا تم تركه فارغاً، سيتم استخدام اسم المقرر الأصلي
                    </small>
                    @error('custom_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>ملاحظة:</strong> سيتم تلقائياً إضافة جميع دروس المقرر الأصلي بنفس الترتيب. يمكنك تعديل الترتيب لاحقاً من صفحة إدارة الدروس.
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> حفظ مقرر الجامعة
                    </button>
                    <a href="{{ route('admin.uni_courses.index') }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

