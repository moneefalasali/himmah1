<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- تفاصيل الكورس -->
        <div class="lg:col-span-2">
            <div class="card mb-4">
                <img src="<?php echo e($course->thumbnail_url); ?>" class="card-img-top" style="height:420px;object-fit:cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-1"><?php echo e($course->title); ?></h1>
                            <div class="text-muted small"><?php echo e($course->subject?->name); ?> • <?php echo e($course->university?->name); ?></div>
                        </div>
                        <div>
                            <span class="badge bg-info text-dark"><?php echo e($course->type === 'recorded' ? 'مسجل' : 'أونلاين'); ?></span>
                        </div>
                    </div>

                    <p class="text-muted mb-4"><?php echo e($course->description); ?></p>

                    <h5 class="mb-3">محتوى الدورة</h5>

                    <?php if($course->sections && $course->sections->count() > 0): ?>
                        <?php $__currentLoopData = $course->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-3">
                                <div class="fw-bold"><?php echo e($section->title); ?> <span class="text-muted small">(<?php echo e($section->lessons->count()); ?> درس)</span></div>
                                <?php if($section->lessons->count() > 0): ?>
                                    <ul class="list-group mt-2">
                                        <?php $__currentLoopData = $section->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none text-reset">
                                                <div>
                                                    <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                                    <strong class="me-2"><?php echo e($lesson->title); ?></strong>
                                                    <span class="text-muted small"><?php echo e(Str::limit($lesson->description ?? '', 80)); ?></span>
                                                </div>
                                                <span class="text-muted small"><?php echo e($lesson->duration); ?> دقيقة</span>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-muted small">لا توجد دروس في هذا القسم بعد.</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php if($course->lessons->count() > 0): ?>
                            <ul class="list-group">
                                <?php $__currentLoopData = $course->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none text-reset">
                                        <div>
                                            <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                            <strong><?php echo e($lesson->title); ?></strong>
                                        </div>
                                        <span class="text-muted small"><?php echo e($lesson->duration); ?> دقيقة</span>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-secondary">لا توجد دروس لهذه الدورة بعد.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- الشريط الجانبي (Sidebar) -->
        <div class="lg:col-span-1">
            <div class="card sticky-top" style="top:24px;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo e($course->teacher?->avatar_url ?? url('assets/images/default-avatar.png')); ?>" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
                        <div>
                            <div class="fw-bold"><?php echo e($course->teacher?->name); ?></div>
                            <div class="text-muted small"><?php echo e($course->teacher?->role ?? 'المعلم'); ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="h4 mb-0"><?php echo e($course->price > 0 ? $course->price . ' ريال' : 'مجاني'); ?></div>
                        <div class="text-muted small"><?php echo e($course->students_count ?? ''); ?> طالب</div>
                    </div>

                    <?php if(auth()->check() && auth()->user()->isEnrolledIn($course)): ?>
                    <div class="space-y-4">
                        <?php $firstLesson = $course->lessons->first(); ?>
                        <?php if($firstLesson): ?>
                            <a href="<?php echo e(route('lessons.show', $firstLesson)); ?>" class="block w-full bg-blue-600 text-white text-center font-bold py-4 rounded-xl hover:bg-blue-700 transition">
                                متابعة التعلم
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('chat.course', $course)); ?>" class="block w-full bg-green-600 text-white text-center font-bold py-4 rounded-xl hover:bg-green-700 transition">
                            <i class="fas fa-comments mr-2"></i> دردشة الكورس الجماعية
                        </a>
                    </div>
                    <div class="mt-3">
                        <?php
                            $courseQuizzes = $course->quizzes()->where('status', 'published')->get();
                        ?>
                        <?php if($courseQuizzes->isNotEmpty()): ?>
                            <hr>
                            <h6 class="fw-bold">الاختبارات</h6>
                            <ul class="list-unstyled">
                                <?php $__currentLoopData = $courseQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="mb-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo e($q->title); ?></strong>
                                            <div class="text-muted small"><?php echo e($q->questions()->count()); ?> سؤال</div>
                                        </div>
                                        <div>
                                            <a href="<?php echo e(route('student.quizzes.show', $q)); ?>" class="btn btn-sm btn-outline-primary ms-2">عرض</a>
                                            <a href="<?php echo e(route('student.quizzes.take', $q)); ?>" class="btn btn-sm btn-primary">ابدأ</a>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                        <?php if(auth()->check()): ?>
                            <a href="<?php echo e(route('payment.form', $course)); ?>" class="btn btn-primary w-100 mb-3">اشترك الآن</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>?redirect=<?php echo e(urlencode(url()->full())); ?>" class="btn btn-primary w-100 mb-3">اشترك الآن</a>
                        <?php endif; ?>
                <?php endif; ?>
                    <hr>
                    <h6 class="fw-bold">هل تحتاج مساعدة؟</h6>
                    <?php if(auth()->check() && auth()->user()->isEnrolledIn($course)): ?>
                        <a href="<?php echo e(route('student.courses.ai.show', $course)); ?>" class="btn btn-outline-indigo w-100 mb-2">اسأل المساعد الذكي</a>
                    <?php endif; ?>
                    <a href="<?php echo e(env('PLATFORM_WHATSAPP') ? env('PLATFORM_WHATSAPP') : 'mailto:' . 'hemmah.platform.app@gmail.com'); ?>" target="_blank" rel="noopener" class="btn btn-outline-primary w-100">تواصل مع الدعم الفني</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/courses/show.blade.php ENDPATH**/ ?>