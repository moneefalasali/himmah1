@extends('layouts.admin')

@section('page_title', 'إدارة المستخدمين')
@section('page_actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> إضافة مستخدم جديد
    </a>
@endsection

@section('content')
    @include('layouts.partials.admin_page_header')
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-start border-primary border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">إجمالي المستخدمين</h6>
                            <h3 class="mb-0">{{ number_format($totalUsers) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-success border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">المستخدمين النشطين</h6>
<h3 class="mb-0">{{ number_format($recentUsers) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-warning border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">المستخدمين الجدد (هذا الشهر)</h6>
<h3 class="mb-0">{{ number_format($recentUsers) }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-start border-info border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">نسبة الاحتفاظ</h6>
                            <h3 class="mb-0">{{ number_format($retentionRate, 1) }}%</h3>
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
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="roleFilter" 
                            data-bs-toggle="dropdown">
                        جميع الأدوار
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">الطلاب</a></li>
                        <li><a class="dropdown-item" href="#">المعلمين</a></li>
                        <li><a class="dropdown-item" href="#">المدراء</a></li>
                    </ul>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusFilter" 
                            data-bs-toggle="dropdown">
                        جميع الحالات
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">نشط</a></li>
                        <li><a class="dropdown-item" href="#">موقوف</a></li>
                        <li><a class="dropdown-item" href="#">معلق</a></li>
                    </ul>
                </div>
            </div>
            
            <form class="d-flex" action="{{ route('admin.users.index') }}" method="GET">
                <input type="text" name="search" class="form-control me-2" placeholder="ابحث عن مستخدم..." 
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
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>تاريخ الانضمام</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" width="30" height="30" 
                                                 class="rounded-circle me-2" style="object-fit: cover;">
                                        @endif
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($user->is_admin) bg-danger
                                        @elseif($user->is_instructor) bg-info
                                        @else bg-primary @endif">
                                        {{ $user->role_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($user->is_active) bg-success
                                        @else bg-secondary @endif">
                                        {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.destroy', $user) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-info-circle text-muted me-2"></i> لا توجد مستخدمين حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">التنزيلات</h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('admin.users.export') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-2"></i> تصدير بيانات المستخدمين (CSV)
                </a>
                <a href="{{ route('admin.users.export', ['format' => 'xlsx']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download me-2"></i> تصدير بيانات المستخدمين (Excel)
                </a>
{{-- <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-info">
    <i class="bi bi-file-earmark-pdf me-2"></i> توليد تقرير PDF
</a> --}}
            </div>
        </div>
    </div>
@endsection