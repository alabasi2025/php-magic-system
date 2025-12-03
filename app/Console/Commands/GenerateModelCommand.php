<?php

namespace App\Console\Commands;

use App\Services\ModelGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * 🧬 Command: GenerateModelCommand
 * 
 * أمر Artisan لتوليد Models من CLI
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Commands
 * @package App\Console\Commands
 */
class GenerateModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model
                            {--text= : Generate from text description}
                            {--json= : Generate from JSON file}
                            {--table= : Generate from database table}
                            {--migration= : Generate from migration file}
                            {--all : Generate from all database tables}
                            {--deploy : Deploy generated model to filesystem}
                            {--validate : Validate generated model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🧬 Model Generator v3.26.0 - توليد Models بشكل ذكي';

    /**
     * Model Generator Service
     */
    protected ModelGeneratorService $generatorService;

    /**
     * Constructor
     */
    public function __construct(ModelGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧬 Model Generator v3.26.0');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        try {
            // توليد من وصف نصي
            if ($this->option('text')) {
                return $this->generateFromText();
            }

            // توليد من JSON
            if ($this->option('json')) {
                return $this->generateFromJson();
            }

            // توليد من جدول قاعدة البيانات
            if ($this->option('table')) {
                return $this->generateFromTable();
            }

            // توليد من Migration
            if ($this->option('migration')) {
                return $this->generateFromMigration();
            }

            // توليد من جميع الجداول
            if ($this->option('all')) {
                return $this->generateAll();
            }

            // عرض القائمة التفاعلية
            $this->showInteractiveMenu();

        } catch (\Exception $e) {
            $this->error('❌ خطأ: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * عرض القائمة التفاعلية
     */
    protected function showInteractiveMenu()
    {
        $choice = $this->choice(
            'اختر طريقة التوليد:',
            [
                '1' => 'من وصف نصي (Text Description)',
                '2' => 'من JSON Schema',
                '3' => 'من جدول قاعدة البيانات (Database Table)',
                '4' => 'من ملف Migration',
                '5' => 'من جميع الجداول (All Tables)',
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
                $this->generateFromTable();
                break;
            case '4':
                $this->generateFromMigration();
                break;
            case '5':
                $this->generateAll();
                break;
        }
    }

    /**
     * توليد من وصف نصي
     */
    protected function generateFromText()
    {
        $description = $this->option('text') ?: $this->ask('أدخل وصف الـ Model:');

        if (empty($description)) {
            $this->error('❌ الوصف مطلوب');
            return 1;
        }

        $this->info('⏳ جاري التوليد...');

        $generation = $this->generatorService->generateFromText($description);

        $this->displayGenerationResult($generation);

        return 0;
    }

    /**
     * توليد من JSON
     */
    protected function generateFromJson()
    {
        $jsonFile = $this->option('json') ?: $this->ask('أدخل مسار ملف JSON:');

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

        $generation = $this->generatorService->generateFromJson($schema);

        $this->displayGenerationResult($generation);

        return 0;
    }

    /**
     * توليد من جدول قاعدة البيانات
     */
    protected function generateFromTable()
    {
        $tableName = $this->option('table') ?: $this->ask('أدخل اسم الجدول:');

        if (empty($tableName)) {
            $this->error('❌ اسم الجدول مطلوب');
            return 1;
        }

        $this->info('⏳ جاري التوليد...');

        $generation = $this->generatorService->generateFromDatabase($tableName);

        $this->displayGenerationResult($generation);

        return 0;
    }

    /**
     * توليد من Migration
     */
    protected function generateFromMigration()
    {
        $migrationFile = $this->option('migration') ?: $this->ask('أدخل اسم ملف Migration:');

        if (empty($migrationFile)) {
            $this->error('❌ اسم ملف Migration مطلوب');
            return 1;
        }

        $this->info('⏳ جاري التوليد...');

        $generation = $this->generatorService->generateFromMigration($migrationFile);

        $this->displayGenerationResult($generation);

        return 0;
    }

    /**
     * توليد من جميع الجداول
     */
    protected function generateAll()
    {
        if (!$this->confirm('هل تريد توليد Models لجميع الجداول؟')) {
            return 0;
        }

        $this->info('⏳ جاري التوليد...');

        $results = $this->generatorService->generateAllFromDatabase();

        $this->newLine();
        $this->info('📊 النتائج:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $successCount = 0;
        $failedCount = 0;

        foreach ($results as $result) {
            if ($result['status'] === 'success') {
                $this->info("✓ {$result['table']} - تم التوليد بنجاح");
                $successCount++;
            } else {
                $this->error("✗ {$result['table']} - فشل: {$result['error']}");
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("✓ نجح: {$successCount}");
        $this->error("✗ فشل: {$failedCount}");

        return 0;
    }

    /**
     * عرض نتيجة التوليد
     */
    protected function displayGenerationResult($generation)
    {
        $this->newLine();
        $this->info('✅ تم التوليد بنجاح!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("📝 Model: {$generation->name}");
        $this->info("📊 Table: {$generation->table_name}");
        $this->info("📁 Namespace: {$generation->namespace}");
        $this->info("🔧 Input Method: {$generation->input_method_label}");
        $this->info("📈 Status: {$generation->status_label}");

        if ($generation->relations_count > 0) {
            $this->info("🔗 Relations: {$generation->relations_count}");
        }

        if ($generation->scopes_count > 0) {
            $this->info("🔍 Scopes: {$generation->scopes_count}");
        }

        // التحقق من الصحة إذا طلب ذلك
        if ($this->option('validate')) {
            $this->newLine();
            $this->info('⏳ جاري التحقق من الصحة...');
            $results = $this->generatorService->validate($generation);

            if ($results['valid']) {
                $this->info('✅ Model صحيح');
            } else {
                $this->error('❌ Model يحتوي على أخطاء:');
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

        // النشر إذا طلب ذلك
        if ($this->option('deploy')) {
            $this->newLine();
            
            if (!$this->confirm('هل تريد نشر Model إلى نظام الملفات؟')) {
                return;
            }

            $this->info('⏳ جاري النشر...');
            $success = $this->generatorService->deploy($generation);

            if ($success) {
                $this->info('✅ تم النشر بنجاح!');
                $this->info("📁 المسار: {$generation->file_path}");
            } else {
                $this->error('❌ فشل النشر');
            }
        } else {
            $this->newLine();
            $this->comment('💡 استخدم --deploy لنشر Model إلى نظام الملفات');
        }

        // عرض المحتوى
        if ($this->confirm('هل تريد عرض المحتوى المولد؟', false)) {
            $this->newLine();
            $this->line($generation->generated_content);
        }
    }
}
