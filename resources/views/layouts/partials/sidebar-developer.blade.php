<!-- نظام المطور -->
<li class="nav-item">
    <a href="{{ route('developer.dashboard') }}" class="nav-link {{ request()->is('developer*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-code"></i>
        <p>
            نظام المطور
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('developer.dashboard') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>لوحة التحكم</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.artisan.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>أوامر Artisan</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.code-generator.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>مولد الأكواد</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.database.info') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>قاعدة البيانات</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.monitor.system-info') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>مراقبة النظام</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.cache.overview') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>الذاكرة المؤقتة</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.logs.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>السجلات</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>أدوات AI</p>
            </a>
        </li>
    </ul>
</li>
