@extends('layouts.admin')

@section('title', 'نظرة عامة على الاشتراكات')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>نظرة عامة على اشتراكات الدورات</h2>
    </div>

    <div class="card">
        <div class="card-body">
            @if($courses->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الكورس</th>
                                <th>عدد المشتركين</th>
                                <th>آخر تحديث</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->students_count }}</td>
                                    <td>{{ $course->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-sm btn-outline-primary">إدارة المشتركين</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $courses->links() }}
                </div>
            @else
                <p class="text-muted">لا توجد دورات.</p>
            @endif
        </div>
    </div>
</div>
@endsection
