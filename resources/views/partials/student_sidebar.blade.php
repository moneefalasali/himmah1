<div class="student-sidebar">
    <h5 class="mb-3">قائمة الطالب</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">لوحة التحكم</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('my-courses') }}">دوراتي</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('my-courses') }}">المفضلة</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">الملف الشخصي</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('student.live-sessions.index') ?? '#' }}">الحصص المباشرة</a></li>
    </ul>
</div>
