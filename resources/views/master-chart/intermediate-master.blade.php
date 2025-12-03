@extends('layouts.app')

@section('title', 'دليل الحسابات الوسيطة - ' . $unit->name)

@section('content')
<div class="container-fluid">
    <!-- Header with Gradient -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="card-body text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-exchange-alt me-2"></i>
                        دليل الحسابات الوسيطة
                    </h2>
                    <p class="mb-0 opacity-90">
                        <i class="fas fa-building me-2"></i>
                        {{ $unit->name }} <span class="badge bg-white bg-opacity-25 ms-2">{{ $unit->code }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('master-chart.show', $unit->id) }}" class="btn btn-light btn-lg shadow-sm">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للدليل الرئيسي
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

    <!-- Info Banner -->
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="fas fa-lightbulb fa-2x"></i>
            </div>
            <div>
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-info-circle me-2"></i>
                    نبذة عن دليل الحسابات الوسيطة
                </h5>
                <p class="mb-0">
                    <strong>الفكرة:</strong> دليل الحسابات الوسيطة هو دليل خاص يحتوي على فروع تلقائية لكل دليل فرعي في الوحدة. 
                    كل فرع يحتوي على الحسابات الوسيطة المرتبطة بالصناديق، البنوك، المحافظ، والصرافات.
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-folder-tree fa-3x opacity-75"></i>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $branches->count() }}</h2>
                    <p class="mb-0 opacity-90">عدد الفروع</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-list fa-3x opacity-75"></i>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $intermediateMaster->accounts_count }}</h2>
                    <p class="mb-0 opacity-90">عدد الحسابات</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <div class="card-body text-white text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-link fa-3x opacity-75"></i>
                    </div>
                    <h2 class="mb-2 fw-bold">{{ $branches->sum(function($b) { return $b->accounts->where('is_linked', true)->count(); }) }}</h2>
                    <p class="mb-0 opacity-90">الحسابات المرتبطة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Branches Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-folder-tree text-primary me-2"></i>
                فروع دليل الحسابات الوسيطة
            </h5>
        </div>
        <div class="card-body">
            @if($branches->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-folder-open fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-3">لا توجد فروع حالياً</h5>
                    <p class="text-muted mb-4">سيتم إنشاء فروع تلقائياً عند إضافة أدلة فرعية جديدة</p>
                    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        إضافة دليل فرعي جديد
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($branches as $branch)
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100 hover-shadow">
                                <div class="card-body p-4">
                                    <!-- Branch Header -->
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                            <i class="{{ $branch->icon ?? 'fas fa-book' }} fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 fw-bold">{{ $branch->name }}</h5>
                                            <p class="text-muted mb-2 small">
                                                <i class="fas fa-code me-1"></i>
                                                {{ $branch->code }}
                                            </p>
                                            @if($branch->sourceGroup)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-link me-1"></i>
                                                    مرتبط بـ: {{ $branch->sourceGroup->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Branch Stats -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="p-3 rounded text-center" style="background-color: #f8f9fa;">
                                                <h4 class="mb-1 text-primary fw-bold">{{ $branch->accounts->count() }}</h4>
                                                <small class="text-muted">عدد الحسابات</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 rounded text-center" style="background-color: #f8f9fa;">
                                                <h4 class="mb-1 text-success fw-bold">{{ $branch->accounts->where('is_linked', true)->count() }}</h4>
                                                <small class="text-muted">حسابات مرتبطة</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Branch Accounts -->
                                    @if($branch->accounts->isNotEmpty())
                                        <div class="border-top pt-3">
                                            <h6 class="mb-3 text-muted small">
                                                <i class="fas fa-list me-2"></i>
                                                الحسابات الوسيطة
                                            </h6>
                                            <div class="list-group list-group-flush">
                                                @foreach($branch->accounts->take(5) as $account)
                                                    <div class="list-group-item px-0 py-2 border-0">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center">
                                                                <div class="rounded-circle bg-{{ $account->is_linked ? 'success' : 'secondary' }} bg-opacity-10 p-2 me-2">
                                                                    <i class="fas fa-{{ $account->is_linked ? 'check' : 'circle' }} text-{{ $account->is_linked ? 'success' : 'secondary' }} small"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="d-block fw-bold">{{ $account->name }}</small>
                                                                    <small class="text-muted">{{ $account->code }}</small>
                                                                </div>
                                                            </div>
                                                            @if($account->is_linked)
                                                                <span class="badge bg-success small">مرتبط</span>
                                                            @else
                                                                <span class="badge bg-secondary small">متاح</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($branch->accounts->count() > 5)
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted">
                                                            و {{ $branch->accounts->count() - 5 }} حساب آخر...
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3 border-top">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                لا توجد حسابات وسيطة في هذا الفرع
                                            </small>
                                        </div>
                                    @endif

                                    <!-- View Details Button -->
                                    @if($branch->sourceGroup)
                                        <div class="mt-3">
                                            <a href="{{ route('chart-of-accounts.show', $branch->sourceGroup->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fas fa-eye me-2"></i>
                                                عرض تفاصيل الدليل الأصلي
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}
</style>
@endsection
