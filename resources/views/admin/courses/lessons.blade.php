@extends('layouts.admin')

@section('title', 'إدارة دروس: ' . $course->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة دروس الدورة</h2>
        <div>
            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>العودة للدورة
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
                                            <form action="{{ route('admin.courses.sections.destroy', [$course, $section]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="حذف القسم"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم نقل الدروس إلى قائمة الدروس بدون قسم.')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
                                                                        <a href="{{ route('lessons.show', $lesson) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="معاينة">
                                                                            <i class="bi bi-eye"></i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                                onclick="editLesson({{ $lesson->id }})" title="تعديل">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                    <form action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" 
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                                title="حذف"
                                                                                onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
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
                                        <form action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

    <!-- Modal إضافة درس جديد -->
    <div class="modal fade" id="addLessonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLessonModalTitle">إضافة درس جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.courses.lessons.store', $course) }}" method="POST" id="addLessonForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="section_id" id="lesson_section_id">
                    <div class="mb-3">
                        <label for="lesson_section_select" class="form-label">اختر القسم (اختياري)</label>
                        <select id="lesson_section_select" class="form-select">
                            <option value="">- بدون قسم -</option>
                            @foreach($course->sections as $section)
                                <option value="{{ $section->id }}">{{ $section->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="video_path" id="video_path_input">
                    
                    <div class="modal-body">
                        <div id="addLessonAlert"></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">عنوان الدرس <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="duration" class="form-label">المدة (بالدقائق)</label>
                                <input type="number" name="duration" id="duration" class="form-control" value="0">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الدرس</label>
                            <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white py-2">إعدادات الفيديو</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">مصدر الفيديو</label>
                                    <select name="video_platform" id="video_platform" class="form-select" onchange="toggleVideoSource()">
                                        <option value="wasabi">Wasabi (رفع مباشر - HLS)</option>
                                        <option value="vimeo">Vimeo (رابط/ID)</option>
                                        <option value="google_drive">Google Drive (رابط خاص)</option>
                                    </select>
                                </div>

                                <!-- Wasabi Upload -->
                                <div id="source_wasabi" class="video-source-fields">
                                    <label class="form-label">اختر ملف الفيديو</label>
                                    <input type="file" name="video_file" id="video_file" class="form-control" accept="video/*">
                                    <div id="upload_progress_container" class="mt-3" style="display:none;">
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div id="upload_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small id="upload_status" class="text-muted">جاري التحضير للرفع...</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vimeo Input -->
                                <div id="source_vimeo" class="video-source-fields" style="display:none;">
                                    <label class="form-label">Vimeo Video ID أو الرابط</label>
                                    <input type="text" name="vimeo_video_id" class="form-control" placeholder="مثال: 123456789">
                                </div>

                                <!-- Google Drive Input -->
                                <div id="source_google_drive" class="video-source-fields" style="display:none;">
                                    <label class="form-label">رابط Google Drive (Private)</label>
                                    <input type="text" name="google_drive_url" id="google_drive_url" class="form-control" placeholder="ضع رابط الملف هنا">
                                    <small class="text-muted">سيتم استخراج الـ ID تلقائياً وتوفير حماية للملف.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_free" id="is_free">
                            <label class="form-check-label" for="is_free">درس مجاني (معاينة)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" id="submitLessonBtn" class="btn btn-primary">حفظ الدرس</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/wasabi-upload.js') }}"></script>
    <script>
        function toggleVideoSource() {
            const platform = document.getElementById('video_platform').value;
            document.querySelectorAll('.video-source-fields').forEach(el => el.style.display = 'none');
            document.getElementById('source_' + platform).style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const videoFileInput = document.getElementById('video_file');
            const videoPathInput = document.getElementById('video_path_input');
            const submitBtn = document.getElementById('submitLessonBtn');
            const progressContainer = document.getElementById('upload_progress_container');
            const progressBar = document.getElementById('upload_progress_bar');
            const statusText = document.getElementById('upload_status');

            if (videoFileInput) {
                videoFileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    const uploader = new WasabiUploader({
                        file: file,
                        onProgress: (percent) => {
                            progressContainer.style.display = 'block';
                            progressBar.style.width = percent + '%';
                            progressBar.textContent = percent + '%';
                            statusText.textContent = `جاري الرفع المباشر: ${percent}%`;
                            submitBtn.disabled = true;
                        },
                        onSuccess: (data) => {
                            videoPathInput.value = data.key;
                            statusText.textContent = 'اكتمل الرفع بنجاح!';
                            progressBar.classList.remove('progress-bar-animated');
                            progressBar.classList.add('bg-success');
                            submitBtn.disabled = false;
                        },
                        onError: (err) => {
                            statusText.textContent = 'فشل الرفع: ' + err.message;
                            progressBar.classList.add('bg-danger');
                            submitBtn.disabled = false;
                        }
                    });

                    uploader.start();
                });
            }

            // Handle Google Drive ID extraction
            const driveUrlInput = document.getElementById('google_drive_url');
            if (driveUrlInput) {
                driveUrlInput.addEventListener('blur', function() {
                    const url = this.value;
                    const match = url.match(/[-\w]{25,}/);
                    if (match) {
                        videoPathInput.value = match[0];
                    }
                });
            }
        });

        function editSection(id, title) {
            document.getElementById('edit_section_title').value = title;
            document.getElementById('editSectionForm').action = `/admin/courses/{{ $course->id }}/sections/${id}`;
            new bootstrap.Modal(document.getElementById('editSectionModal')).show();
        }

        function addLessonToSection(id, title) {
            document.getElementById('lesson_section_id').value = id;
            const sel = document.getElementById('lesson_section_select');
            if (sel) sel.value = id;
            document.getElementById('addLessonModalTitle').textContent = 'إضافة درس إلى: ' + title;
            new bootstrap.Modal(document.getElementById('addLessonModal')).show();
        }

        function editLesson(id) {
            // Logic for editing lesson can be added here
            alert('سيتم إضافة ميزة تعديل الدرس قريباً');
        }
    </script>
@endpush
