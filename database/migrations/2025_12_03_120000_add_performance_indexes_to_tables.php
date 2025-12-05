<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds performance indexes to improve query speed
     * for the most frequently accessed tables and columns.
     */
    public function up(): void
    {
        // Add indexes to chart_accounts table
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->index('chart_group_id', 'idx_chart_accounts_chart_group_id');
            $table->index('parent_id', 'idx_chart_accounts_parent_id');
            $table->index('account_type', 'idx_chart_accounts_account_type');
            $table->index('is_active', 'idx_chart_accounts_is_active');
            $table->index('is_linked', 'idx_chart_accounts_is_linked');
            $table->index(['chart_group_id', 'is_active'], 'idx_chart_accounts_group_active');
            $table->index(['account_type', 'is_active'], 'idx_chart_accounts_type_active');
            $table->index(['parent_id', 'is_active'], 'idx_chart_accounts_parent_active');
        });

        // Add indexes to chart_groups table
        Schema::table('chart_groups', function (Blueprint $table) {
            $table->index('unit_id', 'idx_chart_groups_unit_id');
            $table->index('is_active', 'idx_chart_groups_is_active');
            $table->index(['unit_id', 'is_active'], 'idx_chart_groups_unit_active');
        });

        // Add indexes to cash_boxes table
        Schema::table('cash_boxes', function (Blueprint $table) {
            $table->index('unit_id', 'idx_cash_boxes_unit_id');
            $table->index('intermediate_account_id', 'idx_cash_boxes_intermediate_account_id');
            $table->index('is_active', 'idx_cash_boxes_is_active');
            $table->index(['unit_id', 'is_active'], 'idx_cash_boxes_unit_active');
        });

        // Add indexes to cash_box_transactions table
        if (Schema::hasTable('cash_box_transactions')) {
            Schema::table('cash_box_transactions', function (Blueprint $table) {
                $table->index('cash_box_id', 'idx_cash_box_transactions_cash_box_id');
                $table->index('type', 'idx_cash_box_transactions_type');
                $table->index('transaction_date', 'idx_cash_box_transactions_date');
                $table->index(['cash_box_id', 'transaction_date'], 'idx_cash_box_transactions_box_date');
                $table->index(['type', 'transaction_date'], 'idx_cash_box_transactions_type_date');
            });
        }

        // Add indexes to intermediate_accounts table
        if (Schema::hasTable('intermediate_accounts')) {
            Schema::table('intermediate_accounts', function (Blueprint $table) {
                $table->index('chart_group_id', 'idx_intermediate_accounts_chart_group_id');
                $table->index('intermediate_for', 'idx_intermediate_accounts_for');
                $table->index('is_active', 'idx_intermediate_accounts_is_active');
                $table->index(['intermediate_for', 'is_active'], 'idx_intermediate_accounts_for_active');
            });
        }

        // Add indexes to intermediate_transactions table
        if (Schema::hasTable('intermediate_transactions')) {
            Schema::table('intermediate_transactions', function (Blueprint $table) {
                $table->index('intermediate_account_id', 'idx_intermediate_transactions_account_id');
                $table->index('type', 'idx_intermediate_transactions_type');
                $table->index('transaction_date', 'idx_intermediate_transactions_date');
                $table->index(['intermediate_account_id', 'transaction_date'], 'idx_intermediate_transactions_account_date');
            });
        }

        // Add indexes to partners table
        if (Schema::hasTable('partners')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->index('unit_id', 'idx_partners_unit_id');
                $table->index('is_active', 'idx_partners_is_active');
                $table->index(['unit_id', 'is_active'], 'idx_partners_unit_active');
            });
        }

        // Add indexes to partner_transactions table
        if (Schema::hasTable('partner_transactions')) {
            Schema::table('partner_transactions', function (Blueprint $table) {
                $table->index('partner_id', 'idx_partner_transactions_partner_id');
                $table->index('type', 'idx_partner_transactions_type');
                $table->index('transaction_date', 'idx_partner_transactions_date');
                $table->index(['partner_id', 'transaction_date'], 'idx_partner_transactions_partner_date');
            });
        }

        // Add indexes to holdings table
        if (Schema::hasTable('holdings')) {
            Schema::table('holdings', function (Blueprint $table) {
                $table->index('is_active', 'idx_holdings_is_active');
                $table->index('created_at', 'idx_holdings_created_at');
            });
        }

        // Add indexes to units table
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->index('holding_id', 'idx_units_holding_id');
                $table->index('is_active', 'idx_units_is_active');
                $table->index(['holding_id', 'is_active'], 'idx_units_holding_active');
            });
        }

        // Add indexes to departments table
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->index('unit_id', 'idx_departments_unit_id');
                $table->index('is_active', 'idx_departments_is_active');
                $table->index(['unit_id', 'is_active'], 'idx_departments_unit_active');
            });
        }

        // Add indexes to projects table
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->index('unit_id', 'idx_projects_unit_id');
                $table->index('department_id', 'idx_projects_department_id');
                $table->index('status', 'idx_projects_status');
                $table->index(['unit_id', 'status'], 'idx_projects_unit_status');
            });
        }

        // Add indexes to budgets table
        if (Schema::hasTable('budgets')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->index('unit_id', 'idx_budgets_unit_id');
                $table->index('status', 'idx_budgets_status');
                $table->index(['unit_id', 'status'], 'idx_budgets_unit_status');
            });
        }

        // Add indexes to budget_items table
        if (Schema::hasTable('budget_items')) {
            Schema::table('budget_items', function (Blueprint $table) {
                $table->index('budget_id', 'idx_budget_items_budget_id');
                $table->index('chart_account_id', 'idx_budget_items_chart_account_id');
            });
        }

        // Add indexes to report_templates table
        if (Schema::hasTable('report_templates')) {
            Schema::table('report_templates', function (Blueprint $table) {
                $table->index('report_type', 'idx_report_templates_type');
                $table->index('is_active', 'idx_report_templates_is_active');
                $table->index(['report_type', 'is_active'], 'idx_report_templates_type_active');
            });
        }

        // Add indexes to generated_reports table
        if (Schema::hasTable('generated_reports')) {
            Schema::table('generated_reports', function (Blueprint $table) {
                $table->index('report_template_id', 'idx_generated_reports_template_id');
                $table->index('generated_by', 'idx_generated_reports_generated_by');
                $table->index('generated_at', 'idx_generated_reports_generated_at');
            });
        }

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('created_at', 'idx_users_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from chart_accounts table
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropIndex('idx_chart_accounts_chart_group_id');
            $table->dropIndex('idx_chart_accounts_parent_id');
            $table->dropIndex('idx_chart_accounts_account_type');
            $table->dropIndex('idx_chart_accounts_is_active');
            $table->dropIndex('idx_chart_accounts_is_linked');
            $table->dropIndex('idx_chart_accounts_group_active');
            $table->dropIndex('idx_chart_accounts_type_active');
            $table->dropIndex('idx_chart_accounts_parent_active');
        });

        // Drop indexes from chart_groups table
        Schema::table('chart_groups', function (Blueprint $table) {
            $table->dropIndex('idx_chart_groups_unit_id');
            $table->dropIndex('idx_chart_groups_is_active');
            $table->dropIndex('idx_chart_groups_unit_active');
        });

        // Drop indexes from cash_boxes table
        Schema::table('cash_boxes', function (Blueprint $table) {
            $table->dropIndex('idx_cash_boxes_unit_id');
            $table->dropIndex('idx_cash_boxes_intermediate_account_id');
            $table->dropIndex('idx_cash_boxes_is_active');
            $table->dropIndex('idx_cash_boxes_unit_active');
        });

        // Drop indexes from other tables (if they exist)
        if (Schema::hasTable('cash_box_transactions')) {
            Schema::table('cash_box_transactions', function (Blueprint $table) {
                $table->dropIndex('idx_cash_box_transactions_cash_box_id');
                $table->dropIndex('idx_cash_box_transactions_type');
                $table->dropIndex('idx_cash_box_transactions_date');
                $table->dropIndex('idx_cash_box_transactions_box_date');
                $table->dropIndex('idx_cash_box_transactions_type_date');
            });
        }

        // Continue with other tables...
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_created_at');
        });
    }
};
