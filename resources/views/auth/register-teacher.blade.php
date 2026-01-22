@extends('layouts.app')

@section('title', 'إنشاء حساب معلم - منصة همة التعليمية')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        إنشاء حساب معلم
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register.teacher') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="أدخل اسمك الكامل">
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="أدخل بريدك الإلكتروني">
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف (اختياري)</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="university_id" class="form-label">الجامعة (اختياري)</label>
                            <select class="form-control @error('university_id') is-invalid @enderror" id="university_id" name="university_id">
                                <option value="">اختر الجامعة</option>
                                @foreach($universities as $university)
                                    <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>{{ $university->name }}</option>
                                @endforeach
                            </select>
                            @error('university_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="أدخل كلمة مرور قوية">
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="أعد إدخال كلمة المرور">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">إنشاء حساب المعلم</button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-primary fw-bold">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
