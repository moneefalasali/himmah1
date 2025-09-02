@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - منصة همة التعليمية')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        إنشاء حساب جديد
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="أدخل اسمك الكامل">
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="أدخل بريدك الإلكتروني">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف (اختياري)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       autocomplete="tel"
                                       placeholder="05xxxxxxxx">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="university_id" class="form-label">الجامعة (اختياري)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-university"></i>
                                </span>
                                <select class="form-control @error('university_id') is-invalid @enderror" 
                                        id="university_id" 
                                        name="university_id">
                                    <option value="">اختر الجامعة</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>
                                            {{ $university->name }}
                                            @if($university->city)
                                                - {{ $university->city }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('university_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="أدخل كلمة مرور قوية">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="toggleIcon1"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                يجب أن تحتوي كلمة المرور على 6 أحرف على الأقل
                            </small>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="أعد إدخال كلمة المرور">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="toggleIcon2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                أوافق على <a href="#" class="text-primary">الشروط والأحكام</a> و <a href="#" class="text-primary">سياسة الخصوصية</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                إنشاء الحساب
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center">
                    <p class="mb-0">
                        لديك حساب بالفعل؟ 
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">
                            تسجيل الدخول
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = inputId === 'password' ? document.getElementById('toggleIcon1') : document.getElementById('toggleIcon2');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (password.length === 0) {
        return;
    }
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const strengthTexts = ['ضعيف جداً', 'ضعيف', 'متوسط', 'قوي', 'قوي جداً'];
    const strengthColors = ['danger', 'warning', 'info', 'success', 'success'];
    
    if (!strengthIndicator) {
        const indicator = document.createElement('div');
        indicator.id = 'passwordStrength';
        indicator.className = 'mt-1';
        this.parentNode.parentNode.appendChild(indicator);
    }
    
    document.getElementById('passwordStrength').innerHTML = 
        `<small class="text-${strengthColors[strength - 1]}">قوة كلمة المرور: ${strengthTexts[strength - 1]}</small>`;
});
</script>
@endpush
@endsection

