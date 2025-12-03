<?php

/**
 * 🧬 Gene: Factory Generator Routes
 * 
 * مسارات نظام توليد الـ Factories
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Routes
 */

use App\Http\Controllers\FactoryGeneratorController;
use Illuminate\Support\Facades\Route;

// ========== Web Routes ==========

Route::prefix('factory-generator')->name('factory-generator.')->group(function () {
    
    // الصفحة الرئيسية
    Route::get('/', [FactoryGeneratorController::class, 'index'])
        ->name('index');
    
    // صفحة الإنشاء
    Route::get('/create', [FactoryGeneratorController::class, 'create'])
        ->name('create');
    
    // توليد من وصف نصي
    Route::post('/generate/text', [FactoryGeneratorController::class, 'generateFromText'])
        ->name('generate.text');
    
    // توليد من JSON
    Route::post('/generate/json', [FactoryGeneratorController::class, 'generateFromJson'])
        ->name('generate.json');
    
    // توليد من قالب
    Route::post('/generate/template', [FactoryGeneratorController::class, 'generateFromTemplate'])
        ->name('generate.template');
    
    // توليد من Model موجود
    Route::post('/generate/model', [FactoryGeneratorController::class, 'generateFromModel'])
        ->name('generate.model');
    
    // عرض تفاصيل
    Route::get('/{id}', [FactoryGeneratorController::class, 'show'])
        ->name('show');
    
    // تحديث
    Route::put('/{id}', [FactoryGeneratorController::class, 'update'])
        ->name('update');
    
    // حفظ كملف
    Route::post('/{id}/save-file', [FactoryGeneratorController::class, 'saveFile'])
        ->name('save-file');
    
    // تحميل
    Route::get('/{id}/download', [FactoryGeneratorController::class, 'download'])
        ->name('download');
    
    // حذف
    Route::delete('/{id}', [FactoryGeneratorController::class, 'destroy'])
        ->name('destroy');
});

// ========== API Routes ==========

Route::prefix('api/factory-generator')->name('api.factory-generator.')->group(function () {
    
    // قائمة الـ Factories
    Route::get('/', [FactoryGeneratorController::class, 'apiIndex'])
        ->name('index');
    
    // توليد جديد
    Route::post('/generate', [FactoryGeneratorController::class, 'apiGenerate'])
        ->name('generate');
    
    // تفاصيل Factory
    Route::get('/{id}', [FactoryGeneratorController::class, 'apiShow'])
        ->name('show');
    
    // تحديث Factory
    Route::put('/{id}', [FactoryGeneratorController::class, 'apiUpdate'])
        ->name('update');
    
    // حذف Factory
    Route::delete('/{id}', [FactoryGeneratorController::class, 'apiDestroy'])
        ->name('destroy');
    
    // قائمة القوالب
    Route::get('/templates', [FactoryGeneratorController::class, 'apiTemplates'])
        ->name('templates');
});
