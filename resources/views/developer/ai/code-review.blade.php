@php
$toolData = [
    'code-refactor' => ['title' => 'تحسين الكود', 'icon' => 'fas fa-magic', 'description' => 'تحسين وإعادة هيكلة الكود تلقائياً'],
    'code-review' => ['title' => 'مراجعة الكود', 'icon' => 'fas fa-search', 'description' => 'مراجعة الكود واكتشاف المشاكل'],
    'bug-detector' => ['title' => 'كشف الأخطاء', 'icon' => 'fas fa-bug', 'description' => 'اكتشاف الأخطاء والمشاكل في الكود'],
    'documentation-generator' => ['title' => 'توليد التوثيق', 'icon' => 'fas fa-book', 'description' => 'توليد التوثيق تلقائياً من الكود'],
    'test-generator' => ['title' => 'مولد الاختبارات', 'icon' => 'fas fa-vial', 'description' => 'توليد اختبارات تلقائية للكود'],
    'performance-analyzer' => ['title' => 'تحليل الأداء', 'icon' => 'fas fa-tachometer-alt', 'description' => 'تحليل أداء التطبيق وتحسينه'],
    'security-scanner' => ['title' => 'فحص الأمان', 'icon' => 'fas fa-shield-alt', 'description' => 'فحص الثغرات الأمنية'],
    'api-generator' => ['title' => 'مولد API', 'icon' => 'fas fa-plug', 'description' => 'توليد API RESTful تلقائياً'],
    'database-optimizer' => ['title' => 'محسن قاعدة البيانات', 'icon' => 'fas fa-database', 'description' => 'تحسين استعلامات قاعدة البيانات'],
    'code-translator' => ['title' => 'مترجم الأكواد', 'icon' => 'fas fa-language', 'description' => 'ترجمة الكود بين اللغات المختلفة'],
    'assistant' => ['title' => 'المساعد الذكي', 'icon' => 'fas fa-robot', 'description' => 'مساعد ذكي للإجابة على أسئلتك'],
    'settings' => ['title' => 'إعدادات AI', 'icon' => 'fas fa-cog', 'description' => 'إعدادات أدوات الذكاء الاصطناعي'],
];

$currentTool = basename(request()->path());
$data = $toolData[$currentTool] ?? ['title' => 'أداة AI', 'icon' => 'fas fa-robot', 'description' => 'أداة ذكاء اصطناعي'];
@endphp

@include('developer.ai.ai-tool-template', [
    'title' => $data['title'],
    'icon' => $data['icon'],
    'description' => $data['description'],
])
