@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        إعادة تعيين كلمة المرور
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="أدخل كلمة المرور الجديدة">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="أعد إدخال كلمة المرور">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>
                                حفظ كلمة المرور
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">
                        العودة لتسجيل الدخول
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
