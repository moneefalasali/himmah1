<aside class="admin-sidebar bg-white rounded-2xl p-4 shadow-sm">
    <nav class="nav flex-column">
        @php
            $links = [
                ['route' => 'admin.dashboard', 'label' => 'الرئيسية', 'icon' => 'fa-home'],
                ['route' => 'admin.courses.index', 'label' => 'الكورسات', 'icon' => 'fa-book'],
                ['route' => 'admin.courses.create', 'label' => 'إضافة كورس', 'icon' => 'fa-plus-circle'],
                ['route' => 'admin.users.index', 'label' => 'المستخدمون', 'icon' => 'fa-users'],
                ['route' => 'admin.universities.index', 'label' => 'الجامعات', 'icon' => 'fa-university'],
                ['route' => 'admin.uni_courses.index', 'label' => 'مقررات الجامعات', 'icon' => 'fa-book'],
                ['route' => 'admin.categories.index', 'label' => 'الفئات', 'icon' => 'fa-tags'],
                ['route' => 'admin.customer-chats.index', 'label' => 'دردشات العملاء', 'icon' => 'fa-comments'],
                ['route' => 'admin.enrollments.index', 'label' => 'إدارة الاشتراكات', 'icon' => 'fa-user-check'],
                ['route' => 'admin.services.index', 'label' => 'الخدمات', 'icon' => 'fa-concierge-bell'],
                ['route' => 'admin.live-sessions.index', 'label' => 'الحصص الأونلاين', 'icon' => 'fa-video'],
                ['route' => 'admin.ai.reports', 'label' => 'تقارير AI', 'icon' => 'fa-chart-bar'],
            ];
        @endphp

        @foreach($links as $link)
            @if(Route::has($link['route']))
                <a href="{{ route($link['route']) }}" class="nav-link d-flex align-items-center gap-2 py-2 px-3 rounded">
                    <i class="fas {{ $link['icon'] }} text-muted"></i>
                    <span class="small">{{ $link['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</aside>
