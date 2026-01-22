@extends('layouts.admin')

@section('title', 'تعديل طلب الخدمة')

@section('content')
<div class="container">
    <h1 class="mb-4">تعديل طلب الخدمة</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.update', $serviceRequest->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">العنوان</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $serviceRequest->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $serviceRequest->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="requirements" class="form-label">المتطلبات (اختياري)</label>
            <textarea name="requirements" id="requirements" class="form-control" rows="3">{{ old('requirements', $serviceRequest->requirements) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="service_type_id" class="form-label">نوع الخدمة</label>
            <select name="service_type_id" id="service_type_id" class="form-select" required>
                <option value="">اختر نوع الخدمة</option>
                @foreach ($serviceTypes as $type)
                    <option value="{{ $type->id }}" {{ old('service_type_id', $serviceRequest->service_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">حالة الطلب</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending" {{ old('status', $serviceRequest->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="in_progress" {{ old('status', $serviceRequest->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                <option value="completed" {{ old('status', $serviceRequest->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled" {{ old('status', $serviceRequest->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
