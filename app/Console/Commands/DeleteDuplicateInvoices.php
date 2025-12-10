<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;

class DeleteDuplicateInvoices extends Command
{
    protected $signature = 'invoices:delete-duplicates';
    protected $description = 'حذف الفواتير ذات الأرقام الداخلية المكررة';

    public function handle()
    {
        $this->info('🔍 البحث عن الفواتير المكررة...');
        
        // البحث عن الفواتير ذات الأرقام الداخلية المكررة
        $duplicates = PurchaseInvoice::select('internal_number')
            ->groupBy('internal_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('internal_number');

        if ($duplicates->isEmpty()) {
            $this->info('✅ لا توجد فواتير مكررة!');
            return 0;
        }

        $this->warn('⚠️  تم العثور على ' . $duplicates->count() . ' رقم مكرر');
        
        foreach ($duplicates as $internalNumber) {
            $invoices = PurchaseInvoice::where('internal_number', $internalNumber)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // الاحتفاظ بالأول (الأحدث) وحذف الباقي
            $keep = $invoices->first();
            $toDelete = $invoices->skip(1);
            
            $this->line("📋 الرقم الداخلي: $internalNumber");
            $this->line("   ✅ الاحتفاظ بـ: ID={$keep->id}");
            
            foreach ($toDelete as $invoice) {
                $this->line("   ❌ حذف: ID={$invoice->id}");
                $invoice->delete();
            }
        }

        $this->info('✅ تم حذف جميع الفواتير المكررة بنجاح!');
        return 0;
    }
}
