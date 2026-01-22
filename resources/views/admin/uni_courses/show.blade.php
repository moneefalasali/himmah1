@extends('layouts.admin')

@section('title', 'عرض مقرر الجامعة')

@section('content')
<div class="card mt-4">
    <div class="card-header bg-light">
        <h4 class="mb-0">بيانات مقرر الجامعة</h4>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">الجامعة</dt>
            <dd class="col-sm-9">{{ $uniCourse->university->name }}
                @if($uniCourse->university->city)
                    <br><small class="text-muted">{{ $uniCourse->university->city }}</small>
                @endif
            </dd>

            <dt class="col-sm-3">المقرر الأصلي</dt>
            <dd class="col-sm-9">{{ $uniCourse->course->title }}
                <br><small class="text-muted">{{ $uniCourse->course->instructor_name }}</small>
            </dd>

            <dt class="col-sm-3">الاسم المخصص</dt>
            <dd class="col-sm-9">{{ $uniCourse->custom_name ?? 'لا يوجد' }}</dd>

            <dt class="col-sm-3">عدد الدروس</dt>
            <dd class="col-sm-9"><span class="badge bg-info">{{ $uniCourse->lessons->count() }}</span></dd>

            <dt class="col-sm-3">تاريخ الإنشاء</dt>
            <dd class="col-sm-9">{{ $uniCourse->created_at->format('Y-m-d') }}</dd>
        </dl>
        <a href="{{ route('admin.uni_courses.index') }}" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-right"></i> عودة للقائمة
        </a>
    </div>
</div>
@endsection
