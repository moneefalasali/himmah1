@extends('layouts.app')

@section('title', 'إدارة الاختبارات: ' . $course->title)

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة الاختبارات: {{ $course->title }}</h1>
        <div>
            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للدورة
            </a>
            <a href="{{ route('teacher.quizzes.create', $course) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>إضافة اختبار جديد
            </a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle text-primary fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->count() }}</h4>
                    <p class="text-muted mb-0">إجمالي الاختبارات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->where('is_active', true)->count() }}</h4>
                    <p class="text-muted mb-0">اختبارات نشطة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->sum('attempts_count') }}</h4>
                    <p class="text-muted mb-0">إجمالي المحاولات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up text-warning fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->sum('questions_count') }}</h4>
                    <p class="text-muted mb-0">إجمالي الأسئلة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الاختبارات -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">قائمة الاختبارات</h5>
        </div>
        <div class="card-body">
            @if($quizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الاختبار</th>
                                <th>الحالة</th>
                                <th>عدد الأسئلة</th>
                                <th>الوقت المحدد</th>
                                <th>درجة النجاح</th>
                                <th>عدد المحاولات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $quiz->title }}</h6>
                                            <small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $quiz->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $quiz->questions_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($quiz->time_limit)
                                            <span class="text-muted">{{ $quiz->time_limit }} دقيقة</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $quiz->passing_score }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $quiz->attempts_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('teacher.quizzes.edit', [$course, $quiz]) }}" 
                                               class="btn btn-sm btn-outline-primary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    title="إضافة أسئلة" onclick="addQuestions({{ $quiz->id }})">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    title="عرض النتائج" onclick="showResults({{ $quiz->id }})">
                                                <i class="bi bi-graph-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    title="{{ $quiz->is_active ? 'إيقاف' : 'تفعيل' }}"
                                                    onclick="toggleQuiz({{ $quiz->id }}, {{ $quiz->is_active ? 'false' : 'true' }})">
                                                <i class="bi bi-{{ $quiz->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-question-circle text-muted fs-1"></i>
                    <h4 class="mt-3 text-muted">لا توجد اختبارات بعد</h4>
                    <p class="text-muted">ابدأ بإنشاء اختبار جديد لقياس مستوى الطلاب</p>
                    <a href="{{ route('teacher.quizzes.create', $course) }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>إنشاء اختبار جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal إضافة أسئلة -->
<div class="modal fade" id="addQuestionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة أسئلة للاختبار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="questionsContainer">
                    <div class="question-item border rounded p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">نوع السؤال</label>
                                <select class="form-select question-type" onchange="toggleQuestionOptions(this)">
                                    <option value="multiple_choice">اختيار متعدد</option>
                                    <option value="true_false">صح أو خطأ</option>
                                    <option value="fill_blank">ملء الفراغ</option>
                                    <option value="essay">مقالي</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">نص السؤال <span class="text-danger">*</span></label>
                                <textarea class="form-control question-text" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">النقاط</label>
                                <input type="number" class="form-control question-points" value="1" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الترتيب</label>
                                <input type="number" class="form-control question-order" value="1" min="1">
                            </div>
                            <div class="col-12 multiple-choice-options" style="display: block;">
                                <label class="form-label">الخيارات</label>
                                <div class="options-container">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control option-text" placeholder="الخيار الأول">
                                        <div class="input-group-text">
                                            <input type="checkbox" class="form-check-input correct-answer">
                                        </div>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control option-text" placeholder="الخيار الثاني">
                                        <div class="input-group-text">
                                            <input type="checkbox" class="form-check-input correct-answer">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addOption()">
                                    <i class="bi bi-plus me-1"></i>إضافة خيار
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">شرح الإجابة (اختياري)</label>
                                <textarea class="form-control question-explanation" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeQuestion(this)">
                            <i class="bi bi-trash me-1"></i>حذف السؤال
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary" onclick="addQuestion()">
                    <i class="bi bi-plus-circle me-2"></i>إضافة سؤال آخر
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveQuestions()">
                    <i class="bi bi-save me-2"></i>حفظ الأسئلة
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentQuizId = null;

    function addQuestions(quizId) {
        currentQuizId = quizId;
        new bootstrap.Modal(document.getElementById('addQuestionsModal')).show();
    }

    function showResults(quizId) {
        // يمكن إضافة منطق عرض النتائج هنا
        alert('سيتم إضافة ميزة عرض النتائج قريباً');
    }

    function toggleQuiz(quizId, isActive) {
        const action = isActive ? 'تفعيل' : 'إيقاف';
        if (confirm(`هل أنت متأكد من ${action} هذا الاختبار؟`)) {
            // يمكن إضافة منطق التفعيل/الإيقاف هنا
            alert(`تم ${action} الاختبار بنجاح`);
        }
    }

    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        const questionItem = container.querySelector('.question-item').cloneNode(true);
        
        // إعادة تعيين القيم
        questionItem.querySelectorAll('input, textarea, select').forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else if (input.type === 'number') {
                input.value = container.querySelectorAll('.question-item').length + 1;
            } else {
                input.value = '';
            }
        });
        
        container.appendChild(questionItem);
    }

    function removeQuestion(button) {
        const questionItems = document.querySelectorAll('.question-item');
        if (questionItems.length > 1) {
            button.closest('.question-item').remove();
        }
    }

    function addOption() {
        const optionsContainer = event.target.previousElementSibling;
        const newOption = document.createElement('div');
        newOption.className = 'input-group mb-2';
        newOption.innerHTML = `
            <input type="text" class="form-control option-text" placeholder="خيار جديد">
            <div class="input-group-text">
                <input type="checkbox" class="form-check-input correct-answer">
            </div>
        `;
        optionsContainer.appendChild(newOption);
    }

    function toggleQuestionOptions(select) {
        const questionItem = select.closest('.question-item');
        const optionsContainer = questionItem.querySelector('.multiple-choice-options');
        
        if (select.value === 'multiple_choice') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    }

    function saveQuestions() {
        // يمكن إضافة منطق حفظ الأسئلة هنا
        alert('تم حفظ الأسئلة بنجاح!');
        bootstrap.Modal.getInstance(document.getElementById('addQuestionsModal')).hide();
    }
</script>
@endsection 