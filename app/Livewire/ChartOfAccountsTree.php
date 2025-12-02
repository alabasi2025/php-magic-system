<?php

namespace App\Livewire;

use App\Models\ChartOfAccount;
use App\Models\Unit;
use Livewire\Component;

class ChartOfAccountsTree extends Component
{
    public $unit_id;
    public $account_type;
    public $search = '';
    public $expandedNodes = [];

    protected $queryString = ['unit_id', 'account_type', 'search'];

    public function mount()
    {
        // تحميل جميع الحسابات الرئيسية مفتوحة بشكل افتراضي
        $rootAccounts = ChartOfAccount::whereNull('parent_id')->pluck('id')->toArray();
        $this->expandedNodes = $rootAccounts;
    }

    public function toggleNode($nodeId)
    {
        if (in_array($nodeId, $this->expandedNodes)) {
            // إغلاق العقدة
            $this->expandedNodes = array_diff($this->expandedNodes, [$nodeId]);
        } else {
            // فتح العقدة
            $this->expandedNodes[] = $nodeId;
        }
    }

    public function expandAll()
    {
        $allAccounts = ChartOfAccount::pluck('id')->toArray();
        $this->expandedNodes = $allAccounts;
    }

    public function collapseAll()
    {
        $this->expandedNodes = [];
    }

    public function render()
    {
        $query = ChartOfAccount::with(['unit', 'parent', 'children'])
            ->whereNull('parent_id'); // فقط الحسابات الرئيسية

        // تصفية حسب الوحدة
        if ($this->unit_id) {
            $query->where('unit_id', $this->unit_id);
        }

        // تصفية حسب نوع الحساب
        if ($this->account_type) {
            $query->where('account_type', $this->account_type);
        }

        // البحث
        if ($this->search) {
            $query->where(function($q) {
                $q->where('account_code', 'like', '%' . $this->search . '%')
                  ->orWhere('account_name_ar', 'like', '%' . $this->search . '%')
                  ->orWhere('account_name_en', 'like', '%' . $this->search . '%');
            });
        }

        $accounts = $query->orderBy('account_code')->get();
        $units = Unit::all();

        return view('livewire.chart-of-accounts-tree', [
            'accounts' => $accounts,
            'units' => $units,
        ]);
    }
}
