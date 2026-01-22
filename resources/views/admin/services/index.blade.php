@extends('layouts.admin')

@section('title', 'طلبات الخدمات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>طلبات الخدمات التعليمية</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.services.export') }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i> تصدير البيانات
            </a>
            <a href="{{ route('admin.service-types.index') }}" class="btn btn-outline-info">
                <i class="bi bi-gear me-2"></i> إعدادات الخدمات
            </a>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-start border-primary border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">إجمالي الطلبات</h6>
                            <h3 class="mb-0">{{ number_format($totalRequests) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-success border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">الطلبات المكتملة</h6>
                            <h3 class="mb-0">{{ number_format($completedRequests) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-warning border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">الطلبات العاجلة</h6>
                            <h3 class="mb-0">{{ number_format($urgentRequests) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-info border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">إجمالي الإيرادات</h6>
                            <h3 class="mb-0">{{ number_format($totalRevenue, 2) }} ر.س</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeFilter" 
                            data-bs-toggle="dropdown">
                        جميع الأنواع
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">حل واجب</a></li>
                        <li><a class="dropdown-item" href="#">مشروع تخرج</a></li>
                        <li><a class="dropdown-item" href="#">تدقيق لغوي</a></li>
                        <li><a class="dropdown-item" href="#">تحليل بيانات</a></li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilter" 
                            data-bs-toggle="dropdown">
                        جميع الحالات
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">مكتمل</a></li>
                        <li><a class="dropdown-item" href="#">قيد التنفيذ</a></li>
                        <li><a class="dropdown-item" href="#">معلق</a></li>
                        <li><a class="dropdown-item" href="#">ملغى</a></li>
                    </ul>
                </div>
            </div>
            
            <form class="d-flex" action="{{ route('admin.services.index') }}" method="GET">
                <input type="text" name="search" class="form-control me-2" placeholder="ابحث عن طلب..." 
                       value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>العميل</th>
                            <th>النوع</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($request->user->avatar_url)
                                            <img src="{{ $request->user->avatar_url }}" width="30" height="30" 
                                                 class="rounded-circle me-2" style="object-fit: cover;">
                                        @endif
                                        <span>{{ $request->user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->type_badge }}">
                                        {{ $request->type_label }}
                                    </span>
                                </td>
                                <td>{{ number_format($request->total_price, 2) }} ر.س</td>
                                <td>
                                    <span class="badge 
                                        @if($request->status == 'completed') bg-success
                                        @elseif($request->status == 'in_progress') bg-info
                                        @elseif($request->status == 'pending') bg-warning
                                        @elseif($request->status == 'canceled') bg-danger
                                        @else bg-secondary @endif">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.services.show', $request) }}" 
                                           class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.services.edit', $request) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.services.destroy', $request) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-info-circle text-muted me-2"></i> لا توجد طلبات حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $serviceRequests->links() }}
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">الإحصائيات</h5>
            <a href="{{ route('admin.services.statistics') }}" class="btn btn-sm btn-outline-info">
                <i class="bi bi-graph-up me-2"></i> عرض الإحصائيات التفصيلية
            </a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="mb-3">توزيع الطلبات حسب النوع</h6>
                    <canvas id="requestsByTypeChart" height="200"></canvas>
                </div>
                
                <div class="col-md-6">
                    <h6 class="mb-3">توزيع الطلبات حسب الحالة</h6>
                    <canvas id="requestsByStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // توزيع الطلبات حسب النوع
        const typeCtx = document.getElementById('requestsByTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: {{ json_encode($requestsByType['labels']) }},
                datasets: [{
                    data: {{ json_encode($requestsByType['data']) }},
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 206, 86)',
                        'rgb(255, 99, 132)',
                        'rgb(153, 102, 255)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // توزيع الطلبات حسب الحالة
        const statusCtx = document.getElementById('requestsByStatusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {{ json_encode($requestsByStatus['labels']) }},
                datasets: [{
                    data: {{ json_encode($requestsByStatus['data']) }},
                    backgroundColor: [
                        'rgb(40, 167, 69)',
                        'rgb(23, 162, 184)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection