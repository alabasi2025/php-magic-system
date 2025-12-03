@extends('layouts.app')

@section('title', 'الدليل الرئيسي - ' . $unit->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-sitemap text-primary"></i>
                الدليل الرئيسي
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-building"></i>
                {{ $unit->name }} ({{ $unit->code }})
            </p>
        </div>
        <div>
            <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                إضافة دليل فرعي جديد
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Master Chart Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i>
                معلومات الدليل الرئيسي
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-2"><strong>الكود:</strong></p>
                    <p class="text-muted">{{ $masterChart->code }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>الاسم:</strong></p>
                    <p class="text-muted">{{ $masterChart->name }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>النوع:</strong></p>
                    <p class="text-muted">
                        <span class="badge bg-info">{{ $masterChart->type_label }}</span>
                    </p>
                </div>
                <div class="col-md-3">
                    <p class="mb-2"><strong>الحالة:</strong></p>
                    <p class="text-muted">
                        @if($masterChart->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Intermediate Master Chart -->
    <div class="card mb-4 shadow-sm border-info">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt"></i>
                    دليل الحسابات الوسيطة
                </h5>
                <a href="{{ route('master-chart.intermediate', $unit->id) }}" class="btn btn-sm btn-light">
                    <i class="fas fa-eye"></i>
                    عرض التفاصيل
                </a>
            </div>
        </div>
        <div class="card-body">
            <p class="mb-3">
                <i class="fas fa-info-circle text-info"></i>
                يحتوي على فروع تلقائية لكل دليل فرعي، مرتبطة بالصناديق والبنوك والمحافظ
            </p>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>عدد الفروع:</strong></p>
                    <p class="text-muted">{{ $intermediateBranches->count() }} فرع</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>عدد الحسابات:</strong></p>
                    <p class="text-muted">{{ $intermediateMaster->accounts_count }} حساب</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Charts -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-folder-tree"></i>
                الأدلة الفرعية ({{ $subCharts->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($subCharts->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد أدلة فرعية حالياً</p>
                    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        إنشاء دليل فرعي الآن
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الكود</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>عدد الحسابات</th>
                                <th>فرع الحسابات الوسيطة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subCharts as $chart)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $chart->code }}</span>
                                    </td>
                                    <td>
                                        <i class="{{ $chart->icon ?? $chart->default_icon }} text-{{ $chart->color ?? $chart->default_color }}"></i>
                                        {{ $chart->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $chart->type_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $chart->accounts_count }}</span>
                                    </td>
                                    <td>
                                        @if($chart->intermediateBranches->isNotEmpty())
                                            <i class="fas fa-check-circle text-success"></i>
                                            موجود
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                            غير موجود
                                        @endif
                                    </td>
                                    <td>
                                        @if($chart->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('chart-of-accounts.show', $chart->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
