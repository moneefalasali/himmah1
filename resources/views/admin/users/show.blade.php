{{-- resources/views/admin/users/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">تفاصيل المستخدم</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>معلومات المستخدم</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>الاسم</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>رقم الهاتف</th>
                        <td>{{ $user->phone ?? 'غير متوفر' }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ التسجيل</th>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>آخر تحديث</th>
                        <td>{{ $user->updated_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>الحالة</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> العودة إلى قائمة المستخدمين
    </a>

</div>
@endsection
