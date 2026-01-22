@extends('layouts.admin')

@section('title', 'إدارة الدورات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة الدورات</h2>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> إضافة دورة جديدة
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoryFilter" 
                            data-bs-toggle="dropdown">
                        جميع الفئات
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">البرمجة</a></li>
                        <li><a class="dropdown-item" href="#">التصميم</a></li>
                        <li><a class="dropdown-item" href="#">التسويق</a></li>
                        <li><a class="dropdown-item" href="#">الذكاء الاصطناعي</a></li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="levelFilter" 
                            data-bs-toggle="dropdown">
                        جميع المستويات
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">مبتدئ</a></li>
                        <li><a class="dropdown-item" href="#">متوسط</a></li>
                        <li><a class="dropdown-item" href="#">متقدم</a></li>
                    </ul>
                </div>
            </div>
            
            <form class="d-flex" action="{{ route('admin.courses.index') }}" method="GET">
                <input type="text" name="search" class="form-control me-2" placeholder="ابحث عن دورة..." 
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>الفئة</th>
                            <th>المعلم</th>
                            <th>السعر</th>
                            <th>الطلاب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($course->thumbnail_url)
                                            <img src="{{ $course->thumbnail_url }}" width="50" height="50" 
                                                 class="rounded me-2" style="object-fit: cover;">
                                        @endif
                                        <span>{{ $course->title }}</span>
                                    </div>
                                </td>
                                <td>برمجة</td>
                                <td>{{ $course->instructor_name }}</td>
                                <td>{{ number_format($course->price, 2) }} ر.س</td>
                                <td>{{ $course->purchases_count }}</td>
                                <td>
                                    <span class="badge 
                                        @if($course->status === 'active') bg-success
                                        @else bg-secondary @endif">
                                        {{ $course->status === 'active' ? 'منشور' : 'غير منشور' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.courses.edit', $course) }}" 
                                           class="btn btn-sm btn-outline-primary" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.courses.lessons', $course) }}" 
                                           class="btn btn-sm btn-outline-info" title="المناهج">
                                            <i class="bi bi-journal-text"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.courses.destroy', $course) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذه الدورة؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-info-circle text-muted me-2"></i> لا توجد دورات حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
@endsection