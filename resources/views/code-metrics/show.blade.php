@extends('layouts.app')

@section('title', 'تقرير تفصيلي - Code Metrics')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>
                        <i class="fas fa-file-alt me-2"></i>
                        تقرير تفصيلي لجودة الكود
                    </h2>
                    <p class="text-muted mb-0">
                        الإصدار: <code>{{ $metric->version }}</code> | 
                        تاريخ التحليل: {{ $metric->analyzed_at->format('Y-m-d H:i:s') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('code-metrics.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        العودة
                    </a>
                    <a href="{{ route('code-metrics.export', $metric->id) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>
                        تصدير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        الملخص التنفيذي
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">النتيجة الإجمالية</h6>
                            <div class="text-center mb-4">
                                <span class="badge bg-{{ $metric->grade_color }} fs-1 px-4 py-3">
                                    {{ $metric->grade_icon }} {{ $metric->grade }}
                                </span>
                                <h2 class="mt-3">{{ $metric->overall_score }}/100</h2>
                                <p class="text-muted">{{ $metric->quality_status }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">إحصائيات عامة</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>إجمالي الملفات:</td>
                                    <td><strong>{{ number_format($metric->total_files) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>إجمالي الأسطر:</td>
                                    <td><strong>{{ number_format($metric->total_lines) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>الأسطر المنطقية:</td>
                                    <td><strong>{{ number_format($metric->logical_lines) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>أسطر التعليقات:</td>
                                    <td><strong>{{ number_format($metric->comment_lines) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>مدة التحليل:</td>
                                    <td><strong>{{ $metric->analysis_duration_seconds }}s</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        المشاكل
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>حرجة:</span>
                        <span class="badge bg-danger">{{ $metric->critical_issues_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>عالية:</span>
                        <span class="badge bg-warning">{{ $metric->high_issues_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>أمان:</span>
                        <span class="badge bg-danger">{{ $metric->security_issues }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>موثوقية:</span>
                        <span class="badge bg-success">{{ $metric->reliability_issues }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>أداء:</span>
                        <span class="badge bg-warning">{{ $metric->performance_issues }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>صيانة:</span>
                        <span class="badge bg-info">{{ $metric->maintainability_issues }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ISO 5055 Detailed Scores -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        تفصيل معايير ISO/IEC 5055:2021
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Security -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">
                                <i class="fas fa-shield-alt text-danger me-2"></i>
                                الأمان (Security) - وزن 30%
                            </h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $metric->security_score }}%">
                                    <strong>{{ $metric->security_score }}/100</strong>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">
                                عدد المشاكل: {{ $metric->security_issues }}
                            </p>
                        </div>

                        <!-- Reliability -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                الموثوقية (Reliability) - وزن 25%
                            </h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $metric->reliability_score }}%">
                                    <strong>{{ $metric->reliability_score }}/100</strong>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">
                                عدد المشاكل: {{ $metric->reliability_issues }}
                            </p>
                        </div>

                        <!-- Performance -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">
                                <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                كفاءة الأداء (Performance) - وزن 20%
                            </h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $metric->performance_score }}%">
                                    <strong>{{ $metric->performance_score }}/100</strong>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">
                                عدد المشاكل: {{ $metric->performance_issues }}
                            </p>
                        </div>

                        <!-- Maintainability -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">
                                <i class="fas fa-wrench text-info me-2"></i>
                                قابلية الصيانة (Maintainability) - وزن 25%
                            </h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $metric->maintainability_score }}%">
                                    <strong>{{ $metric->maintainability_score }}/100</strong>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">
                                عدد المشاكل: {{ $metric->maintainability_issues }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complexity Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram me-2"></i>
                        مقاييس التعقيد
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>متوسط التعقيد الدوري:</td>
                            <td><strong>{{ $metric->avg_cyclomatic_complexity }}</strong></td>
                            <td>
                                @if($metric->avg_cyclomatic_complexity <= 10)
                                    <span class="badge bg-success">ممتاز</span>
                                @elseif($metric->avg_cyclomatic_complexity <= 20)
                                    <span class="badge bg-warning">مقبول</span>
                                @else
                                    <span class="badge bg-danger">عالي</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>أعلى تعقيد دوري:</td>
                            <td><strong>{{ $metric->max_cyclomatic_complexity }}</strong></td>
                            <td>
                                @if($metric->max_cyclomatic_complexity <= 20)
                                    <span class="badge bg-success">جيد</span>
                                @elseif($metric->max_cyclomatic_complexity <= 50)
                                    <span class="badge bg-warning">يحتاج مراجعة</span>
                                @else
                                    <span class="badge bg-danger">حرج</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>متوسط التعقيد المعرفي:</td>
                            <td><strong>{{ $metric->avg_cognitive_complexity }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>متوسط حجم الدالة:</td>
                            <td><strong>{{ $metric->avg_function_size }} سطر</strong></td>
                            <td>
                                @if($metric->avg_function_size <= 50)
                                    <span class="badge bg-success">جيد</span>
                                @else
                                    <span class="badge bg-warning">كبير</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>أكبر دالة:</td>
                            <td><strong>{{ $metric->max_function_size }} سطر</strong></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-code me-2"></i>
                        مقاييس الجودة
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>نسبة التوثيق:</td>
                            <td><strong>{{ $metric->documentation_percentage }}%</strong></td>
                            <td>
                                @if($metric->documentation_percentage >= 20)
                                    <span class="badge bg-success">جيد</span>
                                @elseif($metric->documentation_percentage >= 10)
                                    <span class="badge bg-warning">مقبول</span>
                                @else
                                    <span class="badge bg-danger">منخفض</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>إجمالي الدوال:</td>
                            <td><strong>{{ number_format($metric->total_functions) }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>إجمالي الفئات:</td>
                            <td><strong>{{ number_format($metric->total_classes) }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>متوسط حجم الفئة:</td>
                            <td><strong>{{ $metric->avg_class_size }} سطر</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>أكبر فئة:</td>
                            <td><strong>{{ $metric->max_class_size }} سطر</strong></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if($metric->recommendations && count($metric->recommendations) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        التوصيات الذكية المدعومة بالذكاء الاصطناعي
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($metric->recommendations as $index => $recommendation)
                    <div class="card mb-3 border-{{ $recommendation['priority'] === 'critical' ? 'danger' : ($recommendation['priority'] === 'high' ? 'warning' : 'info') }}">
                        <div class="card-header bg-{{ $recommendation['priority'] === 'critical' ? 'danger' : ($recommendation['priority'] === 'high' ? 'warning' : 'info') }} text-white">
                            <h6 class="mb-0">
                                <span class="badge bg-white text-dark me-2">{{ $index + 1 }}</span>
                                {{ $recommendation['title'] }}
                                <span class="badge bg-white text-dark float-end">{{ $recommendation['category'] }}</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">{{ $recommendation['description'] }}</p>
                            <h6 class="mb-2">خطوات التنفيذ:</h6>
                            <ol class="mb-0">
                                @foreach($recommendation['actions'] as $action)
                                <li>{{ $action }}</li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Trend Comparison -->
    @if($trend)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        مقارنة مع التحليل السابق
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <h6>النتيجة الإجمالية</h6>
                            <h3>
                                {{ $trend['overall_score']['current'] }}
                                @if($trend['overall_score']['change'] > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                @elseif($trend['overall_score']['change'] < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                @else
                                    <i class="fas fa-minus text-muted"></i>
                                @endif
                            </h3>
                            <small class="text-muted">
                                التغيير: {{ number_format($trend['overall_score']['change'], 2) }}
                            </small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>الأمان</h6>
                            <h3>
                                {{ $trend['security_score']['current'] }}
                                @if($trend['security_score']['change'] > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                @elseif($trend['security_score']['change'] < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                @endif
                            </h3>
                            <small class="text-muted">
                                التغيير: {{ number_format($trend['security_score']['change'], 2) }}
                            </small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>الموثوقية</h6>
                            <h3>
                                {{ $trend['reliability_score']['current'] }}
                                @if($trend['reliability_score']['change'] > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                @elseif($trend['reliability_score']['change'] < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                @endif
                            </h3>
                            <small class="text-muted">
                                التغيير: {{ number_format($trend['reliability_score']['change'], 2) }}
                            </small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>الأداء</h6>
                            <h3>
                                {{ $trend['performance_score']['current'] }}
                                @if($trend['performance_score']['change'] > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                @elseif($trend['performance_score']['change'] < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                @endif
                            </h3>
                            <small class="text-muted">
                                التغيير: {{ number_format($trend['performance_score']['change'], 2) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
