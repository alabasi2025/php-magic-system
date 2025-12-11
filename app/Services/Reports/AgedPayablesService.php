<?php

namespace App\Services\Reports;

use Carbon\Carbon;

class AgedPayablesService
{
    public function generate(?string $asOfDate = null, array $options = []): array
    {
        $asOfDate = $asOfDate ?? Carbon::now()->toDateString();
        
        // TODO: Implement when Supplier/Invoice models are ready
        return [
            'aging_buckets' => [
                '0-30' => ['count' => 0, 'amount' => 0],
                '31-60' => ['count' => 0, 'amount' => 0],
                '61-90' => ['count' => 0, 'amount' => 0],
                '90+' => ['count' => 0, 'amount' => 0],
            ],
            'total' => 0,
            'as_of_date' => $asOfDate,
        ];
    }
}
