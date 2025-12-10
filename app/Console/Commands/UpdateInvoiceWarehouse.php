<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;

class UpdateInvoiceWarehouse extends Command
{
    protected $signature = 'invoice:update-warehouse {invoice_id} {warehouse_id}';
    protected $description = 'Update warehouse_id for a specific invoice';

    public function handle()
    {
        $invoiceId = $this->argument('invoice_id');
        $warehouseId = $this->argument('warehouse_id');
        
        $invoice = PurchaseInvoice::find($invoiceId);
        
        if (!$invoice) {
            $this->error("Invoice #{$invoiceId} not found!");
            return 1;
        }
        
        $invoice->warehouse_id = $warehouseId;
        $invoice->save();
        
        $this->info("✅ Invoice #{$invoice->invoice_number} updated successfully!");
        $this->info("warehouse_id: {$invoice->warehouse_id}");
        
        return 0;
    }
}
