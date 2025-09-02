@extends('layouts.app')

@section('title', 'دفع مقابل الدورة: ' . $course->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h3 class="mb-0">شراء الدورة: {{ $course->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">التفاصيل</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>السعر:</strong> {{ number_format($course->price, 2) }} ر.س</p>
                                    <p><strong>الدورة:</strong> {{ $course->title }}</p>
                                    <p><strong>المدة:</strong> {{ $course->duration }} ساعة</p>
                                    <p><strong>المستوى:</strong> {{ $course->level_label }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h4 class="mb-0">طريقة الدفع</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="mada" value="mada" checked>
                                            <label class="form-check-label" for="mada">
                                                <img src="/payment-methods/mada.png" width="80" alt="مدى">
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="visa" value="visa">
                                            <label class="form-check-label" for="visa">
                                                <img src="/payment-methods/visa.png" width="80" alt="فيزا">
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="apple_pay" value="apple_pay">
                                            <label class="form-check-label" for="apple_pay">
                                                <i class="bi bi-apple me-2"></i> Apple Pay
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('payment.process', $course) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="mada">
                                        
                                        <div class="mb-3">
                                            <label for="card_number" class="form-label">رقم البطاقة</label>
                                            <input type="text" class="form-control" id="card_number" 
                                                   placeholder="XXXX XXXX XXXX XXXX" maxlength="19">
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label for="expiry_date" class="form-label">تاريخ الانتهاء</label>
                                                <input type="text" class="form-control" id="expiry_date" 
                                                       placeholder="MM/YY">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cvv" class="form-label">رقم الأمان (CVV)</label>
                                                <input type="text" class="form-control" id="cvv" 
                                                       placeholder="XXX" maxlength="3">
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success btn-lg w-100">
                                                <i class="bi bi-credit-card me-2"></i> الدفع الآن - {{ number_format($course->price, 2) }} ر.س
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> بعد إتمام الدفع، سيتم توجيهك تلقائياً إلى صفحة الدورة.
                    </div>
                    <p>نستخدم بوابة دفع آمنة متوافقة مع معايير PCI DSS لضمان أمان معلوماتك المالية.</p>
                    <p>للاستفسارات، يرجى التواصل مع الدعم عبر <a href="mailto:support@platform.com">support@platform.com</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // تحسين إدخال رقم البطاقة
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });
        
        // تحسين إدخال تاريخ الانتهاء
        document.getElementById('expiry_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            e.target.value = value;
        });
    </script>
@endsection