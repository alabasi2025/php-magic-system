<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'analyzed_at',
        'total_files',
        'total_lines',
        'logical_lines',
        'comment_lines',
        'blank_lines',
        'avg_cyclomatic_complexity',
        'max_cyclomatic_complexity',
        'avg_cognitive_complexity',
        'max_cognitive_complexity',
        'avg_function_size',
        'max_function_size',
        'avg_class_size',
        'max_class_size',
        'security_score',
        'security_issues',
        'reliability_score',
        'reliability_issues',
        'performance_score',
        'performance_issues',
        'maintainability_score',
        'maintainability_issues',
        'duplication_percentage',
        'duplicated_blocks',
        'duplicated_lines',
        'documentation_percentage',
        'documented_functions',
        'total_functions',
        'documented_classes',
        'total_classes',
        'test_coverage',
        'branch_coverage',
        'total_tests',
        'total_dependencies',
        'outdated_dependencies',
        'vulnerable_dependencies',
        'overall_score',
        'grade',
        'detailed_analysis',
        'issues',
        'recommendations',
        'top_complex_files',
        'top_security_issues',
        'analysis_duration_seconds',
        'analyzer_version',
    ];

    protected $casts = [
        'analyzed_at' => 'datetime',
        'detailed_analysis' => 'array',
        'issues' => 'array',
        'recommendations' => 'array',
        'top_complex_files' => 'array',
        'top_security_issues' => 'array',
        'avg_cyclomatic_complexity' => 'decimal:2',
        'avg_cognitive_complexity' => 'decimal:2',
        'avg_function_size' => 'decimal:2',
        'avg_class_size' => 'decimal:2',
        'security_score' => 'decimal:2',
        'reliability_score' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'maintainability_score' => 'decimal:2',
        'duplication_percentage' => 'decimal:2',
        'documentation_percentage' => 'decimal:2',
        'test_coverage' => 'decimal:2',
        'branch_coverage' => 'decimal:2',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Get the grade color for display
     */
    public function getGradeColorAttribute(): string
    {
        return match($this->grade) {
            'A+', 'A' => 'success',
            'B+', 'B' => 'info',
            'C+', 'C' => 'warning',
            'D' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the grade icon
     */
    public function getGradeIconAttribute(): string
    {
        return match($this->grade) {
            'A+', 'A' => '🏆',
            'B+', 'B' => '✅',
            'C+', 'C' => '⚠️',
            'D', 'F' => '❌',
            default => '❓',
        };
    }

    /**
     * Get quality status
     */
    public function getQualityStatusAttribute(): string
    {
        if ($this->overall_score >= 90) return 'ممتاز';
        if ($this->overall_score >= 80) return 'جيد جداً';
        if ($this->overall_score >= 70) return 'جيد';
        if ($this->overall_score >= 60) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Scope for latest analysis
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('analyzed_at', 'desc')->first();
    }

    /**
     * Scope for version
     */
    public function scopeForVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Get trend comparison with previous version
     */
    public function getTrendComparison(): ?array
    {
        $previous = self::where('analyzed_at', '<', $this->analyzed_at)
            ->orderBy('analyzed_at', 'desc')
            ->first();

        if (!$previous) {
            return null;
        }

        return [
            'overall_score' => [
                'current' => $this->overall_score,
                'previous' => $previous->overall_score,
                'change' => $this->overall_score - $previous->overall_score,
                'percentage' => $previous->overall_score > 0 
                    ? (($this->overall_score - $previous->overall_score) / $previous->overall_score) * 100 
                    : 0,
            ],
            'security_score' => [
                'current' => $this->security_score,
                'previous' => $previous->security_score,
                'change' => $this->security_score - $previous->security_score,
            ],
            'reliability_score' => [
                'current' => $this->reliability_score,
                'previous' => $previous->reliability_score,
                'change' => $this->reliability_score - $previous->reliability_score,
            ],
            'performance_score' => [
                'current' => $this->performance_score,
                'previous' => $previous->performance_score,
                'change' => $this->performance_score - $previous->performance_score,
            ],
            'maintainability_score' => [
                'current' => $this->maintainability_score,
                'previous' => $previous->maintainability_score,
                'change' => $this->maintainability_score - $previous->maintainability_score,
            ],
        ];
    }

    /**
     * Get critical issues count
     */
    public function getCriticalIssuesCountAttribute(): int
    {
        $issues = $this->issues ?? [];
        return collect($issues)->where('severity', 'critical')->count();
    }

    /**
     * Get high issues count
     */
    public function getHighIssuesCountAttribute(): int
    {
        $issues = $this->issues ?? [];
        return collect($issues)->where('severity', 'high')->count();
    }
}
