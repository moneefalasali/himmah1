<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">استكشف مستقبلك التعليمي</h1>
        <p class="text-muted">اختر من بين مئات الدورات المتخصصة في المناهج الجامعية والمهارات العامة مع أفضل الخبراء.</p>
    </div>

    <!-- Filters (simple row) -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">نوع التعليم</label>
                    <select name="classification" class="form-select">
                        <option value="" <?php echo e(request('classification') == '' ? 'selected' : ''); ?>>الكل</option>
                        <option value="university" <?php echo e(request('classification') == 'university' ? 'selected' : ''); ?>>جامعي</option>
                        <option value="general" <?php echo e(request('classification') == 'general' ? 'selected' : ''); ?>>دورات عامة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الجامعة</label>
                    <select name="university_id" class="form-select">
                        <option value="">اختر الجامعة...</option>
                        <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($uni->id); ?>" <?php echo e(request('university_id') == $uni->id ? 'selected' : ''); ?>><?php echo e($uni->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الفئة</label>
                    <select name="category_id" class="form-select">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e((string)request('category_id') === (string)$cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">نوع الدورة</label>
                    <select name="type" class="form-select">
                        <option value="" <?php echo e(request('type') == '' ? 'selected' : ''); ?>>الكل</option>
                        <option value="recorded" <?php echo e(request('type') == 'recorded' ? 'selected' : ''); ?>>مسجل</option>
                        <option value="online" <?php echo e(request('type') == 'online' ? 'selected' : ''); ?>>أونلاين</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">بحث سريع</label>
                    <div class="input-group">
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="ابحث عن دورة...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="col-12 mt-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">تطبيق الفلتر</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Course Grid -->
    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php if($course->thumbnail_url): ?>
                        <img src="<?php echo e($course->thumbnail_url); ?>" class="card-img-top" alt="<?php echo e($course->title); ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-light text-primary border"><?php echo e($course->category->name ?? 'عام'); ?></span>
                            <small class="text-muted"><i class="far fa-clock"></i> <?php echo e($course->total_duration ?? '0'); ?> ساعة</small>
                        </div>
                        <h5 class="card-title"><?php echo e($course->title); ?></h5>
                        <p class="card-text text-muted mb-4"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($course->description), 120)); ?></p>

                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                            <div>
                                <small class="d-block text-uppercase text-muted">السعر</small>
                                <div class="h5 mb-0"><?php echo e($course->price); ?> <small class="text-muted">ريال</small></div>
                            </div>
                            <a href="<?php echo e(route('courses.show', $course)); ?>" class="btn btn-primary">اشترك الآن</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info">لم يتم العثور على دورات.</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <?php echo e($courses->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/courses/index.blade.php ENDPATH**/ ?>