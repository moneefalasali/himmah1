@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">إدارة المشتركين في دورة: {{ $course->title }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الطلاب</h6>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enrollModal">تسجيل طالب جديد</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }} ({{ $student->email }})</td>
                            <td>{{ $student->pivot->subscription_start }}</td>
                            <td>{{ $student->pivot->subscription_end }}</td>
                            <td>
                                <span class="badge badge-{{ $student->pivot->status == 'active' ? 'success' : ($student->pivot->status == 'suspended' ? 'danger' : 'warning') }}">
                                    {{ $student->pivot->status }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.enrollments.update', [$course->id, $student->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    @if($student->pivot->status == 'active')
                                        <input type="hidden" name="status" value="suspended">
                                        <button type="submit" class="btn btn-sm btn-danger">إيقاف (Suspend)</button>
                                    @else
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-sm btn-success">تفعيل (Reactivate)</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Enroll -->
<div class="modal fade" id="enrollModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
                <form action="{{ route('admin.enrollments.store', $course->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل طالب في الدورة</h5>
                </div>
                <div class="modal-body">
                            <div class="form-group">
                                <label>معرف الطالب (User ID) — اختياري</label>
                                <input type="number" name="user_id" class="form-control">
                                <small class="form-text text-muted">أو أدخل بريدًا إلكترونيًا لإنشاء/البحث عن المستخدم.</small>
                            </div>
                            <div class="form-group mt-2">
                                <label>البريد الإلكتروني (اختياري)</label>
                                <input type="email" name="email" class="form-control" placeholder="user@example.com">
                            </div>
                            <div class="form-group mt-2">
                                <label>الاسم (اختياري - يُستخدم عند إنشاء مستخدم جديد)</label>
                                <input type="text" name="name" class="form-control" placeholder="اسم الطالب">
                            </div>
                    <div class="form-group">
                        <label>المدة (بالأشهر)</label>
                        <input type="number" name="duration_months" class="form-control" value="4" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تسجيل</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
