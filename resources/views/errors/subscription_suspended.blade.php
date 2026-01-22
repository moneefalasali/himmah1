@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow border-0 p-5">
        <i class="fas fa-exclamation-triangle text-danger fa-5x mb-4"></i>
        <h2 class="text-dark">تم إيقاف وصولك لهذه الدورة</h2>
        <p class="lead text-muted">لقد تم إيقاف اشتراكك من قبل الإدارة. يرجى التواصل مع الدعم الفني للمزيد من المعلومات.</p>
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">العودة للرئيسية</a>
        </div>
    </div>
</div>
@endsection
