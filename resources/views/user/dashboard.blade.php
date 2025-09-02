@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">مرحباً، {{ auth()->user()->name }}!</h2>
            <p class="lead">هذا هو مركز تحكمك. من هنا يمكنك إدارة دوراتك، طلباتك، وملفك الشخصي.</p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person fs-1 mb-3"></i>
                    <h5 class="card-title">الملف الشخصي</h5>
                    <a href="{{ route('profile') }}" class="btn btn-light mt-auto">تعديل الملف</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-journal fs-1 mb-3"></i>
                    <h5 class="card-title">دوراتي</h5>
                    <a href="{{ route('my-courses') }}" class="btn btn-light mt-auto">عرض الدورات</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-tools fs-1 mb-3"></i>
                    <h5 class="card-title">طلباتي</h5>
                    <a href="{{ route('my-service-requests') }}" class="btn btn-light mt-auto">عرض الطلبات</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4 g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">آخر الدورات</h5>
                </div>
                <div class="card-body">
                    <p>قائمة بالدورات التي بدأتها مؤخراً.</p>
                    <div class="alert alert-info">لا توجد دورات مسجلة بعد.</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الإشعارات</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            لديك دورة جديدة جاهزة للبدء
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            تم قبول طلب خدمة جديد
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            تذكير: لديك درس غير مكتمل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection