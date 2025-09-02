@extends('layouts.app')

@section('title', 'دوراتي')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>دوراتي المسجلة</h2>
        <a href="{{ route('courses.index') }}" class="btn btn-primary">تصفح المزيد من الدورات</a>
    </div>
    
    @if($purchases->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> لم تقم بالتسجيل في أي دورة بعد.
        </div>
    @else
        <div class="row g-4">
            @foreach($purchases as $purchase)
                <div class="col-md-4">
                    <div class="card h-100">
                        @if($purchase->course->image)
                            <img src="{{ Storage::url($purchase->course->image) }}" 
                                 class="card-img-top" alt="{{ $purchase->course->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $purchase->course->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($purchase->course->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-success">تم الشراء</span>
                                <a href="{{ route('courses.curriculum', $purchase->course) }}" class="btn btn-sm btn-primary">
                                    متابعة الدراسة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection