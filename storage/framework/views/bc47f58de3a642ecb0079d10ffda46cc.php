<?php $__env->startSection('content'); ?>
<div class="container">
    <h3>جدول الحصص المباشرة</h3>
    <div class="row mt-4">
        <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 mb-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($session->topic); ?></h5>
                        <p class="text-muted">الكورس: <?php echo e($session->course->title); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>الموعد:</strong> <?php echo e($session->start_time->format('Y-m-d h:i A')); ?><br>
                                <strong>المدة:</strong> <?php echo e($session->duration); ?> دقيقة
                            </div>
                            <div>
                                <?php if($session->isLive()): ?>
                                    <a href="<?php echo e(route('student.live-sessions.join', $session)); ?>" class="btn btn-success btn-lg">
                                        دخول الحصة الآن
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary disabled">
                                        غير متاحة الآن
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">لا توجد حصص مباشرة مجدولة حالياً.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/user/live_sessions/index.blade.php ENDPATH**/ ?>