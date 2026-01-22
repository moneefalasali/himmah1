<nav class="bg-gray-800 text-white w-64 min-h-screen p-4">
    <div class="mb-8 text-2xl font-bold text-center">لوحة الإدارة</div>
    <ul class="space-y-2">
        <li><a href="{{ route('admin.dashboard') }}" class="block p-2 hover:bg-gray-700 rounded">الرئيسية</a></li>
        <li><a href="{{ route('admin.users.index') }}" class="block p-2 hover:bg-gray-700 rounded">المستخدمين والمعلمين</a></li>
        <li><a href="{{ route('admin.courses.index') }}" class="block p-2 hover:bg-gray-700 rounded">الكورسات (جامعي/عام)</a></li>
        <li><a href="{{ route('admin.universities.index') }}" class="block p-2 hover:bg-gray-700 rounded">الجامعات والمقررات</a></li>
        <li><a href="{{ route('admin.customer-chats.index') }}" class="block p-2 hover:bg-gray-700 rounded">الدردشات (خدمات/كورسات)</a></li>
        <li><a href="{{ route('admin.live-sessions.index') }}" class="block p-2 hover:bg-gray-700 rounded">الحصص الأونلاين (Zoom)</a></li>
        <li><a href="{{ route('admin.ai.reports') }}" class="block p-2 hover:bg-gray-700 rounded">تقارير الـ AI</a></li>
    </ul>
</nav>
