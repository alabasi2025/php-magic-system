<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Partner;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\SimpleRevenue;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\SimpleExpense;

class PartnershipController extends Controller
{
    public function index()
    {
        $stats = [
            'total_partners' => Partner::count(),
            'total_revenues' => SimpleRevenue::sum('amount'),
            'total_expenses' => SimpleExpense::sum('amount'),
            'net_profit' => SimpleRevenue::sum('amount') - SimpleExpense::sum('amount'),
        ];
        
        return view('partnership.index', compact('stats'));
    }
    
    public function partners()
    {
        $partners = Partner::with('shares')->paginate(20);
        return view('partnership.partners', compact('partners'));
    }
    
    public function revenues()
    {
        $revenues = SimpleRevenue::with(['unit', 'project'])->latest()->paginate(20);
        return view('partnership.revenues', compact('revenues'));
    }
    
    public function expenses()
    {
        $expenses = SimpleExpense::with(['unit', 'project'])->latest()->paginate(20);
        return view('partnership.expenses', compact('expenses'));
    }
    
    public function profits()
    {
        return view('partnership.profits');
    }
    
    public function reports()
    {
        return view('partnership.reports');
    }
    
    public function settings()
    {
        return view('partnership.settings');
    }
}
