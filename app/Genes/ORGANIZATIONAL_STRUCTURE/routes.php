<?php

/**
 * ORGANIZATIONAL_STRUCTURE Gene Routes
 * 
 * مسارات جين الهيكل التنظيمي
 * يتضمن مسارات الشركات القابضة، الوحدات، الأقسام، والمشاريع
 */

use Illuminate\Support\Facades\Route;
use App\Genes\ORGANIZATIONAL_STRUCTURE\Controllers\HoldingController;
use App\Genes\ORGANIZATIONAL_STRUCTURE\Controllers\UnitController;
use App\Genes\ORGANIZATIONAL_STRUCTURE\Controllers\DepartmentController;
use App\Genes\ORGANIZATIONAL_STRUCTURE\Controllers\ProjectController;

// Organization Structure Routes with 'organization' prefix
Route::prefix('organization')->name('organization.')->group(function () {
    // Holdings - الشركات القابضة
    Route::resource('holdings', HoldingController::class);
    
    // Units - الوحدات
    Route::resource('units', UnitController::class);
    
    // Departments - الأقسام
    Route::resource('departments', DepartmentController::class);
    
    // Projects - المشاريع
    Route::resource('projects', ProjectController::class);
});

// Shortcut routes (without organization prefix) for easier access
Route::resource('holdings', HoldingController::class);
Route::resource('units', UnitController::class);
Route::resource('departments', DepartmentController::class);
Route::resource('projects', ProjectController::class);
