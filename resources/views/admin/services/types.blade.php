@extends('layouts.admin')

@section('title', 'إدارة أنواع الخدمات')

@section('content')
<div class="container mt-4">
    <h2>أنواع الخدمات</h2>

    <!-- رسالة النجاح -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- رسالة الخطأ -->
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- نموذج إضافة نوع خدمة جديد -->
    <form action="{{ route('admin.service-types.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="اسم نوع الخدمة" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="description" class="form-control" placeholder="وصف نوع الخدمة (اختياري)" value="{{ old('description') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">إضافة</button>
            </div>
        </div>
    </form>

    <!-- جدول أنواع الخدمات -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الوصف</th>
                <th>عدد الطلبات</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($serviceTypes as $type)
            <tr>
                <td>{{ $type->name }}</td>
                <td>{{ $type->description }}</td>
                <td>{{ $type->service_requests_count }}</td>
                <td>
                    <!-- زر تعديل -->
                    <a href="{{ route('admin.service-types.edit', $type->id) }}" class="btn btn-sm btn-warning">تعديل</a>

                    <!-- زر حذف -->
                    <form action="{{ route('admin.service-types.destroy', $type->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
