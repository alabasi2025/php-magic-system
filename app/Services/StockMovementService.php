<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Item;
use App\Models\Alert;
use Illuminate\Support\Facades\DB;

/**
 * StockMovementService
 * 
 * Business logic for stock movements and integration with accounting system.
 */
class StockMovementService
{
    /**
     * Generate unique movement number.
     */
    public function generateMovementNumber(string $movementType): string
    {
        $prefix = [
            'stock_in' => 'IN',
            'stock_out' => 'OUT',
            'transfer' => 'TRF',
            'adjustment' => 'ADJ',
            'return' => 'RET',
        ][$movementType] ?? 'MOV';

        $date = now()->format('Ymd');
        
        // Get last movement number for today
        $lastMovement = StockMovement::where('movement_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('movement_number', 'desc')
            ->first();

        if ($lastMovement) {
            $lastNumber = (int) substr($lastMovement->movement_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $newNumber);
    }

    /**
     * Create corresponding transfer entry for destination warehouse.
     */
    public function createTransferEntry(StockMovement $movement): void
    {
        if ($movement->movement_type !== 'transfer') {
            return;
        }

        // The transfer creates a stock_out from source and stock_in to destination
        // This is already handled in the main movement record
        // No additional entry needed as we track both warehouses in one record
    }

    /**
     * Create accounting journal entry for stock movement.
     * 
     * Integration with accounting system:
     * - Stock In: Debit Inventory, Credit Accounts Payable (or Cash)
     * - Stock Out: Debit Cost of Goods Sold, Credit Inventory
     * - Transfer: No accounting entry (internal movement)
     * - Adjustment: Debit/Credit Inventory, Credit/Debit Inventory Adjustment
     * - Return: Debit Accounts Payable (or Cash), Credit Inventory
     */
    public function createJournalEntry(StockMovement $movement)
    {
        // Check if JournalEntry model exists
        if (!class_exists('App\Models\JournalEntry')) {
            return null;
        }

        $journalEntryClass = 'App\Models\JournalEntry';
        $chartAccountClass = 'App\Models\ChartAccount';

        // Get relevant chart accounts
        $inventoryAccount = $chartAccountClass::where('code', 'like', '1-2-%')->first(); // Asset - Inventory
        $cogsAccount = $chartAccountClass::where('code', 'like', '5-1-%')->first(); // Expense - COGS
        $apAccount = $chartAccountClass::where('code', 'like', '2-1-%')->first(); // Liability - Accounts Payable
        $adjustmentAccount = $chartAccountClass::where('code', 'like', '5-9-%')->first(); // Other Expense - Inventory Adjustment

        if (!$inventoryAccount) {
            return null;
        }

        $entries = [];
        $description = $movement->getMovementTypeLabel() . ' - ' . $movement->item->name;

        switch ($movement->movement_type) {
            case 'stock_in':
                // Debit Inventory, Credit Accounts Payable
                $entries[] = [
                    'account_id' => $inventoryAccount->id,
                    'debit' => $movement->total_cost,
                    'credit' => 0,
                    'description' => $description,
                ];
                if ($apAccount) {
                    $entries[] = [
                        'account_id' => $apAccount->id,
                        'debit' => 0,
                        'credit' => $movement->total_cost,
                        'description' => $description,
                    ];
                }
                break;

            case 'stock_out':
                // Debit COGS, Credit Inventory
                if ($cogsAccount) {
                    $entries[] = [
                        'account_id' => $cogsAccount->id,
                        'debit' => $movement->total_cost,
                        'credit' => 0,
                        'description' => $description,
                    ];
                }
                $entries[] = [
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $movement->total_cost,
                    'description' => $description,
                ];
                break;

            case 'transfer':
                // No accounting entry for internal transfers
                return null;

            case 'adjustment':
                // Debit/Credit Inventory, Credit/Debit Adjustment Account
                if ($movement->quantity > 0) {
                    // Positive adjustment (increase inventory)
                    $entries[] = [
                        'account_id' => $inventoryAccount->id,
                        'debit' => $movement->total_cost,
                        'credit' => 0,
                        'description' => $description,
                    ];
                    if ($adjustmentAccount) {
                        $entries[] = [
                            'account_id' => $adjustmentAccount->id,
                            'debit' => 0,
                            'credit' => $movement->total_cost,
                            'description' => $description,
                        ];
                    }
                } else {
                    // Negative adjustment (decrease inventory)
                    $entries[] = [
                        'account_id' => $inventoryAccount->id,
                        'debit' => 0,
                        'credit' => abs($movement->total_cost),
                        'description' => $description,
                    ];
                    if ($adjustmentAccount) {
                        $entries[] = [
                            'account_id' => $adjustmentAccount->id,
                            'debit' => abs($movement->total_cost),
                            'credit' => 0,
                            'description' => $description,
                        ];
                    }
                }
                break;

            case 'return':
                // Debit Accounts Payable, Credit Inventory
                if ($apAccount) {
                    $entries[] = [
                        'account_id' => $apAccount->id,
                        'debit' => $movement->total_cost,
                        'credit' => 0,
                        'description' => $description,
                    ];
                }
                $entries[] = [
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $movement->total_cost,
                    'description' => $description,
                ];
                break;
        }

        if (empty($entries)) {
            return null;
        }

        // Create journal entry
        $journalEntry = $journalEntryClass::create([
            'entry_number' => $this->generateJournalEntryNumber(),
            'entry_date' => $movement->movement_date,
            'description' => $description,
            'reference_type' => 'stock_movement',
            'reference_id' => $movement->id,
            'created_by' => $movement->created_by,
            'status' => 'approved',
        ]);

        // Create journal entry details
        foreach ($entries as $entry) {
            $journalEntry->details()->create($entry);
        }

        return $journalEntry;
    }

    /**
     * Generate unique journal entry number.
     */
    protected function generateJournalEntryNumber(): string
    {
        $journalEntryClass = 'App\Models\JournalEntry';
        $date = now()->format('Ymd');
        
        $lastEntry = $journalEntryClass::where('entry_number', 'like', "JE-INV-{$date}-%")
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->entry_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('JE-INV-%s-%04d', $date, $newNumber);
    }

    /**
     * Check stock level and create alert if below minimum.
     */
    public function checkStockLevel(int $itemId): void
    {
        $item = Item::findOrFail($itemId);
        $currentStock = $item->getTotalStock();

        if ($currentStock < $item->min_stock) {
            // Create alert if Alert model exists
            if (class_exists('App\Models\Alert')) {
                Alert::create([
                    'type' => 'low_stock',
                    'title' => 'تنبيه: مخزون منخفض',
                    'message' => "الصنف {$item->name} (SKU: {$item->sku}) أقل من الحد الأدنى. المخزون الحالي: {$currentStock} {$item->unit->name}",
                    'severity' => 'warning',
                    'reference_type' => 'item',
                    'reference_id' => $item->id,
                    'status' => 'active',
                ]);
            }
        }
    }

    /**
     * Validate stock movement before creation.
     */
    public function validateMovement(array $data): array
    {
        $errors = [];

        // Check if warehouse is active
        $warehouse = \App\Models\Warehouse::find($data['warehouse_id']);
        if ($warehouse && !$warehouse->isActive()) {
            $errors[] = 'المخزن غير نشط';
        }

        // Check if item is active
        $item = Item::find($data['item_id']);
        if ($item && !$item->isActive()) {
            $errors[] = 'الصنف غير نشط';
        }

        // For stock out, check if sufficient stock available
        if ($data['movement_type'] === 'stock_out') {
            $inventoryService = new InventoryService();
            if (!$inventoryService->hasSufficientStock($data['item_id'], $data['warehouse_id'], $data['quantity'])) {
                $errors[] = 'المخزون غير كافٍ لإتمام هذه العملية';
            }
        }

        // For transfer, ensure source and destination are different
        if ($data['movement_type'] === 'transfer') {
            if ($data['warehouse_id'] === $data['to_warehouse_id']) {
                $errors[] = 'لا يمكن النقل إلى نفس المخزن';
            }
        }

        return $errors;
    }
}
