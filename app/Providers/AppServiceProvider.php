<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register views for Genes
        $this->loadViewsFromGenes();
    }

    /**
     * Load views from all Genes
     */
    protected function loadViewsFromGenes(): void
    {
        $genesPath = app_path('Genes');
        
        if (is_dir($genesPath)) {
            $genes = scandir($genesPath);
            
            foreach ($genes as $gene) {
                if ($gene === '.' || $gene === '..') {
                    continue;
                }
                
                $geneViewsPath = $genesPath . '/' . $gene . '/Views';
                
                if (is_dir($geneViewsPath)) {
                    // Register views without namespace to match controller usage
                    $this->loadViewsFrom($geneViewsPath, '');
                }
            }
        }
    }
}
