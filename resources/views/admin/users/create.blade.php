{{-- resources/views/admin/users/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'إنشاء مستخدم جديد')

@section('content')
<div class="container mt-4">
    <h2>إنشاء مستخدم جديد</h2>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">رقم الهاتف</label>
            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="role" class="form-label">الصلاحية</label>
            <select id="role" name="role" class="form-select" required>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>مستخدم</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>معلم</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مدير</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="course_ids" class="form-label">تسجيل المستخدم في دورات (اختياري)</label>
            <select id="course_ids" name="course_ids[]" class="form-select" multiple>
                @foreach(App\Models\Course::all() as $course)
                    <option value="{{ $course->id }}">{{ $course->title }} - {{ $course->university?->name ?? 'عام' }}</option>
                @endforeach
            </select>
            <div class="form-text">اختر دورات لوضع المستخدم كمُسجل بها (سيتم إنشاء مشتريات إدارية).</div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">إنشاء</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">عودة</a>
    </form>
</div>
@endsection
