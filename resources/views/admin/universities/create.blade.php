@extends('layouts.admin')

@section('title', 'إضافة جامعة جديدة')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إضافة جامعة جديدة</h2>
        <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> العودة للقائمة
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">بيانات الجامعة</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.universities.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الجامعة <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required
                                   placeholder="مثال: جامعة الملك سعود">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city" class="form-label">المدينة</label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
                                   placeholder="مثال: الرياض">
                            @error('city')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> حفظ الجامعة
                    </button>
                    <a href="{{ route('admin.universities.index') }}" class="btn btn-secondary">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

