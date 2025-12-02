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
        <!-- لوحة التحكم -->
        <li class="nav-item">
            <a href="{{ route('developer.dashboard') }}" class="nav-link {{ request()->is('developer/dashboard') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>لوحة التحكم</p>
            </a>
        </li>
        
        <!-- المراقبة والتصحيح -->
        <li class="nav-item">
            <a href="https://php-magic-system-main-4kqldr.laravel.cloud/telescope" target="_blank" class="nav-link">
                <i class="far fa-circle nav-icon text-blue-400"></i>
                <p>Telescope <i class="fas fa-external-link-alt text-xs ml-1"></i></p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.debugbar') }}" class="nav-link {{ request()->is('developer/debugbar') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-purple-400"></i>
                <p>Debugbar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="https://php-magic-system-main-4kqldr.laravel.cloud/horizon" target="_blank" class="nav-link">
                <i class="far fa-circle nav-icon text-red-400"></i>
                <p>Horizon <i class="fas fa-external-link-alt text-xs ml-1"></i></p>
            </a>
        </li>
        
        <!-- قاعدة البيانات -->
        <li class="nav-item">
            <a href="{{ route('developer.migrations') }}" class="nav-link {{ request()->is('developer/migrations') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-green-400"></i>
                <p>Migrations</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.seeders') }}" class="nav-link {{ request()->is('developer/seeders') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-pink-400"></i>
                <p>Seeders</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.database-info') }}" class="nav-link {{ request()->is('developer/database-info') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-blue-400"></i>
                <p>معلومات القاعدة</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.database-optimize') }}" class="nav-link {{ request()->is('developer/database-optimize') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-purple-400"></i>
                <p>تحسين القاعدة</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.database-backup') }}" class="nav-link {{ request()->is('developer/database-backup') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-teal-400"></i>
                <p>نسخ احتياطي</p>
            </a>
        </li>
        
        <!-- أدوات الكود -->
        <li class="nav-item">
            <a href="{{ route('developer.cache') }}" class="nav-link {{ request()->is('developer/cache') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-orange-400"></i>
                <p>مسح Cache</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.pint') }}" class="nav-link {{ request()->is('developer/pint') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-cyan-400"></i>
                <p>تنسيق الكود (Pint)</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.tests') }}" class="nav-link {{ request()->is('developer/tests') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-green-400"></i>
                <p>تشغيل الاختبارات</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.routes-list') }}" class="nav-link {{ request()->is('developer/routes-list') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-purple-400"></i>
                <p>قائمة Routes</p>
            </a>
        </li>
        
        <!-- معلومات النظام -->
        <li class="nav-item">
            <a href="{{ route('developer.server-info') }}" class="nav-link {{ request()->is('developer/server-info') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-orange-400"></i>
                <p>معلومات الخادم</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('developer.logs-viewer') }}" class="nav-link {{ request()->is('developer/logs-viewer') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon text-red-400"></i>
                <p>السجلات (Logs)</p>
            </a>
        </li>
    </ul>
</li>
