<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * Gene Service Provider
 * 
 * مزود الخدمة لتحميل Migrations والموارد من الأجنحة (Genes)
 */
class GeneServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الخدمات
     */
    public function register(): void
    {
        //
    }

    /**
     * تحميل الموارد عند بدء التطبيق
     */
    public function boot(): void
    {
        $this->loadGeneMigrations();
        $this->loadGeneViews();
        $this->loadGeneRoutes();
    }

    /**
     * تحميل Migrations من جميع الأجنحة
     */
    protected function loadGeneMigrations(): void
    {
        $genesPath = app_path('Genes');
        
        if (!File::isDirectory($genesPath)) {
            return;
        }

        // الحصول على جميع مجلدات الأجنحة
        $genes = File::directories($genesPath);

        foreach ($genes as $genePath) {
            $migrationsPath = $genePath . '/Database/Migrations';
            
            if (File::isDirectory($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }

    /**
     * تحميل Views من جميع الأجنحة
     */
    protected function loadGeneViews(): void
    {
        $genesPath = app_path('Genes');
        
        if (!File::isDirectory($genesPath)) {
            return;
        }

        $genes = File::directories($genesPath);

        foreach ($genes as $genePath) {
            $viewsPath = $genePath . '/Views';
            $geneName = basename($genePath);
            
            if (File::isDirectory($viewsPath)) {
                $this->loadViewsFrom($viewsPath, $geneName);
            }
        }
    }

    /**
     * تحميل Routes من جميع الأجنحة
     */
    protected function loadGeneRoutes(): void
    {
        $genesPath = app_path('Genes');
        
        if (!File::isDirectory($genesPath)) {
            return;
        }

        $genes = File::directories($genesPath);

        foreach ($genes as $genePath) {
            // تحميل ملف routes.php الرئيسي
            $mainRoutesFile = $genePath . '/routes.php';
            if (File::exists($mainRoutesFile)) {
                $this->loadRoutesFrom($mainRoutesFile);
            }

            // تحميل ملفات routes من مجلد routes
            $routesPath = $genePath . '/routes';
            if (File::isDirectory($routesPath)) {
                $routeFiles = File::files($routesPath);
                foreach ($routeFiles as $routeFile) {
                    if ($routeFile->getExtension() === 'php') {
                        $this->loadRoutesFrom($routeFile->getPathname());
                    }
                }
            }
        }
    }
}
