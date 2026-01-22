@extends('layouts.app')

@section('content')
<div class="container">
    <h1>الطلاب</h1>

    @if($students->isEmpty())
        <p>لا توجد سجلات طلابية حتى الآن.</p>
    @else
        <ul>
            @foreach($students as $student)
                <li>{{ $student->name ?? 'Unnamed' }} ({{ $student->email ?? '' }})</li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'قائمة الطلاب')

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">قائمة الطلاب</h1>
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للوحة التحكم
            </a>
            <a href="{{ route('teacher.earnings.index') }}" class="btn btn-success">
                <i class="bi bi-graph-up me-2"></i>تقارير الأرباح
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-primary fs-1"></i>
                    <h4 class="mt-2">{{ $students->count() }}</h4>
                    <p class="text-muted mb-0">إجمالي الطلاب</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-book text-success fs-1"></i>
                    <h4 class="mt-2">{{ $students->sum('courses.count') }}</h4>
                    <p class="text-muted mb-0">إجمالي التسجيلات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin text-info fs-1"></i>
                    <h4 class="mt-2">{{ number_format($students->sum('total_spent'), 2) }} ر.س</h4>
                    <p class="text-muted mb-0">إجمالي الإنفاق</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up text-warning fs-1"></i>
                    <h4 class="mt-2">{{ number_format($students->sum('instructor_profit'), 2) }} ر.س</h4>
                    <p class="text-muted mb-0">أرباحك (40%)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الطلاب -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">
                <i class="bi bi-people me-2 text-primary"></i>
                قائمة الطلاب
            </h5>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الطالب</th>
                                <th>الدورات المسجل فيها</th>
                                <th>إجمالي الإنفاق</th>
                                <th>أرباحك (40%)</th>
                                <th>تاريخ آخر تسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $student['user']->name }}</h6>
                                                <small class="text-muted">{{ $student['user']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($student['courses'] as $course)
                                                <span class="badge bg-info">{{ $course->title }}</span>
                                            @endforeach
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            {{ $student['courses']->count() }} دورة
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ number_format($student['total_spent'], 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($student['instructor_profit'], 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        @php
                                            $latestPurchase = $student['courses']->sortByDesc('created_at')->first();
                                        @endphp
                                        @if($latestPurchase)
                                            <small class="text-muted">{{ $latestPurchase->created_at->format('Y-m-d') }}</small>
                                        @else
                                            <small class="text-muted">غير محدد</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#studentDetailsModal{{ $student['user']->id }}">
                                                <i class="bi bi-eye me-1"></i>تفاصيل
                                            </button>
                                            <a href="mailto:{{ $student['user']->email }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-envelope me-1"></i>رسالة
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal تفاصيل الطالب -->
                                <div class="modal fade" id="studentDetailsModal{{ $student['user']->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تفاصيل الطالب: {{ $student['user']->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>الاسم:</strong> {{ $student['user']->name }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>البريد الإلكتروني:</strong> {{ $student['user']->email }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>رقم الهاتف:</strong> {{ $student['user']->phone ?? 'غير محدد' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>تاريخ التسجيل:</strong> {{ $student['user']->created_at->format('Y-m-d') }}
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <h6>الدورات المسجل فيها:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>الدورة</th>
                                                                <th>السعر</th>
                                                                <th>تاريخ التسجيل</th>
                                                                <th>الحالة</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($student['courses'] as $course)
                                                                <tr>
                                                                    <td>{{ $course->title }}</td>
                                                                    <td>{{ number_format($course->price, 2) }} ر.س</td>
                                                                    <td>{{ $course->created_at->format('Y-m-d') }}</td>
                                                                    <td>
                                                                        <span class="badge bg-success">مكتمل</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light">
                                                            <div class="card-body text-center">
                                                                <h5 class="text-primary">{{ number_format($student['total_spent'], 2) }} ر.س</h5>
                                                                <small class="text-muted">إجمالي الإنفاق</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light">
                                                            <div class="card-body text-center">
                                                                <h5 class="text-success">{{ number_format($student['instructor_profit'], 2) }} ر.س</h5>
                                                                <small class="text-muted">أرباحك (40%)</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                <a href="mailto:{{ $student['user']->email }}" class="btn btn-primary">
                                                    <i class="bi bi-envelope me-2"></i>إرسال رسالة
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted fs-1"></i>
                    <h4 class="mt-3 text-muted">لا يوجد طلاب مسجلين بعد</h4>
                    <p class="text-muted">عندما يبدأ الطلاب في التسجيل في دوراتك، ستظهر هنا</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 