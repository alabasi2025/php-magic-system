<?php

return [

    /*
    |--------------------------------------------------------------------------
    | System Information
    |--------------------------------------------------------------------------
    |
    | معلومات النظام الأساسية - يتم تعبئتها عند التثبيت
    |
    */

    'client_name' => env('SYSTEM_CLIENT_NAME', 'SEMOP System'),
    'client_code' => env('SYSTEM_CLIENT_CODE', 'SEMOP'),
    'version' => 'v2.8.5',
    'environment' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Active Genes
    |--------------------------------------------------------------------------
    |
    | الجينات (المميزات) المفعلة في هذا التثبيت
    | يمكن تفعيل/تعطيل الجينات من خلال لوحة التحكم
    |
    */

    'active_genes' => [
        // سيتم تحميلها من قاعدة البيانات
    ],

    /*
    |--------------------------------------------------------------------------
    | Available Genes
    |--------------------------------------------------------------------------
    |
    | قائمة الجينات المتاحة في النظام
    |
    */

    'available_genes' => [
        'ORGANIZATIONAL_STRUCTURE' => [
            'name' => 'الهيكل التنظيمي',
            'description' => 'إدارة الشركات القابضة والوحدات والأقسام والمشاريع',
            'version' => '1.0.0',
            'requires' => [],
        ],
        'INTERMEDIATE_ACCOUNTS' => [
            'name' => 'الحسابات الوسيطة',
            'description' => 'نظام الحسابات الوسيطة والعمليات المالية',
            'version' => '1.0.0',
            'requires' => ['ORGANIZATIONAL_STRUCTURE'],
        ],
        'CASH_BOXES' => [
            'name' => 'الصناديق النقدية',
            'description' => 'إدارة الصناديق النقدية والعمليات النقدية',
            'version' => '1.0.0',
            'requires' => ['INTERMEDIATE_ACCOUNTS'],
        ],
        'ACCOUNTING_REPORTS' => [
            'name' => 'التقارير المحاسبية',
            'description' => 'تقارير رقابية شاملة للعمليات المالية',
            'version' => '1.0.0',
            'requires' => ['INTERMEDIATE_ACCOUNTS'],
        ],
        'BUDGET_PLANNING' => [
            'name' => 'تخطيط الميزانية',
            'description' => 'نظام تخطيط ومراقبة الميزانيات',
            'version' => '1.0.0',
            'requires' => ['ORGANIZATIONAL_STRUCTURE'],
        ],
        'CLIENT_REQUIREMENTS' => [
            'name' => 'متطلبات العملاء',
            'description' => 'نظام إدارة متطلبات وطلبات العملاء',
            'version' => '1.0.0',
            'requires' => [],
        ],
        'PARTNERSHIP_ACCOUNTING' => [
            'name' => 'محاسبة الشراكات',
            'description' => 'نظام محاسبة الشراكات والمشاريع المشتركة',
            'version' => '1.0.0',
            'requires' => [],
        ],
        'MULTI_CURRENCY' => [
            'name' => 'العملات المتعددة',
            'description' => 'دعم التعامل مع عملات متعددة',
            'version' => '1.0.0',
            'requires' => [],
        ],
        'ADVANCED_REPORTS' => [
            'name' => 'التقارير المتقدمة',
            'description' => 'تقارير مالية وإدارية متقدمة',
            'version' => '1.0.0',
            'requires' => [],
        ],
        'POWER_STATION_MANAGEMENT' => [
            'name' => 'إدارة محطات الكهرباء',
            'description' => 'نظام متخصص لإدارة محطات الكهرباء',
            'version' => '1.0.0',
            'requires' => ['PARTNERSHIP_ACCOUNTING'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | System Settings
    |--------------------------------------------------------------------------
    |
    | إعدادات النظام العامة
    |
    */

    'settings' => [
        'default_currency' => env('DEFAULT_CURRENCY', 'YER'),
        'default_language' => env('DEFAULT_LANGUAGE', 'ar'),
        'timezone' => env('APP_TIMEZONE', 'Asia/Aden'),
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'datetime_format' => 'Y-m-d H:i:s',
    ],

];
