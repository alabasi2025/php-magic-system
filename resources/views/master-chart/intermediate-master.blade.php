@extends('layouts.app')

@section('title', 'دليل الحسابات الوسيطة - ' . $unit->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-exchange-alt text-info"></i>
                دليل الحسابات الوسيطة
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-building"></i>
                {{ $unit->name }} ({{ $unit->code }})
            </p>
        </div>
        <div>
            <a href="{{ route('master-chart.show', $unit->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                العودة للدليل الرئيسي
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

    <!-- Info Card -->
    <div class="card mb-4 shadow-sm border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i>
                نبذة عن دليل الحسابات الوسيطة
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3">
                <i class="fas fa-lightbulb text-warning"></i>
                <strong>الفكرة:</strong>
                دليل الحسابات الوسيطة هو دليل خاص يحتوي على فروع تلقائية لكل دليل فرعي في الوحدة.
                كل فرع يحتوي على الحسابات الوسيطة المرتبطة بالصناديق، البنوك، المحافظ، والصرافات.
            </p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-folder-tree fa-2x text-primary mb-2"></i>
                            <h6>عدد الفروع</h6>
                            <h3 class="text-primary">{{ $branches->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-list fa-2x text-success mb-2"></i>
                            <h6>عدد الحسابات</h6>
                            <h3 class="text-success">{{ $intermediateMaster->accounts_count }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-link fa-2x text-info mb-2"></i>
                            <h6>الحسابات المرتبطة</h6>
                            <h3 class="text-info">{{ $branches->sum(function($b) { return $b->accounts->where('is_linked', true)->count(); }) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Branches -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-folder-tree"></i>
                فروع دليل الحسابات الوسيطة
            </h5>
        </div>
        <div class="card-body">
            @if($branches->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد فروع حالياً</p>
                    <p class="text-muted small">
                        سيتم إنشاء فروع تلقائياً عند إضافة أدلة فرعية جديدة
                    </p>
                </div>
            @else
                <div class="accordion" id="branchesAccordion">
                    @foreach($branches as $index => $branch)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $branch->id }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $branch->id }}" 
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ $branch->id }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <i class="{{ $branch->icon ?? $branch->default_icon }} text-{{ $branch->color ?? $branch->default_color }}"></i>
                                            <strong>{{ $branch->name }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $branch->code }}</span>
                                        </div>
                                        <div class="me-3">
                                            <span class="badge bg-primary">{{ $branch->accounts->count() }} حساب</span>
                                            @if($branch->sourceGroup)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-link"></i>
                                                    مرتبط بـ {{ $branch->sourceGroup->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $branch->id }}" 
                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                 aria-labelledby="heading{{ $branch->id }}" 
                                 data-bs-parent="#branchesAccordion">
                                <div class="accordion-body">
                                    @if($branch->accounts->isEmpty())
                                        <div class="text-center py-3">
                                            <i class="fas fa-inbox text-muted"></i>
                                            <p class="text-muted mb-0">لا توجد حسابات في هذا الفرع</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>الكود</th>
                                                        <th>الاسم</th>
                                                        <th>النوع</th>
                                                        <th>مخصص لـ</th>
                                                        <th>الحالة</th>
                                                        <th>الربط</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($branch->accounts as $account)
                                                        <tr>
                                                            <td><code>{{ $account->code }}</code></td>
                                                            <td>{{ $account->name }}</td>
                                                            <td>
                                                                <span class="badge bg-info">
                                                                    {{ $account->account_type == 'intermediate' ? 'وسيط' : 'عام' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($account->intermediate_for)
                                                                    <span class="badge bg-warning">
                                                                        {{ match($account->intermediate_for) {
                                                                            'cash_boxes' => 'صناديق',
                                                                            'banks' => 'بنوك',
                                                                            'wallets' => 'محافظ',
                                                                            'atms' => 'صرافات',
                                                                            default => $account->intermediate_for
                                                                        } }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($account->is_active)
                                                                    <span class="badge bg-success">نشط</span>
                                                                @else
                                                                    <span class="badge bg-secondary">غير نشط</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($account->is_linked)
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                    مرتبط
                                                                @else
                                                                    <i class="fas fa-times-circle text-danger"></i>
                                                                    غير مرتبط
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                    
                                    @if($branch->sourceGroup)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-2">
                                                <i class="fas fa-link text-info"></i>
                                                <strong>الدليل الأصلي:</strong>
                                            </p>
                                            <p class="mb-0">
                                                <a href="{{ route('chart-of-accounts.show', $branch->sourceGroup->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    {{ $branch->sourceGroup->name }} ({{ $branch->sourceGroup->code }})
                                                </a>
                                            </p>
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
@endsection
