<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModelGeneratorController;

/**
 * 🧬 Routes: Model Generator v3.26.0
 * 
 * مسارات مولد الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */

Route::prefix('model-generator')->name('model-generator.')->middleware(['web'])->group(function () {
    
    // الصفحة الرئيسية
    Route::get('/', [ModelGeneratorController::class, 'index'])->name('index');
    
    // صفحة إنشاء Model جديد
    Route::get('/create', [ModelGeneratorController::class, 'create'])->name('create');
    
    // عرض تفاصيل Generation
    Route::get('/{generation}', [ModelGeneratorController::class, 'show'])->name('show');
    
    // تحديث Generation
    Route::put('/{generation}', [ModelGeneratorController::class, 'update'])->name('update');
    
    // حذف Generation
    Route::delete('/{generation}', [ModelGeneratorController::class, 'destroy'])->name('destroy');
    
    // توليد من وصف نصي
    Route::post('/generate/text', [ModelGeneratorController::class, 'generateFromText'])->name('generate.text');
    
    // توليد من JSON Schema
    Route::post('/generate/json', [ModelGeneratorController::class, 'generateFromJson'])->name('generate.json');
    
    // توليد من قاعدة البيانات
    Route::post('/generate/database', [ModelGeneratorController::class, 'generateFromDatabase'])->name('generate.database');
    
    // توليد من Migration
    Route::post('/generate/migration', [ModelGeneratorController::class, 'generateFromMigration'])->name('generate.migration');
    
    // توليد من جميع الجداول
    Route::post('/generate/all', [ModelGeneratorController::class, 'generateAll'])->name('generate.all');
    
    // توليد من قالب
    Route::post('/generate/template/{template}', [ModelGeneratorController::class, 'generateFromTemplate'])->name('generate.template');
    
    // التحقق من صحة Generation
    Route::post('/{generation}/validate', [ModelGeneratorController::class, 'validate'])->name('validate');
    
    // نشر Generation
    Route::post('/{generation}/deploy', [ModelGeneratorController::class, 'deploy'])->name('deploy');
    
    // الحصول على إحصائيات
    Route::get('/api/statistics', [ModelGeneratorController::class, 'statistics'])->name('statistics');
    
    // الحصول على قائمة الجداول
    Route::get('/api/tables', [ModelGeneratorController::class, 'getTables'])->name('tables');
    
    // الحصول على قائمة Migrations
    Route::get('/api/migrations', [ModelGeneratorController::class, 'getMigrations'])->name('migrations');
});

/**
 * API Routes (بدون middleware auth للاستخدام الخارجي)
 */
Route::prefix('api/model-generator')->name('api.model-generator.')->middleware(['api'])->group(function () {
    
    // توليد من وصف نصي (API)
    Route::post('/generate/text', [ModelGeneratorController::class, 'generateFromText']);
    
    // توليد من JSON Schema (API)
    Route::post('/generate/json', [ModelGeneratorController::class, 'generateFromJson']);
    
    // توليد من قاعدة البيانات (API)
    Route::post('/generate/database', [ModelGeneratorController::class, 'generateFromDatabase']);
    
    // الحصول على إحصائيات (API)
    Route::get('/statistics', [ModelGeneratorController::class, 'statistics']);
});
