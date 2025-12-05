<?php

namespace App\Console\Commands;

use App\Services\RequestGeneratorService;
use Illuminate\Console\Command;
use Throwable;

/**
 * @class GenerateRequestCommand
 * @package App\Console\Commands
 *
 * @brief أمر Artisan لتوليد Form Requests من سطر الأوامر.
 *
 * يوفر هذا الأمر إمكانية توليد Form Requests باستخدام
 * الذكاء الاصطناعي مباشرة من سطر الأوامر.
 *
 * Artisan command for generating Form Requests from CLI.
 *
 * This command provides the ability to generate Form Requests using
 * AI directly from the command line.
 *
 * @version 3.29.0
 * @author Manus AI
 */
class GenerateRequestCommand extends Command
{
    /**
     * @var string $signature توقيع الأمر.
     * The name and signature of the console command.
     */
    protected $signature = 'generate:request
                            {name : The name of the Request class}
                            {--type= : The type of Request (store, update, search, filter, custom)}
                            {--description= : Description of the Request}
                            {--fields= : Fields in JSON format}
                            {--authorization : Include authorization logic}
                            {--custom-messages : Include custom error messages}
                            {--save : Save the generated Request to file}';

    /**
     * @var string $description وصف الأمر.
     * The console command description.
     */
    protected $description = 'Generate a Laravel Form Request using AI';

    /**
     * @var RequestGeneratorService $generatorService خدمة التوليد.
     * The generator service.
     */
    protected RequestGeneratorService $generatorService;

    /**
     * GenerateRequestCommand constructor.
     *
     * @param RequestGeneratorService $generatorService خدمة التوليد.
     * The generator service.
     */
    public function __construct(RequestGeneratorService $generatorService)
    {
        parent::__construct();
        $this->generatorService = $generatorService;
    }

    /**
     * @brief تنفيذ الأمر.
     *
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->info('🚀 Starting Form Request generation...');

            // جمع المعلومات
            $config = $this->collectConfiguration();

            // التحقق من الإعدادات
            $this->info('✓ Configuration validated');

            // توليد Request
            $this->info('⚙️  Generating Request with AI...');
            $result = $this->generatorService->generate($config);

            // عرض النتيجة
            $this->displayResult($result);

            // حفظ إذا طلب المستخدم
            if ($this->option('save')) {
                $this->info('💾 Saving Request to file...');
                $saveResult = $this->generatorService->save($result['name'], $result['code']);
                $this->info("✓ Request saved to: {$saveResult['path']}");
            }

            $this->info('✅ Request generated successfully!');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error('❌ Failed to generate Request: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @brief جمع إعدادات التوليد.
     *
     * Collect generation configuration.
     *
     * @return array
     */
    protected function collectConfiguration(): array
    {
        $config = [
            'name' => $this->argument('name'),
            'type' => $this->option('type') ?? RequestGeneratorService::TYPE_CUSTOM,
            'description' => $this->option('description') ?? '',
            'authorization' => $this->option('authorization'),
            'custom_messages' => $this->option('custom-messages'),
        ];

        // معالجة الحقول
        $fieldsJson = $this->option('fields');
        if ($fieldsJson) {
            $config['fields'] = json_decode($fieldsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format for fields');
            }
        } else {
            // طلب الحقول بشكل تفاعلي
            $config['fields'] = $this->collectFieldsInteractively();
        }

        return $config;
    }

    /**
     * @brief جمع الحقول بشكل تفاعلي.
     *
     * Collect fields interactively.
     *
     * @return array
     */
    protected function collectFieldsInteractively(): array
    {
        $fields = [];
        $this->info('📝 Enter fields (leave name empty to finish):');

        while (true) {
            $name = $this->ask('Field name');
            if (empty($name)) {
                break;
            }

            $rules = $this->ask('Validation rules (e.g., required|string|max:255)');
            
            $fields[] = [
                'name' => $name,
                'rules' => $rules,
            ];

            $this->info("✓ Field '{$name}' added");
        }

        if (empty($fields)) {
            throw new \Exception('At least one field is required');
        }

        return $fields;
    }

    /**
     * @brief عرض نتيجة التوليد.
     *
     * Display generation result.
     *
     * @param array $result نتيجة التوليد. The generation result.
     * @return void
     */
    protected function displayResult(array $result): void
    {
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Generated Request: ' . $result['name']);
        $this->info('Type: ' . $result['type']);
        $this->info('Path: ' . $result['path']);
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->line($result['code']);
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
    }
}
