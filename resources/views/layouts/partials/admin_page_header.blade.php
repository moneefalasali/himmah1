<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <h2 class="mb-1">@yield('page_title', $title ?? 'لوحة الإدارة')</h2>
        @hasSection('page_subtitle')
            <p class="text-muted-xs mb-0">@yield('page_subtitle')</p>
        @endif
    </div>

    <div class="d-flex align-items-center gap-2">
        @yield('page_actions')
    </div>
</div>
