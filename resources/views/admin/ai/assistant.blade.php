@extends('layouts.app')

@section('title', 'مساعد الكورس — ' . $course->title)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>مساعد ذكي للكورس: {{ $course->title }}</h2>
        <div>
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-secondary">العودة لتعديل الكورس</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">نص/محتوى الدرس</h5>
                    <p class="text-muted small">ألصق نص الدرس أو وصف الدرس هنا لتلخيصه أو لإنشاء أسئلة اختبارات.</p>
                    <textarea id="ai_content" class="form-control" rows="10" placeholder="ألصق هنا نص الدرس أو النقاط الرئيسية..."></textarea>
                    <div class="mt-3 d-flex gap-2">
                        <button id="btnSummarize" class="btn btn-primary">تلخيص</button>
                        <button id="btnGenerateQuestions" class="btn btn-outline-primary">إنشاء أسئلة (MCQ)</button>
                        <input id="questionCount" type="number" min="1" max="20" value="5" class="form-control" style="width:110px;">
                    </div>
                    <div id="ai_alert" class="mt-3"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">النتيجة</h5>
                    <div id="ai_result" class="small text-break" style="white-space:pre-wrap;">لم يتم إجراء أي عملية بعد.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">تعليمات الاستخدام</h5>
                    <ul>
                        <li>ألصق نص الدرس في الحقل ثم اضغط "تلخيص" للحصول على ملخص منظم.</li>
                        <li>لاستحداث أسئلة، اضغط "إنشاء أسئلة" (سيُرجع JSON منظم يمكن نسخه أو معاينته).</li>
                        <li>يُسجل استخدام واجهة الذكاء الاصطناعي لأغراض تتبع التكاليف في السجل.</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">التاريخ والقيود</h5>
                    <p class="small text-muted">الطلبات قد تستغرق عدة ثوانٍ. يتم تحديد القيود حسب إعدادات API وموازنات الاستخدام.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const showAlert = (type, message) => {
        document.getElementById('ai_alert').innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    };

    document.getElementById('btnSummarize').addEventListener('click', function() {
        const content = document.getElementById('ai_content').value.trim();
        if (!content) { showAlert('warning', 'الرجاء إضافة نص للملخص.'); return; }
        showAlert('info', 'جاري الاتصال بخدمة الذكاء الاصطناعي...');
        fetch('{{ route('admin.courses.ai.summarize', $course) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ content })
        }).then(r => r.json()).then(data => {
            if (data.ok) {
                document.getElementById('ai_result').textContent = data.result;
                showAlert('success', 'تم الحصول على الملخص.');
            } else {
                showAlert('danger', data.error || 'حدث خطأ');
            }
        }).catch(err => { showAlert('danger', 'فشل الطلب إلى الخادم.'); console.error(err); });
    });

    document.getElementById('btnGenerateQuestions').addEventListener('click', function() {
        const content = document.getElementById('ai_content').value.trim();
        const count = parseInt(document.getElementById('questionCount').value) || 5;
        if (!content) { showAlert('warning', 'الرجاء إضافة نص لإنشاء الأسئلة.'); return; }
        showAlert('info', 'جاري إنشاء الأسئلة...');
        fetch('{{ route('admin.courses.ai.generate_questions', $course) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ content, count })
        }).then(r => r.json()).then(data => {
            if (data.ok) {
                // try to pretty-print JSON if possible
                try {
                    const parsed = JSON.parse(data.result);
                    document.getElementById('ai_result').textContent = JSON.stringify(parsed, null, 2);
                } catch (e) {
                    document.getElementById('ai_result').textContent = data.result;
                }
                showAlert('success', 'تم إنشاء الأسئلة بنجاح.');
            } else {
                showAlert('danger', data.error || 'حدث خطأ');
            }
        }).catch(err => { showAlert('danger', 'فشل الطلب إلى الخادم.'); console.error(err); });
    });
</script>
@endpush
