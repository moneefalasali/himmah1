@extends('layouts.admin')

@section('page_title', 'لوحة تحكم الإدارة')
@section('page_subtitle', 'نظرة عامة على أداء المنصة والعمليات الحالية')

@section('content')
    @include('layouts.partials.admin_page_header')

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card p-3 text-center">
                <div class="mb-2 text-muted-xs">إجمالي الطلاب</div>
                <div class="h2 mb-0">{{ $totalStudents ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card p-3 text-center">
                <div class="mb-2 text-muted-xs">إجمالي الكورسات</div>
                <div class="h2 mb-0">{{ $totalCourses ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card p-3 text-center">
                <div class="mb-2 text-muted-xs">إجمالي المعلمين</div>
                <div class="h2 mb-0">{{ $totalTeachers ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card p-3 text-center">
                <div class="mb-2 text-muted-xs">إجمالي المبيعات</div>
                <div class="h2 mb-0">{{ $totalSales ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">آخر النشاطات</h5>
            @if(!empty($recentActivities) && $recentActivities->count())
                <ul class="list-unstyled mb-0">
                    @foreach($recentActivities as $activity)
                        <li class="py-2 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-muted">{{ $activity->created_at->diffForHumans() }}</div>
                                <div>{{ $activity->description }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mb-0 text-muted">لا توجد نشاطات حديثة.</p>
            @endif
        </div>
    </div>
@endsection
