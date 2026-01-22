@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">إدارة المقررات</h4>
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> إضافة مقرر جديد
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($subjects->isEmpty())
                        <div class="alert alert-info">
                            لا توجد مقررات حالياً. <a href="{{ route('admin.subjects.create') }}">إضافة مقرر جديد</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم المقرر</th>
                                        <th>المرحلة التعليمية</th>
                                        <th>عدد الدورات</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subjects as $subject)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $subject->name }}</strong></td>
                                            <td>{{ $subject->category->name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $subject->courses_count ?? 0 }}</span>
                                            </td>
                                            <td>{{ $subject->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                                <form action="{{ route('admin.subjects.destroy', $subject) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا المقرر؟')">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $subjects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
