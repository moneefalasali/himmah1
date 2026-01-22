@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h3 class="mb-0">تسجيل الدخول</h3>
                    <small class="text-muted">أو <a href="{{ route('register') }}">إنشاء حساب جديد</a></small>
                </div>
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email-address" class="form-label">البريد الإلكتروني</label>
                            <input id="email-address" name="email" type="email" autocomplete="email" required 
                                   class="form-control" placeholder="you@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="form-control" placeholder="كلمة المرور">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input id="remember-me" name="remember" type="checkbox" class="form-check-input">
                                <label for="remember-me" class="form-check-label">تذكرني</label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">دخول</button>
                        </div>

                        <div class="text-center my-3">
                            <span class="text-muted">أو عبر</span>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('login.google') }}" class="btn btn-light border">
                                <img class="me-2" src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" style="height:18px"> تسجيل الدخول عبر جوجل
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
