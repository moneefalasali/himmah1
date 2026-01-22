<?php $__env->startSection('title', 'دوراتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- عنوان الصفحة -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">دوراتي</h1>
        <a href="<?php echo e(route('teacher.courses.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>إضافة دورة جديدة
        </a>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-book text-primary fs-1"></i>
                    <h4 class="mt-2"><?php echo e($courses->total()); ?></h4>
                    <p class="text-muted mb-0">إجمالي الدورات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h4 class="mt-2"><?php echo e($courses->where('status', 'active')->count()); ?></h4>
                    <p class="text-muted mb-0">دورات منشورة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people text-info fs-1"></i>
                            <h4 class="mt-2"><?php echo e($courses->sum('students_count')); ?></h4>
                    <p class="text-muted mb-0">إجمالي الطلاب</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle text-warning fs-1"></i>
                    <h4 class="mt-2"><?php echo e($courses->sum('lessons_count')); ?></h4>
                    <p class="text-muted mb-0">إجمالي الدروس</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الدورات -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">قائمة الدورات</h5>
        </div>
        <div class="card-body">
            <?php if($courses->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الدورة</th>
                                <th>الحالة</th>
                                <th>السعر</th>
                                <th>عدد الدروس</th>
                                <th>عدد الطلاب</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($course->image): ?>
                                                <img src="<?php echo e(Storage::url($course->image)); ?>" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-book text-muted fs-4"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?php echo e($course->title); ?></h6>
                                                <small class="text-muted"><?php echo e(Str::limit($course->description, 50)); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($course->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                            <?php echo e($course->status === 'active' ? 'منشور' : 'غير منشور'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?php echo e(number_format($course->price, 2)); ?> ر.س</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($course->lessons_count ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo e($course->students_count ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($course->created_at->format('Y-m-d')); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('teacher.courses.show', $course)); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('teacher.courses.edit', $course)); ?>" 
                                               class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo e(route('teacher.courses.lessons', $course)); ?>" 
                                               class="btn btn-sm btn-outline-info" title="إدارة الدروس">
                                                <i class="bi bi-play-circle"></i>
                                            </a>
                                            <a href="<?php echo e(route('teacher.quizzes.index', ['course' => $course->id])); ?>" 
                                               class="btn btn-sm btn-outline-warning" title="إدارة الاختبارات">
                                                <i class="bi bi-question-circle"></i>
                                            </a>
                                            <a href="<?php echo e(route('chat.course', $course)); ?>" 
                                               class="btn btn-sm btn-outline-success" title="دردشة الكورس">
                                                <i class="bi bi-chat-dots"></i>
                                            </a>
                                            <a href="<?php echo e(route('teacher.courses.ai.show', $course)); ?>"
                                               class="btn btn-sm btn-outline-primary" title="المساعد الذكي">
                                                <i class="bi bi-robot"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- ترقيم الصفحات -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($courses->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-book text-muted fs-1"></i>
                    <h4 class="mt-3 text-muted">لا توجد دورات بعد</h4>
                    <p class="text-muted">ابدأ بإنشاء دورة جديدة لمشاركة معرفتك مع الطلاب</p>
                    <a href="<?php echo e(route('teacher.courses.create')); ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>إنشاء دورة جديدة
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\himm23\resources\views/teacher/courses/index.blade.php ENDPATH**/ ?>