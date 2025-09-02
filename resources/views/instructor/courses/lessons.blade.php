@extends('layouts.app')

@section('title', 'إدارة دروس: ' . $course->title)

@section('content')
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة دروس الدورة</h1>
        <div>
            <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للدورة
            </a>
            <a href="{{ route('instructor.courses.quizzes', $course) }}" class="btn btn-outline-warning">
                <i class="bi bi-question-circle me-2"></i>إدارة الاختبارات
            </a>
        </div>
    </div>

    <!-- إدارة الأقسام -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">أقسام الدورة</h4>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                <i class="bi bi-plus me-2"></i>إضافة قسم جديد
            </button>
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
                <p class="text-muted text-center py-4">لا توجد أقسام لهذه الدورة بعد. ابدأ بإضافة قسم جديد.</p>
            @endif
        </div>
    </div>

    <!-- الدروس بدون أقسام -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">دروس بدون قسم</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="bi bi-plus me-2"></i>إضافة درس
            </button>
        </div>
        @if($lessonsWithoutSection->count() > 0)
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
        @else
        <div class="card-body">
            <p class="text-muted text-center py-4">لا توجد دروس بدون قسم بعد. ابدأ بإضافة درس جديد.</p>
        </div>
        @endif
    </div>

    <!-- Modal إضافة قسم جديد -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قسم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.courses.sections.store', $course) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="section_title" class="form-label">عنوان القسم <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="section_title" class="form-control" 
                                   placeholder="مثال: المقدمة، الأساسيات، التطبيق العملي..." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">إضافة القسم</button>
                    </div>
                </form>
            </div>
        </div>
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

    <!-- Modal إضافة درس جديد -->
    <div class="modal fade" id="addLessonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLessonModalTitle">إضافة درس جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('instructor.courses.lessons.store', $course) }}" method="POST" id="addLessonForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="section_id" id="lesson_section_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان الدرس <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الدرس</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">نوع الفيديو <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_input_type" id="video_type_url" value="url" checked>
                                <label class="form-check-label" for="video_type_url">
                                    رابط فيديو خارجي (YouTube, Vimeo, إلخ)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_input_type" id="video_type_vimeo_id" value="vimeo_id">
                                <label class="form-check-label" for="video_type_vimeo_id">
                                    معرف فيديو Vimeo موجود
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="video_input_type" id="video_type_upload" value="upload">
                                <label class="form-check-label" for="video_type_upload">
                                    رفع فيديو جديد إلى Vimeo
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="video_url_container">
                            <label for="video_url" class="form-label">رابط الفيديو <span class="text-danger">*</span></label>
                            <input type="url" name="video_url" id="video_url" class="form-control">
                            <div class="form-text">مثال: https://www.youtube.com/watch?v=... أو https://vimeo.com/...</div>
                        </div>
                        
                        <div class="mb-3" id="vimeo_id_container" style="display: none;">
                            <label for="vimeo_video_id" class="form-label">معرف فيديو Vimeo <span class="text-danger">*</span></label>
                            <input type="text" name="vimeo_video_id" id="vimeo_video_id" class="form-control">
                            <div class="form-text">مثال: 123456789 (الرقم الموجود في رابط Vimeo)</div>
                        </div>
                        
                        <div class="mb-3" id="video_file_container" style="display: none;">
                            <label for="video_file" class="form-label">ملف الفيديو <span class="text-danger">*</span></label>
                            <input type="file" name="video_file" id="video_file" class="form-control" accept="video/*">
                            <div class="form-text">الحد الأقصى: 100 ميجابايت. الصيغ المدعومة: MP4, AVI, MOV, WMV</div>
                            <div class="alert alert-info mt-2" style="display: none;" id="upload_progress">
                                <i class="bi bi-cloud-upload me-2"></i>جاري رفع الفيديو إلى Vimeo... قد يستغرق هذا بعض الوقت.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">مدة الدرس (بالدقائق)</label>
                            <input type="number" name="duration" id="duration" class="form-control" min="1" value="30">
                        </div>

                        @if($course->sections->count() > 0)
                        <div class="mb-3" id="section_select_container">
                            <label for="section_select" class="form-label">القسم</label>
                            <select name="section_id" id="section_select" class="form-select">
                                <option value="">بدون قسم</option>
                                @foreach($course->sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_free" id="is_free" class="form-check-input" value="1">
                                <label for="is_free" class="form-check-label">درس مجاني (للعرض)</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">إضافة الدرس</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Initialize when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Handle video input type changes
        document.querySelectorAll('input[name="video_input_type"]').forEach(function(radio) {
            radio.addEventListener('change', toggleVideoInputs);
        });

        // Initialize with default video type
        toggleVideoInputs();

        // Show upload progress when file is selected
        const videoFileInput = document.getElementById('video_file');
        if (videoFileInput) {
            videoFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    document.getElementById('upload_progress').style.display = 'block';
                } else {
                    document.getElementById('upload_progress').style.display = 'none';
                }
            });
        }

        // Handle form submission for file uploads
        const lessonForm = document.getElementById('addLessonForm');
        if (lessonForm) {
            lessonForm.addEventListener('submit', function(e) {
                const videoType = document.querySelector('input[name="video_input_type"]:checked').value;
                
                if (videoType === 'upload' && document.getElementById('video_file').files.length > 0) {
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>جاري الرفع...';
                    
                    // Re-enable after a delay (in case of error)
                    setTimeout(function() {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }, 30000); // 30 seconds timeout
                }
            });
        }

        // Reset modal when closed
        const addLessonModal = document.getElementById('addLessonModal');
        if (addLessonModal) {
            addLessonModal.addEventListener('hidden.bs.modal', function () {
                document.getElementById('addLessonModalTitle').textContent = 'إضافة درس جديد';
                document.getElementById('lesson_section_id').value = '';
                document.getElementById('addLessonForm').reset();
                
                const sectionSelectContainer = document.getElementById('section_select_container');
                if (sectionSelectContainer) {
                    sectionSelectContainer.style.display = 'block';
                }
                
                // Reset video input type to URL
                document.getElementById('video_type_url').checked = true;
                toggleVideoInputs();
            });
        }
    });

    function editSection(sectionId, sectionTitle) {
        document.getElementById('edit_section_title').value = sectionTitle;
        document.getElementById('editSectionForm').action = 
            '{{ route("admin.courses.sections.update", [$course, ":section"]) }}'.replace(':section', sectionId);
        
        new bootstrap.Modal(document.getElementById('editSectionModal')).show();
    }

    function addLessonToSection(sectionId, sectionTitle) {
        document.getElementById('addLessonModalTitle').textContent = 'إضافة درس إلى: ' + sectionTitle;
        document.getElementById('lesson_section_id').value = sectionId;
        
        // Hide section select since we're adding to a specific section
        const sectionSelectContainer = document.getElementById('section_select_container');
        if (sectionSelectContainer) {
            sectionSelectContainer.style.display = 'none';
        }
        
        new bootstrap.Modal(document.getElementById('addLessonModal')).show();
    }

    function editLesson(lessonId) {
        // يمكن إضافة منطق تعديل الدرس هنا
        alert('سيتم إضافة ميزة تعديل الدرس قريباً');
    }

    function toggleVideoInputs() {
        const videoType = document.querySelector('input[name="video_input_type"]:checked').value;
        
        // Hide all containers
        document.getElementById('video_url_container').style.display = 'none';
        document.getElementById('vimeo_id_container').style.display = 'none';
        document.getElementById('video_file_container').style.display = 'none';
        
        // Clear all inputs
        document.getElementById('video_url').value = '';
        document.getElementById('vimeo_video_id').value = '';
        document.getElementById('video_file').value = '';
        
        // Show relevant container
        switch(videoType) {
            case 'url':
                document.getElementById('video_url_container').style.display = 'block';
                break;
            case 'vimeo_id':
                document.getElementById('vimeo_id_container').style.display = 'block';
                break;
            case 'upload':
                document.getElementById('video_file_container').style.display = 'block';
                break;
        }
    }
</script>
@endsection 