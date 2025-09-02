@extends('layouts.admin')

@section('title', 'تفاصيل الجامعة')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $university->name }}</h2>
        <div class="btn-group">
            <a href="{{ route('admin.universities.edit', $university) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i> تعديل
            </a>
            <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> العودة للقائمة
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات الجامعة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>الاسم:</strong></td>
                            <td>{{ $university->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>المدينة:</strong></td>
                            <td>{{ $university->city ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td><strong>عدد الطلاب:</strong></td>
                            <td>{{ $university->users->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>عدد المقررات:</strong></td>
                            <td>{{ $university->uniCourses->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $university->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">المقررات المتاحة</h5>
                </div>
                <div class="card-body">
                    @if($university->uniCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>اسم المقرر</th>
                                        <th>الاسم المخصص</th>
                                        <th>عدد الدروس</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($university->uniCourses as $uniCourse)
                                        <tr>
                                            <td>{{ $uniCourse->course->title }}</td>
                                            <td>{{ $uniCourse->custom_name ?? 'لا يوجد' }}</td>
                                            <td>{{ $uniCourse->total_lessons }}</td>
                                            <td>
                                                <a href="{{ route('admin.uni_courses.show', $uniCourse) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.uni_courses.lessons', $uniCourse) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-list"></i> الدروس
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد مقررات</h5>
                            <p class="text-muted">لم يتم إضافة أي مقررات لهذه الجامعة بعد</p>
                            <a href="{{ route('admin.uni_courses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> إضافة مقرر
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

