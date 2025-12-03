<?php

use App\Http\Controllers\RequestGeneratorController;
use Illuminate\Support\Facades\Route;

/**
 * @file request_generator.php
 * @brief مسارات مولد Form Requests.
 *
 * يحتوي هذا الملف على جميع المسارات المتعلقة بمولد Form Requests،
 * بما في ذلك واجهة المستخدم ونقاط النهاية API.
 *
 * Routes for the Form Request Generator.
 *
 * This file contains all routes related to the Form Request Generator,
 * including user interface and API endpoints.
 *
 * @version 3.29.0
 * @author Manus AI
 */

// مجموعة مسارات Request Generator
Route::prefix('request-generator')->name('request-generator.')->group(function () {
    
    // عرض الصفحة الرئيسية
    Route::get('/', [RequestGeneratorController::class, 'index'])
        ->name('index');

    // عرض نموذج الإنشاء
    Route::get('/create', [RequestGeneratorController::class, 'create'])
        ->name('create');

    // API Endpoints
    Route::prefix('api')->name('api.')->group(function () {
        
        // توليد Request جديد
        Route::post('/generate', [RequestGeneratorController::class, 'generate'])
            ->name('generate');

        // توليد من قالب
        Route::post('/generate-from-template', [RequestGeneratorController::class, 'generateFromTemplate'])
            ->name('generate-from-template');

        // حفظ Request
        Route::post('/save', [RequestGeneratorController::class, 'save'])
            ->name('save');

        // قائمة Requests
        Route::get('/list', [RequestGeneratorController::class, 'list'])
            ->name('list');

        // حذف Request
        Route::delete('/delete', [RequestGeneratorController::class, 'delete'])
            ->name('delete');

        // الحصول على القوالب
        Route::get('/templates', [RequestGeneratorController::class, 'templates'])
            ->name('templates');
    });
});
