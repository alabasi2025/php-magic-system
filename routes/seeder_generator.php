<?php

/**
 * 🧬 Gene: Seeder Generator Routes
 * 
 * مسارات نظام توليد الـ Seeders
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Routes
 */

use App\Http\Controllers\SeederGeneratorController;
use Illuminate\Support\Facades\Route;

// ========== Web Routes ==========

Route::prefix('seeder-generator')->name('seeder-generator.')->group(function () {
    
    // الصفحة الرئيسية
    Route::get('/', [SeederGeneratorController::class, 'index'])
        ->name('index');
    
    // صفحة الإنشاء
    Route::get('/create', [SeederGeneratorController::class, 'create'])
        ->name('create');
    
    // توليد من وصف نصي
    Route::post('/generate/text', [SeederGeneratorController::class, 'generateFromText'])
        ->name('generate.text');
    
    // توليد من JSON
    Route::post('/generate/json', [SeederGeneratorController::class, 'generateFromJson'])
        ->name('generate.json');
    
    // توليد من قالب
    Route::post('/generate/template', [SeederGeneratorController::class, 'generateFromTemplate'])
        ->name('generate.template');
    
    // توليد من جدول موجود
    Route::post('/generate/reverse', [SeederGeneratorController::class, 'generateFromTable'])
        ->name('generate.reverse');
    
    // عرض تفاصيل
    Route::get('/{id}', [SeederGeneratorController::class, 'show'])
        ->name('show');
    
    // تحديث
    Route::put('/{id}', [SeederGeneratorController::class, 'update'])
        ->name('update');
    
    // حفظ كملف
    Route::post('/{id}/save-file', [SeederGeneratorController::class, 'saveFile'])
        ->name('save-file');
    
    // تنفيذ
    Route::post('/{id}/execute', [SeederGeneratorController::class, 'execute'])
        ->name('execute');
    
    // تحميل
    Route::get('/{id}/download', [SeederGeneratorController::class, 'download'])
        ->name('download');
    
    // حذف
    Route::delete('/{id}', [SeederGeneratorController::class, 'destroy'])
        ->name('destroy');
});

// ========== API Routes ==========

Route::prefix('api/seeder-generator')->name('api.seeder-generator.')->group(function () {
    
    // قائمة الـ Seeders
    Route::get('/', [SeederGeneratorController::class, 'apiIndex'])
        ->name('index');
    
    // توليد جديد
    Route::post('/generate', [SeederGeneratorController::class, 'apiGenerate'])
        ->name('generate');
    
    // تفاصيل Seeder
    Route::get('/{id}', [SeederGeneratorController::class, 'apiShow'])
        ->name('show');
    
    // تحديث Seeder
    Route::put('/{id}', [SeederGeneratorController::class, 'apiUpdate'])
        ->name('update');
    
    // حذف Seeder
    Route::delete('/{id}', [SeederGeneratorController::class, 'apiDestroy'])
        ->name('destroy');
    
    // تنفيذ Seeder
    Route::post('/{id}/execute', [SeederGeneratorController::class, 'apiExecute'])
        ->name('execute');
    
    // قائمة القوالب
    Route::get('/templates', [SeederGeneratorController::class, 'apiTemplates'])
        ->name('templates');
});
