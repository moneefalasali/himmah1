@extends('layouts.admin')

@section('title', 'تعديل مقرر الجامعة')

@section('content')
<div class="card mt-4">
    <div class="card-header bg-light">
        <h4 class="mb-0">تعديل مقرر الجامعة</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.uni_courses.update', $uniCourse) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="university_id" class="form-label">الجامعة</label>
                <select name="university_id" id="university_id" class="form-select" required>
                    <option value="">اختر الجامعة</option>
                    @foreach($universities as $university)
                        <option value="{{ $university->id }}" {{ $uniCourse->university_id == $university->id ? 'selected' : '' }}>
                            {{ $university->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="course_id" class="form-label">المقرر الأصلي</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">اختر المقرر</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $uniCourse->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="custom_name" class="form-label">الاسم المخصص (اختياري)</label>
                <input type="text" name="custom_name" id="custom_name" class="form-control" value="{{ old('custom_name', $uniCourse->custom_name) }}" maxlength="255">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i> حفظ التعديلات
            </button>
            <a href="{{ route('admin.uni_courses.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-right"></i> إلغاء
            </a>
        </form>
    </div>
</div>
@endsection
