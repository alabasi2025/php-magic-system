<?php

namespace App\Console\Commands;

use App\Services\MiddlewareGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * 🛡️ Command: GenerateMiddlewareCommand
 * 
 * أمر Artisan لتوليد Middleware من CLI
 * 
 * @version 3.28.0
 * @since 2025-12-03
 * @category Commands
 * @package App\Console\Commands
 * @author Manus AI
 */
class GenerateMiddlewareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:middleware
                            {--name= : Middleware name}
                            {--type= : Middleware type (auth, permission, rate_limit, logging, cors, validation, cache, transform, security, custom)}
                            {--text= : Generate from text description}
                            {--json= : Generate from JSON file}
                            {--template= : Generate from template}
                            {--save : Save generated middleware to filesystem}
                            {--validate : Validate generated middleware}
                            {--list-types : List all supported middleware types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🛡️ Middleware Generator v3.28.0 - توليد Middleware بشكل ذكي';

    /**
     * Middleware Generator Service
     */
    protected MiddlewareGeneratorService $generatorService;

    /**
     * Constructor
     */
    public function __construct(MiddlewareGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayHeader();

        try {
            // عرض قائمة الأنواع
            if ($this->option('list-types')) {
                return $this->listTypes();
            }

            // توليد من وصف نصي
            if ($this->option('text')) {
                return $this->generateFromText();
            }

            // توليد من JSON
            if ($this->option('json')) {
                return $this->generateFromJson();
            }

            // توليد من قالب
            if ($this->option('template')) {
                return $this->generateFromTemplate();
            }

            // عرض القائمة التفاعلية
            $this->showInteractiveMenu();

        } catch (\Exception $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    /**
     * عرض الترويسة
     */
    protected function displayHeader()
    {
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║      🛡️  Middleware Generator v3.28.0                     ║');
        $this->info('║      Generate Laravel Middleware Intelligently            ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * عرض قائمة الأنواع المدعومة
     */
    protected function listTypes()
    {
        $types = $this->generatorService->getSupportedTypes();

        $this->info('📋 Supported Middleware Types:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($types as $key => $description) {
            $this->line("  • <fg=cyan>{$key}</> - {$description}");
        }

        $this->newLine();
        $this->comment('💡 Use --type=<type> to generate specific middleware type');

        return 0;
    }

    /**
     * عرض القائمة التفاعلية
     */
    protected function showInteractiveMenu()
    {
        $choice = $this->choice(
            '🎯 اختر طريقة التوليد:',
            [
                '1' => 'من وصف نصي (Text Description)',
                '2' => 'من JSON Schema',
                '3' => 'من قالب (Template)',
                '4' => 'عرض الأنواع المدعومة (List Types)',
            ],
            '1'
        );

        switch ($choice) {
            case '1':
                $this->generateFromText();
                break;
            case '2':
                $this->generateFromJson();
                break;
            case '3':
                $this->generateFromTemplate();
                break;
            case '4':
                $this->listTypes();
                break;
        }
    }

    /**
     * توليد من وصف نصي
     */
    protected function generateFromText()
    {
        $description = $this->option('text') ?: $this->ask('📝 أدخل وصف الـ Middleware:');

        if (empty($description)) {
            $this->error('❌ الوصف مطلوب');
            return 1;
        }

        // اختيار النوع (اختياري)
        $type = $this->option('type');
        if (!$type) {
            $types = array_keys($this->generatorService->getSupportedTypes());
            $type = $this->choice('🎯 اختر نوع Middleware:', $types, 'custom');
        }

        // اختيار الاسم (اختياري)
        $name = $this->option('name') ?: $this->ask('📛 أدخل اسم Middleware (اتركه فارغاً للتوليد التلقائي):');

        $options = array_filter([
            'type' => $type,
            'name' => $name,
        ]);

        $this->info('⏳ جاري التوليد...');
        $this->newLine();

        $middleware = $this->generatorService->generateFromText($description, $options);

        $this->displayMiddlewareResult($middleware);

        return 0;
    }

    /**
     * توليد من JSON
     */
    protected function generateFromJson()
    {
        $jsonFile = $this->option('json') ?: $this->ask('📁 أدخل مسار ملف JSON:');

        if (!File::exists($jsonFile)) {
            $this->error('❌ الملف غير موجود: ' . $jsonFile);
            return 1;
        }

        $schema = json_decode(File::get($jsonFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('❌ خطأ في قراءة JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info('⏳ جاري التوليد...');
        $this->newLine();

        $middleware = $this->generatorService->generateFromJson($schema);

        $this->displayMiddlewareResult($middleware);

        return 0;
    }

    /**
     * توليد من قالب
     */
    protected function generateFromTemplate()
    {
        $templateName = $this->option('template') ?: $this->ask('📋 أدخل اسم القالب:');

        if (empty($templateName)) {
            $this->error('❌ اسم القالب مطلوب');
            return 1;
        }

        $name = $this->option('name') ?: $this->ask('📛 أدخل اسم Middleware:');
        $type = $this->option('type') ?: 'custom';

        $variables = [
            'name' => $name,
            'type' => $type,
        ];

        $this->info('⏳ جاري التوليد...');
        $this->newLine();

        try {
            $middleware = $this->generatorService->generateFromTemplate($templateName, $variables);
            $this->displayMiddlewareResult($middleware);
        } catch (\Exception $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * عرض نتيجة التوليد
     */
    protected function displayMiddlewareResult(array $middleware)
    {
        $this->info('✅ تم التوليد بنجاح!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->table(
            ['Property', 'Value'],
            [
                ['📛 Name', $middleware['name']],
                ['🎯 Type', $middleware['type']],
                ['📝 Description', $middleware['description']],
                ['📁 Namespace', $middleware['namespace']],
                ['📂 Path', $middleware['path']],
                ['📅 Created At', $middleware['created_at']],
            ]
        );

        // التحقق من الصحة إذا طلب ذلك
        if ($this->option('validate')) {
            $this->newLine();
            $this->info('⏳ جاري التحقق من الصحة...');
            $results = $this->generatorService->validate($middleware);

            if ($results['valid']) {
                $this->info('✅ Middleware صحيح');
            } else {
                $this->error('❌ Middleware يحتوي على أخطاء:');
                foreach ($results['errors'] as $error) {
                    $this->error("  • {$error}");
                }
            }

            if (!empty($results['warnings'])) {
                $this->warn('⚠️  تحذيرات:');
                foreach ($results['warnings'] as $warning) {
                    $this->warn("  • {$warning}");
                }
            }
        }

        // الحفظ إذا طلب ذلك
        if ($this->option('save')) {
            $this->newLine();
            
            if (!$this->confirm('💾 هل تريد حفظ Middleware إلى نظام الملفات؟', true)) {
                $this->displayPreview($middleware);
                return;
            }

            $this->info('⏳ جاري الحفظ...');
            $success = $this->generatorService->save($middleware);

            if ($success) {
                $this->info('✅ تم الحفظ بنجاح!');
                $this->info("📁 المسار: {$middleware['path']}");
                
                $this->newLine();
                $this->comment('💡 لا تنسى تسجيل Middleware في app/Http/Kernel.php:');
                $this->line("protected \$middlewareAliases = [");
                $this->line("    // ...");
                $this->line("    '{$this->getMiddlewareAlias($middleware['name'])}' => \\{$middleware['namespace']}\\{$middleware['name']}::class,");
                $this->line("];");
            } else {
                $this->error('❌ فشل الحفظ');
            }
        } else {
            $this->newLine();
            $this->comment('💡 استخدم --save لحفظ Middleware إلى نظام الملفات');
            $this->displayPreview($middleware);
        }
    }

    /**
     * عرض معاينة المحتوى
     */
    protected function displayPreview(array $middleware)
    {
        if ($this->confirm('👁️  هل تريد عرض المحتوى المولد؟', false)) {
            $this->newLine();
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line($middleware['content']);
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        }
    }

    /**
     * الحصول على alias للـ middleware
     */
    protected function getMiddlewareAlias(string $name): string
    {
        // إزالة "Middleware" من النهاية
        $alias = str_replace('Middleware', '', $name);
        
        // تحويل من PascalCase إلى snake_case
        $alias = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $alias));
        
        return $alias;
    }
}
