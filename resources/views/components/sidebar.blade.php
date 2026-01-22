<div class="col-md-3 sidebar text-white">
    <div class="p-3">
        <h4 class="text-center py-3">لوحة التحكم</h4>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-primary' : '' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-house me-2"></i> الرئيسية
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('profile') ? 'active bg-primary' : '' }}" 
                   href="{{ route('profile') }}">
                    <i class="bi bi-person me-2"></i> الملف الشخصي
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('my-courses') ? 'active bg-primary' : '' }}" 
                   href="{{ route('my-courses') }}">
                    <i class="bi bi-journal me-2"></i> دوراتي
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('my-service-requests') ? 'active bg-primary' : '' }}" 
                   href="{{ route('my-service-requests') }}">
                    <i class="bi bi-tools me-2"></i> طلباتي
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('payment-history') ? 'active bg-primary' : '' }}" 
                   href="{{ route('payment-history') }}">
                    <i class="bi bi-wallet me-2"></i> سجل الدفع
                </a>
            </li>
            
            @if(auth()->user()?->is_admin)
                <li class="nav-item mt-3">
                    <a class="nav-link text-white bg-danger" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-shield-lock me-2"></i> لوحة المدير
                    </a>
                </li>
            @endif
            
            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-white w-100 text-start">
                        <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>