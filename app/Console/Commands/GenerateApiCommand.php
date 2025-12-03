<?php

namespace App\Console\Commands;

use App\Services\ApiGeneratorService;
use Illuminate\Console\Command;

/**
 * Generate API Command
 * 
 * Artisan command to generate RESTful API for all models
 * 
 * @version 3.16.0
 * @author SEMOP Team
 */
class GenerateApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate 
                            {--force : Force regeneration of existing files}
                            {--model= : Generate API for specific model only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RESTful API for all models (v3.16.0)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 API Generator v3.16.0');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        try {
            $generator = new ApiGeneratorService();
            
            $this->info('⏳ جاري توليد API...');
            $this->newLine();

            $stats = $generator->generate();

            $this->newLine();
            $this->info('✅ اكتملت عملية التوليد بنجاح!');
            $this->newLine();

            // عرض الإحصائيات
            $this->table(
                ['المؤشر', 'القيمة'],
                [
                    ['النماذج المكتشفة', $stats['models_found']],
                    ['Controllers المولدة', $stats['controllers_generated']],
                    ['Routes المولدة', $stats['routes_generated']],
                    ['الأخطاء', count($stats['errors'])],
                ]
            );

            // عرض الأخطاء إن وجدت
            if (!empty($stats['errors'])) {
                $this->newLine();
                $this->error('⚠️  الأخطاء:');
                foreach ($stats['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            $this->newLine();
            $this->info('📄 تم إنشاء تقرير التوليد: API_GENERATOR_v3.16.0_REPORT.md');
            $this->newLine();

            $this->comment('الخطوات التالية:');
            $this->line('  1. أضف require __DIR__.\'/api_generated.php\'; إلى routes/api.php');
            $this->line('  2. راجع الـ Controllers المولدة في app/Http/Controllers/Api/');
            $this->line('  3. أضف قواعد Validation المناسبة');
            $this->line('  4. اختبر الـ API endpoints');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ فشلت عملية التوليد:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->line($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
