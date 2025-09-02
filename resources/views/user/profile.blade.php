@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">تعديل الملف الشخصي</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   value="{{ auth()->user()->name }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="{{ auth()->user()->email }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">نبذة عني</label>
                            <textarea name="bio" id="bio" class="form-control" rows="3">{{ auth()->user()->bio }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">تحديث الملف</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0">تغيير كلمة المرور</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">تغيير كلمة المرور</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الصورة الشخصية</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ auth()->user()->avatar_url ?? '/default-avatar.png' }}" 
                             alt="الصورة الشخصية" class="rounded-circle" width="150" height="150">
                    </div>
                    <form>
                        <div class="mb-3">
                            <input type="file" class="form-control" name="avatar">
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث الصورة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection