<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class VersionCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check version consistency across all files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking version consistency...');
        $this->newLine();

        $configVersion = config('version.number');
        $this->info("✅ Config Version: {$configVersion}");
        $this->newLine();

        $files = [
            'app/Http/Controllers/DeveloperController.php',
            'resources/views/dashboard.blade.php',
            'resources/views/layouts/app.blade.php',
            'routes/console.php',
            'routes/developer.php',
        ];

        $hardcodedVersions = [];
        $usingConfig = [];

        foreach ($files as $file) {
            $path = base_path($file);
            if (!File::exists($path)) {
                $this->warn("⚠️  File not found: {$file}");
                continue;
            }

            $content = File::get($path);
            
            // Check if using config
            if (str_contains($content, "config('version.number')")) {
                $usingConfig[] = $file;
                $this->line("✅ {$file} - Using config");
            } else {
                // Check for hardcoded versions
                if (preg_match('/v2\.\d+\.\d+/', $content, $matches)) {
                    $hardcodedVersions[$file] = $matches[0];
                    $this->error("❌ {$file} - Hardcoded version: {$matches[0]}");
                }
            }
        }

        $this->newLine();
        
        if (empty($hardcodedVersions)) {
            $this->info('🎉 All files are using centralized config!');
            $this->info('✅ Version consistency check passed!');
            return 0;
        } else {
            $this->error('⚠️  Found hardcoded versions in ' . count($hardcodedVersions) . ' file(s)');
            $this->warn('Please update these files to use config(\'version.number\')');
            return 1;
        }
    }
}
