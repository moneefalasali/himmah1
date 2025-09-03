@extends('layouts.app')

@section('title', 'الخدمات التعليمية')

@section('content')
    <h2 class="mb-4">اختر نوع الخدمة التي تحتاجها</h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-journal-check h1 text-primary"></i>
                    </div>
                    <h4 class="card-title mb-3">حل واجب</h4>
                    <p class="card-text text-muted mb-4">نقوم بحل واجباتك بدقة عالية مع شرح مفصل.</p>
                    <a href="{{ route('services.create', 'hal-wajib') }}" class="btn btn-primary w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-laptop h1 text-success"></i>
                    </div>
                    <h4 class="card-title mb-3">مشروع تخرج</h4>
                    <p class="card-text text-muted mb-4">إرشاد ومساعدة في مشروع التخرج من البداية حتى النهاية.</p>
                    <a href="{{ route('services.create', 'graduation-project') }}" class="btn btn-success w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-text h1 text-info"></i>
                    </div>
                    <h4 class="card-title mb-3">تدقيق لغوي</h4>
                    <p class="card-text text-muted mb-4">مراجعة أكاديمية وتدقيق لغوي لجميع أنواع المستندات.</p>
                    <a href="{{ route('services.create', 'proofreading') }}" class="btn btn-info w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-bar-chart h1 text-warning"></i>
                    </div>
                    <h4 class="card-title mb-3">تحليل بيانات</h4>
                    <p class="card-text text-muted mb-4">تحليل البيانات باستخدام أدوات إحصائية متطورة.</p>
                    <a href="{{ route('services.create', 'data-analysis') }}" class="btn btn-warning w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-mortarboard h1 text-danger"></i>
                    </div>
                    <h4 class="card-title mb-3">استشارة أكاديمية</h4>
                    <p class="card-text text-muted mb-4">استشارات في اختيار التخصص والمسار الأكاديمي.</p>
                    <a href="{{ route('services.create', 'academic-consulting') }}" class="btn btn-danger w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-question-circle h1 text-secondary"></i>
                    </div>
                    <h4 class="card-title mb-3">استفسار عام</h4>
                    <p class="card-text text-muted mb-4">استفسارات عامة أو طلب خدمة غير مدرجة.</p>
                    <a href="{{ route('services.create', 'general-inquiry') }}" class="btn btn-secondary w-100">
                        طلب الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="mb-0">أسعار الخدمات</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>نوع الخدمة</th>
                                <th>السعر الأساسي</th>
                                <th>المدة المتوقعة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>حل واجب</td>
                                <td>من 50 ر.س</td>
                                <td>24-48 ساعة</td>
                                <td>حسب تعقيد الواجب</td>
                            </tr>
                            <tr>
                                <td>مشروع تخرج</td>
                                <td>من 500 ر.س</td>
                                <td>2-4 أسابيع</td>
                                <td>حسب حجم المشروع</td>
                            </tr>
                            <tr>
                                <td>تدقيق لغوي</td>
                                <td>من 30 ر.س</td>
                                <td>12-24 ساعة</td>
                                <td>لكل 500 كلمة</td>
                            </tr>
                            <tr>
                                <td>تحليل بيانات</td>
                                <td>من 100 ر.س</td>
                                <td>3-7 أيام</td>
                                <td>حسب تعقيد التحليل</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i> 
                    <strong>ملاحظة:</strong> الطلبات العاجلة (تحتاج خلال 24 ساعة) تزيد سعرها بنسبة 20%.
                </div>
            </div>
        </div>
    </div>
@endsection
