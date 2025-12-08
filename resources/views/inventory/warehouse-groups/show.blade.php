@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('inventory.dashboard') }}">المخزون</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventory.warehouse-groups.index') }}">مجموعات المخازن</a></li>
                <li class="breadcrumb-item active">{{ $warehouseGroup->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-layer-group text-primary me-2"></i>
                    {{ $warehouseGroup->name }}
                </h2>
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary">{{ $warehouseGroup->code }}</span>
                    @if($warehouseGroup->status === 'active')
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-danger">غير نشط</span>
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('inventory.warehouse-groups.edit', $warehouseGroup) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    تعديل
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Group Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 modern-card mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات المجموعة
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">كود المجموعة</label>
                            <p class="mb-0"><strong>{{ $warehouseGroup->code }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">اسم المجموعة</label>
                            <p class="mb-0"><strong>{{ $warehouseGroup->name }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الحساب المحاسبي</label>
                            <p class="mb-0">
                                @if($warehouseGroup->account)
                                    <span class="badge bg-info">{{ $warehouseGroup->account->code }}</span>
                                    {{ $warehouseGroup->account->name }}
                                @else
                                    <span class="text-muted">غير مرتبط</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الحالة</label>
                            <p class="mb-0">
                                @if($warehouseGroup->status === 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </p>
                        </div>
                        @if($warehouseGroup->description)
                            <div class="col-12">
                                <label class="text-muted small">الوصف</label>
                                <p class="mb-0">{{ $warehouseGroup->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Warehouses in Group -->
            <div class="card shadow-sm border-0 modern-card">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        المخازن في هذه المجموعة ({{ $warehouseGroup->warehouses->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($warehouseGroup->warehouses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">الكود</th>
                                        <th class="py-3">اسم المخزن</th>
                                        <th class="py-3">الموقع</th>
                                        <th class="py-3">المدير</th>
                                        <th class="py-3 text-center">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($warehouseGroup->warehouses as $warehouse)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-secondary">{{ $warehouse->code }}</span>
                                            </td>
                                            <td class="py-3">
                                                <a href="{{ route('inventory.warehouses.show', $warehouse) }}" class="text-decoration-none">
                                                    <strong>{{ $warehouse->name }}</strong>
                                                </a>
                                            </td>
                                            <td class="py-3">
                                                <small class="text-muted">{{ $warehouse->location ?? 'غير محدد' }}</small>
                                            </td>
                                            <td class="py-3">
                                                @if($warehouse->manager)
                                                    <i class="fas fa-user-tie text-muted me-1"></i>
                                                    {{ $warehouse->manager->name }}
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                @if($warehouse->status === 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد مخازن في هذه المجموعة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 modern-card mb-3">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        إحصائيات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted d-block">عدد المخازن</small>
                            <h4 class="mb-0 text-primary">{{ $warehouseGroup->warehouses->count() }}</h4>
                        </div>
                        <i class="fas fa-warehouse fa-2x text-primary opacity-50"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">المخازن النشطة</small>
                            <h4 class="mb-0 text-success">{{ $warehouseGroup->warehouses->where('status', 'active')->count() }}</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 modern-card">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        معلومات النظام
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ الإنشاء</small>
                        <strong>{{ $warehouseGroup->created_at->format('Y-m-d H:i') }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">آخر تحديث</small>
                        <strong>{{ $warehouseGroup->updated_at->format('Y-m-d H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modern-card {
    border-radius: 10px;
    overflow: hidden;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endsection
