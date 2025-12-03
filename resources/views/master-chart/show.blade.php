@extends('layouts.app')

@section('title', 'الدليل الرئيسي - ' . $unit->name)

@section('content')
<div class="container-fluid">
    <!-- Header with Gradient -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-sitemap me-2"></i>
                        الدليل الرئيسي
                    </h2>
                    <p class="mb-0 opacity-90">
                        <i class="fas fa-building me-2"></i>
                        {{ $unit->name }} <span class="badge bg-white bg-opacity-25 ms-2">{{ $unit->code }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-light btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        إضافة دليل فرعي جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Master Chart Info Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        معلومات الدليل الرئيسي
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-barcode text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">الكود</small>
                                        <strong class="text-dark">{{ $masterChart->code }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-tag text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">الاسم</small>
                                        <strong class="text-dark">{{ $masterChart->name }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-layer-group text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">النوع</small>
                                        <span class="badge bg-info">{{ $masterChart->type_label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-{{ $masterChart->is_active ? 'success' : 'secondary' }} bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-power-off text-{{ $masterChart->is_active ? 'success' : 'secondary' }}"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">الحالة</small>
                                        @if($masterChart->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Intermediate Master Chart Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-exchange-alt me-2"></i>
                            دليل الحسابات الوسيطة
                        </h5>
                        <a href="{{ route('master-chart.intermediate', $unit->id) }}" class="btn btn-light btn-sm shadow-sm">
                            <i class="fas fa-eye me-1"></i>
                            عرض
                        </a>
                    </div>
                    <p class="mb-4 opacity-90 small">
                        <i class="fas fa-info-circle me-2"></i>
                        يحتوي على فروع تلقائية لكل دليل فرعي، مرتبطة بالصناديق والبنوك والمحافظ
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 rounded bg-white bg-opacity-10">
                                <h3 class="mb-1 fw-bold">{{ $intermediateBranches->count() }}</h3>
                                <small class="opacity-90">عدد الفروع</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded bg-white bg-opacity-10">
                                <h3 class="mb-1 fw-bold">{{ $intermediateMaster->accounts_count }}</h3>
                                <small class="opacity-90">عدد الحسابات</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Charts Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-folder-tree text-warning me-2"></i>
                    الأدلة الفرعية
                    <span class="badge bg-warning text-dark ms-2">{{ $subCharts->count() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body">
            @if($subCharts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-folder-open fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-3">لا توجد أدلة فرعية حالياً</h5>
                    <p class="text-muted mb-4">ابدأ بإنشاء أول دليل فرعي لوحدتك</p>
                    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        إنشاء دليل فرعي الآن
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">الكود</th>
                                <th class="border-0">الاسم</th>
                                <th class="border-0">النوع</th>
                                <th class="border-0 text-center">عدد الحسابات</th>
                                <th class="border-0 text-center">فرع الحسابات الوسيطة</th>
                                <th class="border-0 text-center">الحالة</th>
                                <th class="border-0 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subCharts as $chart)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $chart->code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-{{ $chart->color ?? 'primary' }} bg-opacity-10 p-2 me-3">
                                                <i class="{{ $chart->icon ?? 'fas fa-book' }} text-{{ $chart->color ?? 'primary' }}"></i>
                                            </div>
                                            <strong>{{ $chart->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $chart->type_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">{{ $chart->accounts_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($chart->intermediateBranches->isNotEmpty())
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                موجود
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                غير موجود
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($chart->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('chart-of-accounts.show', $chart->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>
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
