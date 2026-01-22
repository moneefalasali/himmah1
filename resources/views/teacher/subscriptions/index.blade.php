@extends('layouts.admin')

@section('title', 'إدارة الاشتراكات')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة الاشتراكات - طلاب دوراتك</h2>
    </div>

    @foreach($courses as $course)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $course->title }}</strong>
                <span class="text-muted">{{ $course->students->count() }} مشترك</span>
            </div>
            <div class="card-body">
                @if($course->students->count())
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>بريد إلكتروني</th>
                                    <th>حالة الاشتراك</th>
                                    <th>بداية الاشتراك</th>
                                    <th>نهاية الاشتراك</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->pivot->status ?? '—' }}</td>
                                        <td>{{ $student->pivot->subscription_start ? $student->pivot->subscription_start->format('Y-m-d') : '—' }}</td>
                                        <td>{{ $student->pivot->subscription_end ? $student->pivot->subscription_end->format('Y-m-d') : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">لا يوجد طلاب مشتركين في هذه الدورة.</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
