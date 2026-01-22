

<?php $__env->startSection('title', 'محادثات الكورسات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="mb-4">محادثات الكورسات</h1>

    <?php if($courses->isEmpty()): ?>
        <div class="alert alert-info">لا توجد دورات لإظهار محادثات لها.</div>
    <?php else: ?>
        <div class="list-group">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('chat.course', $course)); ?>" class="list-group-item list-group-item-action">
                    <?php echo e($course->title); ?>

                    <span class="float-end text-muted">فتح المحادثة</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/teacher/chats/index.blade.php ENDPATH**/ ?>