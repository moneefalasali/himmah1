<div class="sidebar p-3">
    <div class="d-flex align-items-center mb-4">
        <div class="me-2">
            <img src="<?php echo e(Auth::user()->avatar_url ?? asset('images/default-avatar.png')); ?>" alt="avatar" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
        </div>
        <div>
            <div class="fw-bold"><?php echo e(Auth::user()->name); ?></div>
            <div class="small text-muted">المعلم</div>
        </div>
    </div>

    <nav class="nav flex-column">
        <a class="nav-link <?php echo e(request()->routeIs('teacher.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.dashboard')); ?>">
            <i class="fas fa-tachometer-alt me-2"></i>لوحة المعلم
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.courses.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.courses.index')); ?>">
            <i class="fas fa-book me-2"></i>الدورات
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.courses.show') ? 'active' : ''); ?>" href="#">
            <i class="fas fa-film me-2"></i>الدروس
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.quizzes.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.quizzes.index')); ?>">
            <i class="fas fa-question-circle me-2"></i>الاختبارات
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.live-sessions.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.live-sessions.index')); ?>">
            <i class="fas fa-video me-2"></i>الجلسات المباشرة
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.earnings.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.earnings.index')); ?>">
            <i class="fas fa-wallet me-2"></i>الأرباح
        </a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.chats.*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.chats.index')); ?>">
            <i class="fas fa-comments me-2"></i>المحادثات
        </a>
        <hr>
        <a class="nav-link text-danger" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
    </nav>
</div>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>
<?php /**PATH C:\xampp\htdocs\himm23\resources\views/partials/teacher_nav.blade.php ENDPATH**/ ?>