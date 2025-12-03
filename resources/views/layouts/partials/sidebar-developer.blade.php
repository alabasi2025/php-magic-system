<!-- نظام المطور -->
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-code" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
        <p>
            نظام المطور
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <!-- لوحة التحكم -->
        <li class="nav-item">
            <a href="{{ route('developer.dashboard') }}" class="nav-link {{ request()->is('developer/dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>لوحة التحكم</p>
            </a>
        </li>
        
        <!-- الذكاء الاصطناعي -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-robot nav-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>
                    الذكاء الاصطناعي
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="padding-right: 1rem;">
                <li class="nav-item">
                    <a href="{{ route('ai.code-generator') }}" class="nav-link {{ request()->is('ai/code-generator') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-pink-400"></i>
                        <p>مولد الأكواد</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.code-refactor') }}" class="nav-link {{ request()->is('ai/code-refactor') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-purple-400"></i>
                        <p>تحسين الكود</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.code-review') }}" class="nav-link {{ request()->is('ai/code-review') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-blue-400"></i>
                        <p>مراجعة الكود</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.bug-detector') }}" class="nav-link {{ request()->is('ai/bug-detector') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-red-400"></i>
                        <p>كشف الأخطاء</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.documentation-generator') }}" class="nav-link {{ request()->is('ai/documentation-generator') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-green-400"></i>
                        <p>توليد التوثيق</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.test-generator') }}" class="nav-link {{ request()->is('ai/test-generator') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-teal-400"></i>
                        <p>مولد الاختبارات</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.performance-analyzer') }}" class="nav-link {{ request()->is('ai/performance-analyzer') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-orange-400"></i>
                        <p>تحليل الأداء</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.security-scanner') }}" class="nav-link {{ request()->is('ai/security-scanner') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-red-400"></i>
                        <p>فحص الأمان</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.api-generator') }}" class="nav-link {{ request()->is('ai/api-generator') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-indigo-400"></i>
                        <p>مولد API</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.database-optimizer') }}" class="nav-link {{ request()->is('ai/database-optimizer') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-cyan-400"></i>
                        <p>محسن قاعدة البيانات</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.code-translator') }}" class="nav-link {{ request()->is('ai/code-translator') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-yellow-400"></i>
                        <p>مترجم الأكواد</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.assistant') }}" class="nav-link {{ request()->is('ai/assistant') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-pink-400"></i>
                        <p>المساعد الذكي</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ai.settings') }}" class="nav-link {{ request()->is('ai/settings') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-gray-400"></i>
                        <p>إعدادات AI</p>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- قاعدة البيانات -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-database nav-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>
                    قاعدة البيانات
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="padding-right: 1rem;">
                <li class="nav-item">
                    <a href="{{ route('developer.migrations') }}" class="nav-link {{ request()->is('developer/migrations') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-blue-400"></i>
                        <p>Migrations</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('developer.seeders') }}" class="nav-link {{ request()->is('developer/seeders') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-green-400"></i>
                        <p>Seeders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('developer.database-info') }}" class="nav-link {{ request()->is('developer/database-info') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-cyan-400"></i>
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
            </ul>
        </li>
        
        <!-- أدوات الكود -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tools nav-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>
                    أدوات الكود
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="padding-right: 1rem;">
                <li class="nav-item">
                    <a href="{{ route('developer.cache') }}" class="nav-link {{ request()->is('developer/cache') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-orange-400"></i>
                        <p>مسح Cache</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('developer.routes-list') }}" class="nav-link {{ request()->is('developer/routes-list') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon text-purple-400"></i>
                        <p>قائمة Routes</p>
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
            </ul>
        </li>
        
        <!-- المراقبة والتصحيح -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-search nav-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>
                    المراقبة والتصحيح
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="padding-right: 1rem;">
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
            </ul>
        </li>
        
        <!-- معلومات النظام -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-info-circle nav-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <p>
                    معلومات النظام
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview" style="padding-right: 1rem;">
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
    </ul>
</li>
