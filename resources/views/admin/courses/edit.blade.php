@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">تعديل الدورة</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>حدث خطأ!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- التصنيف الموحد -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">المرحلة التعليمية <span class="text-danger">*</span></label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">اختر المرحلة</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="type" class="form-label">نوع الدورة <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="recorded" {{ old('type', $course->type) == 'recorded' ? 'selected' : '' }}>مسجل</option>
                                    <option value="online" {{ old('type', $course->type) == 'online' ? 'selected' : '' }}>أونلاين</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="subject_id" class="form-label">المقرر <span class="text-danger">*</span></label>
                                <select class="form-control @error('subject_id') is-invalid @enderror" 
                                        id="subject_id" name="subject_id" required 
                                        data-initial-value="{{ $course->subject_id }}">
                                    <option value="">اختر المقرر</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- معلومات الدورة الأساسية -->
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدورة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $course->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" required>{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label">السعر <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $course->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="instructor_name" class="form-label">اسم المدرب <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('instructor_name') is-invalid @enderror" 
                                       id="instructor_name" name="instructor_name" value="{{ old('instructor_name', $course->instructor_name) }}" required>
                                @error('instructor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="duration" class="form-label">المدة (بالساعات)</label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" name="duration" value="{{ old('duration', $course->duration) }}">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="course_size" class="form-label">حجم الدورة <span class="text-danger">*</span></label>
                                <select class="form-control @error('course_size') is-invalid @enderror" 
                                        id="course_size" name="course_size" required>
                                    <option value="normal" {{ old('course_size', $course->course_size) == 'normal' ? 'selected' : '' }}>عادي</option>
                                    <option value="large" {{ old('course_size', $course->course_size) == 'large' ? 'selected' : '' }}>كبير</option>
                                </select>
                                @error('course_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الدورة</label>
                            @if ($course->image)
                                <div class="mb-2">
                                    <img src="{{ $course->image_url }}" alt="{{ $course->title }}" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">اترك فارغاً للاحتفاظ بالصورة الحالية</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includes_summary" name="includes_summary" {{ old('includes_summary', $course->includes_summary) ? 'checked' : '' }}>
                                <label class="form-check-label" for="includes_summary">
                                    تتضمن ملخص
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includes_tajmeeat" name="includes_tajmeeat" {{ old('includes_tajmeeat', $course->includes_tajmeeat) ? 'checked' : '' }}>
                                <label class="form-check-label" for="includes_tajmeeat">
                                    تتضمن تجميعات
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> رجوع
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/dependent-dropdowns.js') }}"></script>
@endpush
@endsection
