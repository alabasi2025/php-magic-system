<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Project;
use App\Models\Task;

/**
 * DashboardController
 * 
 * Main dashboard controller for SEMOP system
 * 
 * @package App\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'users' => User::count(),
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),
            'items' => Item::count(),
            'projects' => Project::count(),
            'tasks' => Task::count(),
        ];

        // Get recent activities (placeholder)
        $recentActivities = [];

        // System modules
        $modules = [
            [
                'name' => 'المحاسبة',
                'icon' => 'calculator',
                'route' => 'accounting.index',
                'color' => 'blue',
                'description' => 'نظام المحاسبة الكامل'
            ],
            [
                'name' => 'إدارة العملاء',
                'icon' => 'users',
                'route' => 'customers.index',
                'color' => 'green',
                'description' => 'CRM ونظام العملاء'
            ],
            [
                'name' => 'المخزون',
                'icon' => 'box',
                'route' => 'inventory.index',
                'color' => 'orange',
                'description' => 'إدارة المخزون والمستودعات'
            ],
            [
                'name' => 'المشتريات',
                'icon' => 'shopping-cart',
                'route' => 'purchases.index',
                'color' => 'purple',
                'description' => 'نظام المشتريات والموردين'
            ],
            [
                'name' => 'المبيعات',
                'icon' => 'trending-up',
                'route' => 'sales.index',
                'color' => 'red',
                'description' => 'نظام المبيعات والفواتير'
            ],
            [
                'name' => 'المشاريع',
                'icon' => 'briefcase',
                'route' => 'projects.index',
                'color' => 'indigo',
                'description' => 'إدارة المشاريع والمهام'
            ],
            [
                'name' => 'الموارد البشرية',
                'icon' => 'user-check',
                'route' => 'hr.index',
                'color' => 'pink',
                'description' => 'إدارة الموظفين والرواتب'
            ],
            [
                'name' => 'التصنيع',
                'icon' => 'settings',
                'route' => 'manufacturing.index',
                'color' => 'yellow',
                'description' => 'نظام التصنيع والإنتاج'
            ],
            [
                'name' => 'الأصول',
                'icon' => 'archive',
                'route' => 'assets.index',
                'color' => 'teal',
                'description' => 'إدارة الأصول الثابتة'
            ],
            [
                'name' => 'نقاط الولاء',
                'icon' => 'star',
                'route' => 'loyalty.index',
                'color' => 'amber',
                'description' => 'نظام نقاط الولاء والمكافآت'
            ],
            [
                'name' => 'التأمين الطبي',
                'icon' => 'heart',
                'route' => 'insurance.index',
                'color' => 'rose',
                'description' => 'نظام التأمين الطبي'
            ],
            [
                'name' => 'الجينات',
                'icon' => 'zap',
                'route' => 'genes.index',
                'color' => 'cyan',
                'description' => 'نظام تفعيل المميزات'
            ],
        ];

        return view('dashboard', compact('stats', 'modules', 'recentActivities'));
    }

    /**
     * Get system statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $stats = [
            'users' => User::count(),
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),
            'items' => Item::count(),
            'projects' => Project::count(),
            'tasks' => Task::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
