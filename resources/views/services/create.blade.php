@extends('layouts.app')

@section('title', 'طلب خدمة: ' . $serviceType->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h3 class="mb-0">تقديم طلب خدمة - {{ $serviceType->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('services.store', $serviceType->name) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> 
                                يرجى ملء النموذج بالتفصيل لضمان تقديم خدمة عالية الجودة.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الطلب <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   placeholder="أدخل عنوان مختصر لطلبك..." value="{{ old('title') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">التفاصيل المطلوبة <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="6" 
                                      placeholder="اشرح طلبك بالتفصيل..." required>{{ old('description') }}</textarea>
                            <div class="form-text">كلما كانت التفاصيل أكثر دقة، كانت الخدمة أفضل.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="requirements" class="form-label">المتطلبات الإضافية</label>
                            <textarea name="requirements" id="requirements" class="form-control" rows="3" 
                                      placeholder="أي متطلبات إضافية أو تعليمات خاصة...">{{ old('requirements') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="files" class="form-label">رفع ملفات (اختياري)</label>
                            <input type="file" name="files[]" id="files" class="form-control" 
                                   accept=".pdf,.doc,.docx,.zip,.jpg,.png" multiple>
                            <div class="form-text">الملفات المسموحة: PDF, DOC, ZIP, صور - أقصى حجم 10 ميغابايت لكل ملف</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="urgent" id="urgent" class="form-check-input" 
                                   {{ old('urgent') ? 'checked' : '' }}>
                            <label for="urgent" class="form-check-label">
                                طلب عاجل (يتم تسليمه خلال 24 ساعة) <strong>+20%</strong>
                            </label>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">ملاحظات إضافية</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" name="terms_accepted" id="terms_accepted" 
                                           class="form-check-input" required>
                                    <label for="terms_accepted" class="form-check-label">
                                        أوافق على شروط الخدمة وأقر بأن المعلومات المقدمة صحيحة
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-right me-2"></i> العودة
                            </a>
                            <button type="submit" class="btn btn-success">
                                إرسال الطلب <i class="bi bi-send ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

