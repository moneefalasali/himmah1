<?php $__env->startSection('title', 'منهج الدورة: ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>منهج الدورة: <?php echo e($course->title); ?></h2>
            <?php if(isset($uniCourse)): ?>
                <p class="text-muted mb-0">
                    <i class="fas fa-university me-1"></i>
                    ترتيب مخصص لجامعة <?php echo e($uniCourse->university->name); ?>

                </p>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->isSubscribedTo($course)): ?>
                    <a href="<?php echo e(route('student.courses.ai.show', $course)); ?>" class="btn btn-primary">
                        <i class="fas fa-robot me-1"></i> فتح مساعد همّه الذكي
                    </a>
                <?php endif; ?>
                <?php if(auth()->user()->isEnrolledIn($course)): ?>
                    <a href="<?php echo e(route('chat.course', $course)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-comments me-1"></i> دردشة الكورس
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-outline-secondary">
                العودة إلى الصفحة الرئيسية
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="mb-0">المناهج الدراسية</h4>
        </div>
        <div class="card-body">
            <?php if(isset($lessons)): ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    الدروس مرتبة حسب منهج جامعتك
                </div>
                
                <ul class="list-group">
                    <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="text-decoration-none">
                                    <i class="fas fa-play-circle me-2"></i> <?php echo e($lesson->title); ?>

                                </a>
                                <small class="d-block text-muted ms-4">
                                    <?php if($lesson->duration): ?>
                                        <?php echo e($lesson->formatted_duration); ?>

                                    <?php endif; ?>
                                </small>
                            </div>
                            <div>
                                <?php if($lesson->progress && $lesson->progress->completed): ?>
                                    <span class="badge bg-success">مكتمل</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">غير مكتمل</span>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                
                <?php $__empty_1 = true; $__currentLoopData = $course->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mb-4">
                        <h5 class="mb-3"><?php echo e($section->title); ?></h5>
                        
                        <ul class="list-group">
                            <?php $__currentLoopData = $section->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="text-decoration-none">
                                            <i class="fas fa-play-circle me-2"></i> <?php echo e($lesson->title); ?>

                                        </a>
                                        <small class="d-block text-muted ms-4">
                                            <?php if($lesson->duration): ?>
                                                <?php echo e($lesson->formatted_duration); ?>

                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php if($lesson->progress && $lesson->progress->completed): ?>
                                            <span class="badge bg-success">مكتمل</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">غير مكتمل</span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> لم يتم إضافة مناهج دراسية لهذه الدورة بعد.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if(isset($course->quizzes) && $course->quizzes->isNotEmpty()): ?>
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h4 class="mb-0">الاختبارات</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php $__currentLoopData = $course->quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo e($quiz->title); ?></strong>
                                <div class="text-muted small"><?php echo e($quiz->questions_count); ?> سؤال • <?php echo e($quiz->duration_minutes ? $quiz->duration_minutes . ' دقيقة' : ''); ?></div>
                            </div>
                            <div>
                                <a href="<?php echo e(route('student.quizzes.show', $quiz)); ?>" class="btn btn-sm btn-outline-primary">ابدأ الاختبار</a>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary">العودة للوحة التحكم</a>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/courses/curriculum.blade.php ENDPATH**/ ?>