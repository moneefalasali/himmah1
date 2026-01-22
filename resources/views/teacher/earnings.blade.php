@extends('layouts.app')

@section('title', 'تقارير الأرباح')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تقارير الأرباح</h1>
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للوحة التحكم
            </a>
            
        </div>
    </div>

    <!-- ملخص الأرباح -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-cash-coin text-primary fs-1"></i>
                    </div>
                    <h3 class="text-primary mb-1">{{ number_format($totalRevenue, 2) }} ر.س</h3>
                    <p class="text-muted mb-0">إجمالي الإيرادات</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-graph-up text-success fs-1"></i>
                    </div>
                    <h3 class="text-success mb-1">{{ number_format($totalInstructorProfit, 2) }} ر.س</h3>
                    <p class="text-muted mb-0">أرباحك (40%)</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-percent text-info fs-1"></i>
                    </div>
                    <h3 class="text-info mb-1">40%</h3>
                    <p class="text-muted mb-0">نسبة الأرباح</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-calendar-check text-warning fs-1"></i>
                    </div>
                    <h3 class="text-warning mb-1">{{ $monthlyEarnings->count() }}</h3>
                    <p class="text-muted mb-0">شهور نشطة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الأرباح الشهرية -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-primary"></i>
                        الأرباح الشهرية
                    </h5>
                </div>
                <div class="card-body">
                    @if($monthlyEarnings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الشهر</th>
                                        <th>إجمالي الإيرادات</th>
                                        <th>أرباحك (40%)</th>
                                        <th>نسبة النمو</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyEarnings as $index => $earning)
                                        @php
                                            $previousMonth = $monthlyEarnings->get($index + 1);
                                            $growthRate = $previousMonth && $previousMonth->total > 0 
                                                ? (($earning->total - $previousMonth->total) / $previousMonth->total) * 100 
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $earning->month)->format('F Y') }}</strong>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ number_format($earning->total, 2) }} ر.س</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ number_format($earning->instructor_profit, 2) }} ر.س</span>
                                            </td>
                                            <td>
                                                @if($growthRate > 0)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-arrow-up me-1"></i>{{ number_format($growthRate, 1) }}%
                                                    </span>
                                                @elseif($growthRate < 0)
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-arrow-down me-1"></i>{{ number_format(abs($growthRate), 1) }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">0%</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-graph text-muted fs-1"></i>
                            <p class="text-muted mt-2">لا توجد بيانات أرباح بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2 text-info"></i>
                        توزيع الأرباح
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <svg width="200" height="200" viewBox="0 0 36 36" class="d-block mx-auto">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                      fill="none" stroke="#e9ecef" stroke-width="2"/>
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                      fill="none" stroke="#28a745" stroke-width="2" 
                                      stroke-dasharray="40, 100" stroke-dashoffset="25"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h4 class="mb-0 text-success">40%</h4>
                                <small class="text-muted">أرباحك</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-success mb-1">{{ number_format($totalInstructorProfit, 2) }} ر.س</h5>
                                <small class="text-muted">أرباحك</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted mb-1">{{ number_format($totalRevenue - $totalInstructorProfit, 2) }} ر.س</h5>
                            <small class="text-muted">رسوم المنصة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الأرباح حسب الدورة -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">
                <i class="bi bi-book me-2 text-warning"></i>
                الأرباح حسب الدورة
            </h5>
        </div>
        <div class="card-body">
            @if($courseEarnings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الدورة</th>
                                <th>عدد المبيعات</th>
                                <th>إجمالي الإيرادات</th>
                                <th>أرباحك (40%)</th>
                                <th>متوسط السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseEarnings as $earning)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($earning->course->image)
                                                <img src="{{ Storage::url($earning->course->image) }}" 
                                                     class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-book text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $earning->course->title }}</h6>
                                                <small class="text-muted">{{ $earning->course->instructor_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $earning->sales_count }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ number_format($earning->total, 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($earning->instructor_profit, 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($earning->total / $earning->sales_count, 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.courses.show', $earning->course) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>عرض
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-book text-muted fs-1"></i>
                    <p class="text-muted mt-2">لا توجد بيانات أرباح للدورات بعد</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 