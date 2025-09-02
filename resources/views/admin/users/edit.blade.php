@extends('layouts.app')

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
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مدير</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">عودة</a>
    </form>
</div>
@endsection
