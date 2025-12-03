<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_type',
        'description',
        'files_changed',
        'lines_added',
        'lines_deleted',
        'commit_hash',
        'commit_message',
        'branch_name',
        'author',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'files_changed' => 'array',
        'metadata' => 'array',
        'lines_added' => 'integer',
        'lines_deleted' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get operations by type
     */
    public static function getByType($type, $limit = 10)
    {
        return self::where('operation_type', $type)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent operations
     */
    public static function getRecent($limit = 10)
    {
        return self::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get operations by branch
     */
    public static function getByBranch($branch, $limit = 10)
    {
        return self::where('branch_name', $branch)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get operations by status
     */
    public static function getByStatus($status, $limit = 10)
    {
        return self::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get statistics
     */
    public static function getStatistics($period = 'today')
    {
        $query = self::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        return [
            'total_operations' => $query->count(),
            'commits' => $query->where('operation_type', 'commit')->count(),
            'pushes' => $query->where('operation_type', 'push')->count(),
            'pulls' => $query->where('operation_type', 'pull')->count(),
            'branches' => $query->where('operation_type', 'branch')->count(),
            'successful' => $query->where('status', 'success')->count(),
            'failed' => $query->where('status', 'failed')->count(),
        ];
    }

    /**
     * Get total lines changed
     */
    public function getTotalLinesChanged()
    {
        return $this->lines_added + $this->lines_deleted;
    }

    /**
     * Check if operation was successful
     */
    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    /**
     * Check if operation failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Get formatted operation type
     */
    public function getFormattedOperationType()
    {
        $types = [
            'commit' => 'Commit',
            'push' => 'Push',
            'pull' => 'Pull',
            'branch' => 'Branch',
            'merge' => 'Merge',
            'checkout' => 'Checkout',
            'stash' => 'Stash',
        ];

        return $types[$this->operation_type] ?? ucfirst($this->operation_type);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'success' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }
}
