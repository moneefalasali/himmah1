@extends('layouts.teacher')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الاختبارات (Quizzes)</h1>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إنشاء اختبار جديد
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>الدورة</th>
                            <th>عدد الأسئلة</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizzes as $quiz)
                        <tr>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->course->title }}</td>
                            <td>{{ $quiz->questions_count ?? $quiz->questions()->count() }}</td>
                            <td>
                                <span class="badge badge-{{ $quiz->status == 'published' ? 'success' : 'warning' }}">
                                    {{ $quiz->status == 'published' ? 'منشور' : 'مسودة' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-info">تعديل</a>
                                <a href="{{ route('teacher.quizzes.questions.index', $quiz) }}" class="btn btn-sm btn-primary">أسئلة</a>
                                <a href="{{ route('teacher.quizzes.results.index', ['quiz' => $quiz->id]) }}" class="btn btn-sm btn-secondary">النتائج</a>
                                <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
