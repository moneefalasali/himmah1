@extends('layouts.teacher')

@section('title', 'محاولات الاختبار')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">محاولات: {{ $quiz->title }}</h1>
        <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-secondary">العودة للاختبار</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($results->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الطالب</th>
                            <th>النقاط</th>
                            <th>النسبة</th>
                            <th>تاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->user->name ?? $r->user->email }}</td>
                            <td>{{ $r->earned_points }} / {{ $r->total_points }}</td>
                            <td>{{ number_format($r->percentage, 2) }}%</td>
                            <td>{{ $r->completed_at?->format('Y-m-d H:i') }}</td>
                            <td><a href="{{ route('teacher.results.show', $r) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $results->links() }}
            @else
                <p class="text-muted">لا توجد محاولات بعد.</p>
            @endif
        </div>
    </div>
</div>
@endsection
