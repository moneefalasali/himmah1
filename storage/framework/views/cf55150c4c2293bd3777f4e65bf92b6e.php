

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <?php echo $__env->make('partials.student_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col-12 col-lg-9">
            <?php echo $__env->yieldContent('student_content'); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .student-sidebar {
        background: #fff;
        border-left: 1px solid #e2e8f0;
        padding: 1rem;
        border-radius: 0.5rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/layouts/student.blade.php ENDPATH**/ ?>