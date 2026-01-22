@extends('layouts.app')

@section('title', 'عرض الدورة: ' . $course->title)

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">عرض الدورة: {{ $course->title }}</h1>
        <div>
            <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للقائمة
            </a>
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil me-2"></i>تعديل الدورة
            </a>
            <a href="{{ route('teacher.courses.ai.show', $course) }}" class="btn btn-secondary me-2">
                <i class="bi bi-robot me-2"></i>المساعد الذكي
            </a>
            <a href="{{ route('teacher.courses.lessons', $course) }}" class="btn btn-info">
                <i class="bi bi-play-circle me-2"></i>إدارة الدروس
            </a>
        </div>
    </div>

    <!-- معلومات الدورة -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h4 class="mb-0">معلومات الدورة</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">عنوان الدورة</h6>
                            <p class="mb-3">{{ $course->title }}</p>
                            
                            <h6 class="text-muted">الوصف</h6>
                            <p class="mb-3">{{ $course->description }}</p>
                            
                            <h6 class="text-muted">المدرب</h6>
                            <p class="mb-3">{{ $course->instructor_name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted">السعر</h6>
                            <p class="mb-3">
                                <span class="badge bg-success fs-6">{{ number_format($course->price, 2) }} ر.س</span>
                            </p>
                            
                            <h6 class="text-muted">حالة الدورة</h6>
                            <p class="mb-3">
                                <span class="badge {{ $course->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $course->status === 'active' ? 'منشور' : 'غير منشور' }}
                                </span>
                            </p>
                            
                            <h6 class="text-muted">حجم الدورة</h6>
                            <p class="mb-3">
                                <span class="badge bg-info">{{ $course->course_size === 'large' ? 'كبيرة' : 'عادية' }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">عدد الدروس</h6>
                                <h4 class="text-primary">{{ $course->lessons()->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">عدد الأقسام</h6>
                                <h4 class="text-info">{{ $course->sections()->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">عدد الاختبارات</h6>
                                <h4 class="text-warning">{{ $course->quizzes()->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">عدد الطلاب</h6>
                                <h4 class="text-success">{{ $stats['total_students'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">صورة الدورة</h5>
                </div>
                <div class="card-body text-center">
                    @if($course->image)
                        <img src="{{ Storage::url($course->image) }}" class="img-fluid rounded" 
                             style="max-height: 200px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="bi bi-book text-muted fs-1"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>المدة:</strong> 
                        @if($course->duration)
                            {{ $course->duration }} دقيقة
                        @else
                            غير محدد
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>يتضمن ملخص:</strong> 
                        <span class="badge {{ $course->includes_summary ? 'bg-success' : 'bg-secondary' }}">
                            {{ $course->includes_summary ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>يتضمن تجميع:</strong> 
                        <span class="badge {{ $course->includes_tajmeeat ? 'bg-success' : 'bg-secondary' }}">
                            {{ $course->includes_tajmeeat ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>تاريخ الإنشاء:</strong> {{ $course->created_at->format('Y-m-d') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>آخر تحديث:</strong> {{ $course->updated_at->format('Y-m-d') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الدورة -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-people text-primary fs-1"></i>
                    </div>
                    <h3 class="text-primary mb-1">{{ $stats['total_students'] }}</h3>
                    <p class="text-muted mb-0">إجمالي الطلاب</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-cash-coin text-success fs-1"></i>
                    </div>
                    <h3 class="text-success mb-1">{{ number_format($stats['total_revenue'], 2) }} ر.س</h3>
                    <p class="text-muted mb-0">إجمالي الإيرادات</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-graph-up text-info fs-1"></i>
                    </div>
                    <h3 class="text-info mb-1">{{ number_format($stats['instructor_profit'], 2) }} ر.س</h3>
                    <p class="text-muted mb-0">أرباحك (40%)</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-star text-warning fs-1"></i>
                    </div>
                    <h3 class="text-warning mb-1">{{ number_format($stats['average_rating'], 1) }}</h3>
                    <p class="text-muted mb-0">متوسط التقييم</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الأقسام والدروس -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h4 class="mb-0">المناهج الدراسية</h4>
            <a href="{{ route('teacher.courses.lessons', $course) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>إدارة الدروس
            </a>
        </div>
        <div class="card-body">
            @if($course->sections->count() > 0)
                <div class="accordion" id="sectionsAccordion">
                    @foreach($course->sections as $section)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $section->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $section->id }}" aria-expanded="false">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <span><strong>{{ $section->title }}</strong> ({{ $section->lessons->count() }} درس)</span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editSection({{ $section->id }}, '{{ $section->title }}')" 
                                                    title="تعديل القسم">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="addLessonToSection({{ $section->id }}, '{{ $section->title }}')" 
                                                    title="إضافة درس">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $section->id }}" class="accordion-collapse collapse" 
                                 data-bs-parent="#sectionsAccordion">
                                <div class="accordion-body">
                                    @if($section->lessons->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>الترتيب</th>
                                                        <th>العنوان</th>
                                                        <th>المدة</th>
                                                        <th>النوع</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($section->lessons as $lesson)
                                                        <tr>
                                                            <td>{{ $lesson->order }}</td>
                                                            <td>{{ $lesson->title }}</td>
                                                            <td>{{ $lesson->duration }} دقيقة</td>
                                                            <td>
                                                                <span class="badge {{ $lesson->is_free ? 'bg-success' : 'bg-warning' }}">
                                                                    {{ $lesson->is_free ? 'مجاني' : 'مدفوع' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            onclick="editLesson({{ $lesson->id }})" title="تعديل">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-3">لا توجد دروس في هذا القسم بعد.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-journal-text text-muted fs-1"></i>
                    <h5 class="mt-3 text-muted">لا توجد أقسام لهذه الدورة بعد</h5>
                    <p class="text-muted">ابدأ بإضافة أقسام ودروس لتنظيم المحتوى التعليمي</p>
                    <a href="{{ route('teacher.courses.lessons', $course) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>إضافة قسم ودروس
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- الدروس بدون أقسام -->
    @if($lessonsWithoutSection->count() > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">دروس بدون قسم</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>العنوان</th>
                            <th>المدة</th>
                            <th>النوع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lessonsWithoutSection as $lesson)
                            <tr>
                                <td>{{ $lesson->order }}</td>
                                <td>{{ $lesson->title }}</td>
                                <td>{{ $lesson->duration }} دقيقة</td>
                                <td>
                                    <span class="badge {{ $lesson->is_free ? 'bg-success' : 'bg-warning' }}">
                                        {{ $lesson->is_free ? 'مجاني' : 'مدفوع' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editLesson({{ $lesson->id }})" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal تعديل قسم -->
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل القسم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_section_title" class="form-label">عنوان القسم <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_section_title" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editSection(sectionId, sectionTitle) {
        document.getElementById('edit_section_title').value = sectionTitle;
        document.getElementById('editSectionForm').action = 
            '{{ route("admin.courses.sections.update", [$course, ":section"]) }}'.replace(':section', sectionId);
        
        new bootstrap.Modal(document.getElementById('editSectionModal')).show();
    }

    function addLessonToSection(sectionId, sectionTitle) {
        // يمكن إضافة منطق إضافة درس للقسم هنا
        alert('سيتم إضافة ميزة إضافة درس للقسم قريباً');
    }

    function editLesson(lessonId) {
        // يمكن إضافة منطق تعديل الدرس هنا
        alert('سيتم إضافة ميزة تعديل الدرس قريباً');
    }
</script>
@endsection 