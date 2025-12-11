<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات - القيود اليومية
        $journalPermissions = [
            'journal-entries.view',
            'journal-entries.create',
            'journal-entries.update',
            'journal-entries.delete',
            'journal-entries.approve',
            'journal-entries.post',
            'journal-entries.reject',
        ];

        foreach ($journalPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الصلاحيات - دليل الحسابات
        $chartPermissions = [
            'chart-accounts.view',
            'chart-accounts.create',
            'chart-accounts.update',
            'chart-accounts.delete',
        ];

        foreach ($chartPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الصلاحيات - السنوات المالية
        $fiscalYearPermissions = [
            'fiscal-years.view',
            'fiscal-years.create',
            'fiscal-years.update',
            'fiscal-years.delete',
            'fiscal-years.close',
        ];

        foreach ($fiscalYearPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الصلاحيات - الفترات المالية
        $fiscalPeriodPermissions = [
            'fiscal-periods.view',
            'fiscal-periods.create',
            'fiscal-periods.update',
            'fiscal-periods.delete',
            'fiscal-periods.close',
        ];

        foreach ($fiscalPeriodPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الصلاحيات - التقارير
        $reportPermissions = [
            'reports.view',
            'reports.generate',
            'reports.export',
        ];

        foreach ($reportPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الأدوار

        // دور المدير - جميع الصلاحيات
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // دور المحاسب - صلاحيات محدودة
        $accountantRole = Role::create(['name' => 'accountant']);
        $accountantRole->givePermissionTo([
            'journal-entries.view',
            'journal-entries.create',
            'journal-entries.update',
            'chart-accounts.view',
            'fiscal-years.view',
            'fiscal-periods.view',
            'reports.view',
            'reports.generate',
        ]);

        // دور المراجع - صلاحيات الموافقة
        $auditorRole = Role::create(['name' => 'auditor']);
        $auditorRole->givePermissionTo([
            'journal-entries.view',
            'journal-entries.approve',
            'journal-entries.reject',
            'chart-accounts.view',
            'fiscal-years.view',
            'fiscal-periods.view',
            'reports.view',
            'reports.generate',
            'reports.export',
        ]);

        // دور المشاهد - صلاحيات القراءة فقط
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'journal-entries.view',
            'chart-accounts.view',
            'fiscal-years.view',
            'fiscal-periods.view',
            'reports.view',
        ]);

        $this->command->info('Roles and Permissions created successfully!');
    }
}
