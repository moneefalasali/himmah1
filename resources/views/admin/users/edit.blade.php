@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">تعديل مستخدم</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control" 
                value="{{ old('name', $user->name) }}" 
                required
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-control" 
                value="{{ old('email', $user->email) }}" 
                required
            >
        </div>


        <div class="mb-3">
            <label for="role" class="form-label">الصلاحية</label>
            <select id="role" name="role" class="form-select" required>
                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>مستخدم</option>
                <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>معلم</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مدير</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="course_ids" class="form-label">الدورات المسجلة للمستخدم</label>
            <select id="course_ids" name="course_ids[]" class="form-select" multiple>
                @foreach(App\Models\Course::all() as $course)
                    <option value="{{ $course->id }}" {{ $user->purchases()->where('course_id', $course->id)->where('payment_status', 'completed')->exists() ? 'selected' : '' }}>
                        {{ $course->title }} - {{ $course->university?->name ?? 'عام' }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">حدد الدورات التي يجب أن تكون هذه المستخدم مُسجلاً بها. سيُضاف إدخال شراء إداري إن لم يكن موجوداً.</div>
        </div>

        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">عودة</a>
    </form>
</div>
@endsection
