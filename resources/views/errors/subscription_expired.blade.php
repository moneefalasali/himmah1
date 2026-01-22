@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow border-0 p-5">
        <i class="fas fa-clock text-warning fa-5x mb-4"></i>
        <h2 class="text-dark">انتهى اشتراكك في هذه الدورة</h2>
        <p class="lead text-muted">يرجى الاشتراك في الترّم القادم لمواصلة الوصول إلى المحتوى والدروس.</p>
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">العودة للرئيسية</a>
            <a href="#" class="btn btn-outline-primary">تجديد الاشتراك</a>
        </div>
    </div>
</div>
@endsection
