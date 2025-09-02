@extends('layouts.admin')

@section('title', 'إدارة مقررات الجامعات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة مقررات الجامعات</h2>
        <a href="{{ route('admin.uni_courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> إضافة مقرر جامعة
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">قائمة مقررات الجامعات</h5>
        </div>
        
        <div class="card-body">
            @if($uniCourses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الجامعة</th>
                                <th>المقرر الأصلي</th>
                                <th>الاسم المخصص</th>
                                <th>عدد الدروس</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uniCourses as $uniCourse)
                                <tr>
                                    <td>
                                        <strong>{{ $uniCourse->university->name }}</strong>
                                        @if($uniCourse->university->city)
                                            <br><small class="text-muted">{{ $uniCourse->university->city }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $uniCourse->course->title }}
                                        <br><small class="text-muted">{{ $uniCourse->course->instructor_name }}</small>
                                    </td>
                                    <td>
                                        {{ $uniCourse->custom_name ?? 'لا يوجد' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $uniCourse->total_lessons }}</span>
                                    </td>
                                    <td>
                                        {{ $uniCourse->created_at->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.uni_courses.show', $uniCourse) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.uni_courses.lessons', $uniCourse) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <a href="{{ route('admin.uni_courses.edit', $uniCourse) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.uni_courses.destroy', $uniCourse) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المقرر؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $uniCourses->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد مقررات جامعات</h5>
                    <p class="text-muted">ابدأ بإضافة مقرر جامعة جديد</p>
                    <a href="{{ route('admin.uni_courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة مقرر جامعة
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

