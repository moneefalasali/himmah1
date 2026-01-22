<?php $__env->startSection('title', 'إدارة دروس: ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة دروس الدورة</h1>
        <div>
            <a href="<?php echo e(route('teacher.courses.show', $course)); ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-right me-2"></i>العودة للدورة
            </a>
            <a href="<?php echo e(route('teacher.quizzes.index', ['course' => $course->id])); ?>" class="btn btn-outline-warning">
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
            <?php if($course->sections->count() > 0): ?>
                <div class="accordion" id="sectionsAccordion">
                    <?php $__currentLoopData = $course->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo e($section->id); ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo e($section->id); ?>" aria-expanded="false">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <span><strong><?php echo e($section->title); ?></strong> (<?php echo e($section->lessons->count()); ?> درس)</span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editSection(<?php echo e($section->id); ?>, '<?php echo e($section->title); ?>')" 
                                                    title="تعديل القسم">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="addLessonToSection(<?php echo e($section->id); ?>, '<?php echo e($section->title); ?>')" 
                                                    title="إضافة درس">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo e($section->id); ?>" class="accordion-collapse collapse" 
                                 data-bs-parent="#sectionsAccordion">
                                <div class="accordion-body">
                                    <?php if($section->lessons->count() > 0): ?>
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
                                                    <?php $__currentLoopData = $section->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($lesson->order); ?></td>
                                                            <td><?php echo e($lesson->title); ?></td>
                                                            <td><?php echo e($lesson->duration); ?> دقيقة</td>
                                                            <td>
                                                                <span class="badge <?php echo e($lesson->is_free ? 'bg-success' : 'bg-warning'); ?>">
                                                                    <?php echo e($lesson->is_free ? 'مجاني' : 'مدفوع'); ?>

                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            onclick="editLesson(<?php echo e($lesson->id); ?>)" title="تعديل">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center py-3">لا توجد دروس في هذا القسم بعد.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">لا توجد أقسام لهذه الدورة بعد. ابدأ بإضافة قسم جديد.</p>
            <?php endif; ?>
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
        <?php if($lessonsWithoutSection->count() > 0): ?>
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
                        <?php $__currentLoopData = $lessonsWithoutSection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($lesson->order); ?></td>
                                <td><?php echo e($lesson->title); ?></td>
                                <td><?php echo e($lesson->duration); ?> دقيقة</td>
                                <td>
                                    <span class="badge <?php echo e($lesson->is_free ? 'bg-success' : 'bg-warning'); ?>">
                                        <?php echo e($lesson->is_free ? 'مجاني' : 'مدفوع'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editLesson(<?php echo e($lesson->id); ?>)" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="card-body">
            <p class="text-muted text-center py-4">لا توجد دروس بدون قسم بعد. ابدأ بإضافة درس جديد.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal إضافة قسم جديد -->
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قسم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('teacher.courses.sections.store', $course)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
                <form action="<?php echo e(route('teacher.lessons.store', $course)); ?>" method="POST" id="addLessonForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="section_id" id="lesson_section_id">
                    <input type="hidden" name="video_path" id="video_path_input">
                    <div class="mb-3">
                        <label for="lesson_section_select" class="form-label">اختر القسم (اختياري)</label>
                        <select id="lesson_section_select" class="form-select">
                            <option value="">- بدون قسم -</option>
                            <?php $__currentLoopData = $course->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($section->id); ?>"><?php echo e($section->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
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
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/wasabi-upload.js')); ?>"></script>
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
                            // prevent the original file input from being submitted to the server
                            try {
                                videoFileInput.removeAttribute('name');
                                videoFileInput.value = '';
                                videoFileInput.disabled = true;
                            } catch (e) {}
                        },
                        onError: (err) => {
                            statusText.textContent = 'فشل الرفع: ' + err.message;
                            progressBar.classList.add('bg-danger');
                            submitBtn.disabled = false;
                                try { videoFileInput.removeAttribute('name'); } catch(e) {}
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

            // Mirror section select into hidden input
            const sectionSelect = document.getElementById('lesson_section_select');
            const sectionHidden = document.getElementById('lesson_section_id');
            if (sectionSelect && sectionHidden) {
                sectionSelect.addEventListener('change', function() {
                    sectionHidden.value = this.value;
                });
            }
        });

        function editSection(id, title) {
            document.getElementById('edit_section_title').value = title;
            document.getElementById('editSectionForm').action = `/teacher/courses/<?php echo e($course->id); ?>/sections/${id}`;
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
            alert('سيتم إضافة ميزة تعديل الدرس قريباً');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/teacher/courses/lessons.blade.php ENDPATH**/ ?>