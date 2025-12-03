@extends('layouts.app')

@section('title', 'مقاييس جودة الكود - Code Metrics v3.21.0')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="text-white mb-2">
                                <i class="fas fa-chart-line me-2"></i>
                                مقاييس جودة الكود
                            </h2>
                            <p class="text-white-50 mb-0">
                                تحليل شامل لجودة الكود بناءً على معايير ISO/IEC 5055:2021
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('code-metrics.analyze') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light btn-lg">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    تحليل جديد
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($latestAnalysis)
        <!-- Overall Score Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <h3 class="text-muted mb-3">النتيجة الإجمالية</h3>
                        <div class="display-1 mb-3">
                            <span class="badge bg-{{ $latestAnalysis->grade_color }} fs-1 px-5 py-3">
                                {{ $latestAnalysis->grade_icon }} {{ $latestAnalysis->grade }}
                            </span>
                        </div>
                        <h2 class="mb-3">{{ $latestAnalysis->overall_score }}/100</h2>
                        <p class="text-muted mb-0">{{ $latestAnalysis->quality_status }}</p>
                        <small class="text-muted">
                            آخر تحليل: {{ $latestAnalysis->analyzed_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ISO 5055 Four Factors -->
        <div class="row mb-4">
            <!-- Security -->
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt text-danger me-2"></i>
                                الأمان
                            </h5>
                            <span class="badge bg-danger">{{ $latestAnalysis->security_issues }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ $latestAnalysis->security_score }}%">
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $latestAnalysis->security_score }}/100</h3>
                        <small class="text-muted">Security</small>
                    </div>
                </div>
            </div>

            <!-- Reliability -->
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                الموثوقية
                            </h5>
                            <span class="badge bg-success">{{ $latestAnalysis->reliability_issues }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $latestAnalysis->reliability_score }}%">
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $latestAnalysis->reliability_score }}/100</h3>
                        <small class="text-muted">Reliability</small>
                    </div>
                </div>
            </div>

            <!-- Performance -->
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                الأداء
                            </h5>
                            <span class="badge bg-warning">{{ $latestAnalysis->performance_issues }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ $latestAnalysis->performance_score }}%">
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $latestAnalysis->performance_score }}/100</h3>
                        <small class="text-muted">Performance</small>
                    </div>
                </div>
            </div>

            <!-- Maintainability -->
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-wrench text-info me-2"></i>
                                قابلية الصيانة
                            </h5>
                            <span class="badge bg-info">{{ $latestAnalysis->maintainability_issues }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $latestAnalysis->maintainability_score }}%">
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $latestAnalysis->maintainability_score }}/100</h3>
                        <small class="text-muted">Maintainability</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إجمالي الملفات</h6>
                        <h3 class="mb-0">{{ number_format($latestAnalysis->total_files) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إجمالي الأسطر</h6>
                        <h3 class="mb-0">{{ number_format($latestAnalysis->total_lines) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">متوسط التعقيد</h6>
                        <h3 class="mb-0">{{ $latestAnalysis->avg_cyclomatic_complexity }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">نسبة التوثيق</h6>
                        <h3 class="mb-0">{{ $latestAnalysis->documentation_percentage }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        @if($latestAnalysis->recommendations && count($latestAnalysis->recommendations) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            التوصيات الذكية
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($latestAnalysis->recommendations as $recommendation)
                        <div class="alert alert-{{ $recommendation['priority'] === 'critical' ? 'danger' : ($recommendation['priority'] === 'high' ? 'warning' : 'info') }} mb-3">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ $recommendation['title'] }}
                            </h6>
                            <p class="mb-2">{{ $recommendation['description'] }}</p>
                            <ul class="mb-0">
                                @foreach($recommendation['actions'] as $action)
                                <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">إجراءات سريعة</h5>
                        <div class="btn-group" role="group">
                            <a href="{{ route('code-metrics.show', $latestAnalysis->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>
                                عرض التقرير الكامل
                            </a>
                            <a href="{{ route('code-metrics.trends') }}" class="btn btn-info">
                                <i class="fas fa-chart-line me-2"></i>
                                عرض الاتجاهات
                            </a>
                            <a href="{{ route('code-metrics.export', $latestAnalysis->id) }}" class="btn btn-success">
                                <i class="fas fa-download me-2"></i>
                                تصدير JSON
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Analysis Yet -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
                        <h3 class="mb-3">لا يوجد تحليل متاح</h3>
                        <p class="text-muted mb-4">قم بإجراء أول تحليل لجودة الكود للحصول على رؤى شاملة</p>
                        <form action="{{ route('code-metrics.analyze') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-play me-2"></i>
                                ابدأ التحليل الآن
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Analysis History -->
    @if($history && $history->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        سجل التحليلات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الإصدار</th>
                                    <th>النتيجة</th>
                                    <th>التقييم</th>
                                    <th>الأمان</th>
                                    <th>الموثوقية</th>
                                    <th>الأداء</th>
                                    <th>الصيانة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $analysis)
                                <tr>
                                    <td>{{ $analysis->analyzed_at->format('Y-m-d H:i') }}</td>
                                    <td><code>{{ $analysis->version }}</code></td>
                                    <td><strong>{{ $analysis->overall_score }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $analysis->grade_color }}">
                                            {{ $analysis->grade }}
                                        </span>
                                    </td>
                                    <td>{{ $analysis->security_score }}</td>
                                    <td>{{ $analysis->reliability_score }}</td>
                                    <td>{{ $analysis->performance_score }}</td>
                                    <td>{{ $analysis->maintainability_score }}</td>
                                    <td>
                                        <a href="{{ route('code-metrics.show', $analysis->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh on analysis completion
    @if(session('analyzing'))
        setTimeout(function() {
            location.reload();
        }, 5000);
    @endif
</script>
@endpush
