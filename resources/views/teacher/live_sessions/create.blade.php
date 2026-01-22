@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">إنشاء حصة أونلاين جديدة</div>
        <div class="card-body">
            <form action="{{ route('teacher.live-sessions.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label>اختر الكورس</label>
                    <select name="course_id" class="form-control" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>عنوان الحصة</label>
                    <input type="text" name="topic" class="form-control" placeholder="مثلاً: مقدمة في البرمجة" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>تاريخ ووقت البداية</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>المدة (بالدقائق)</label>
                            <input type="number" name="duration" class="form-control" value="60" min="15" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">إنشاء الحصة عبر Zoom</button>
            </form>
        </div>
    </div>
</div>
@endsection
