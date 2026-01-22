<?php $__env->startSection('title', 'دوراتي'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>دوراتي المسجلة</h2>
        <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-primary">تصفح المزيد من الدورات</a>
    </div>
    
    <?php if($purchases->isEmpty()): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> لم تقم بالتسجيل في أي دورة بعد.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if($purchase->course->image): ?>
                            <img src="<?php echo e(Storage::url($purchase->course->image)); ?>" 
                                 class="card-img-top" alt="<?php echo e($purchase->course->title); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo e($purchase->course->title); ?></h5>
                            <p class="card-text text-muted"><?php echo e(Str::limit($purchase->course->description, 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-success">تم الشراء</span>
                                <a href="<?php echo e(route('courses.curriculum', $purchase->course)); ?>" class="btn btn-sm btn-primary">
                                    متابعة الدراسة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/user/my_courses.blade.php ENDPATH**/ ?>