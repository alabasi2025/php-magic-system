<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ReportAccessObserver
{
    public function accessed(string $reportType, array $params = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'report_accessed',
            'auditable_type' => 'Report',
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => [
                'report_type' => $reportType,
                'parameters' => $params,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function exported(string $reportType, string $format, array $params = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'report_exported',
            'auditable_type' => 'Report',
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => [
                'report_type' => $reportType,
                'format' => $format,
                'parameters' => $params,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
