@extends('layouts.admin')

@section('title', 'إدارة الجامعات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة الجامعات</h2>
        <a href="{{ route('admin.universities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> إضافة جامعة جديدة
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">قائمة الجامعات</h5>
        </div>
        
        <div class="card-body">
            @if($universities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>المدينة</th>
                                <th>عدد الطلاب</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($universities as $university)
                                <tr>
                                    <td>
                                        <strong>{{ $university->name }}</strong>
                                    </td>
                                    <td>
                                        {{ $university->city ?? 'غير محدد' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $university->users_count }}</span>
                                    </td>
                                    <td>
                                        {{ $university->created_at->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.universities.show', $university) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.universities.edit', $university) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.universities.destroy', $university) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الجامعة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $universities->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-university fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد جامعات</h5>
                    <p class="text-muted">ابدأ بإضافة جامعة جديدة</p>
                    <a href="{{ route('admin.universities.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> إضافة جامعة
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

