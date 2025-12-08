@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory-enhanced.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header محسن -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-left: 15px;">
                        <i class="fas fa-exchange-alt fa-2x text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">حركات المخزون</h2>
                        <p class="text-muted mb-0">إدارة ومتابعة جميع حركات المخزون</p>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-inventory-primary dropdown-toggle" data-bs-toggle="dropdown" style="padding: 12px 30px;">
                        <i class="fas fa-plus me-2"></i>
                        إضافة حركة جديدة
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'stock_in']) }}" style="padding: 12px 20px;">
                            <i class="fas fa-arrow-down me-2" style="color: #11998e;"></i>
                            <strong>إدخال بضاعة</strong>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'stock_out']) }}" style="padding: 12px 20px;">
                            <i class="fas fa-arrow-up me-2" style="color: #f5576c;"></i>
                            <strong>إخراج بضاعة</strong>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'transfer']) }}" style="padding: 12px 20px;">
                            <i class="fas fa-exchange-alt me-2" style="color: #4facfe;"></i>
                            <strong>نقل بين المخازن</strong>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'adjustment']) }}" style="padding: 12px 20px;">
                            <i class="fas fa-balance-scale me-2" style="color: #f093fb;"></i>
                            <strong>تسوية مخزون</strong>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'return']) }}" style="padding: 12px 20px;">
                            <i class="fas fa-undo me-2" style="color: #667eea;"></i>
                            <strong>إرجاع بضاعة</strong>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show stat-card" role="alert" style="border-right: 4px solid #11998e;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show stat-card" role="alert" style="border-right: 4px solid #f5576c;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="stat-card fade-in">
        <div class="card-header-enhanced">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2" style="color: #f093fb;"></i>
                البحث والفلترة
            </h5>
            <form method="GET" action="{{ route('inventory.stock-movements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-hashtag me-2 text-primary"></i>
                            رقم الحركة
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control-enhanced" 
                               placeholder="ابحث برقم الحركة" 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-tag me-2 text-info"></i>
                            نوع الحركة
                        </label>
                        <select name="movement_type" class="form-control-enhanced">
                            <option value="">جميع الأنواع</option>
                            <option value="stock_in" {{ request('movement_type') == 'stock_in' ? 'selected' : '' }}>إدخال</option>
                            <option value="stock_out" {{ request('movement_type') == 'stock_out' ? 'selected' : '' }}>إخراج</option>
                            <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>نقل</option>
                            <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                            <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>إرجاع</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-toggle-on me-2 text-success"></i>
                            الحالة
                        </label>
                        <select name="status" class="form-control-enhanced">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-warehouse me-2 text-warning"></i>
                            المخزن
                        </label>
                        <select name="warehouse_id" class="form-control-enhanced">
                            <option value="">جميع المخازن</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <label class="d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-inventory-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <a href="{{ route('inventory.stock-movements.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
                        <i class="fas fa-redo me-1"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 enhanced-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2 text-primary"></i>رقم الحركة</th>
                            <th><i class="fas fa-tag me-2 text-info"></i>النوع</th>
                            <th><i class="fas fa-warehouse me-2 text-warning"></i>المخزن</th>
                            <th><i class="fas fa-box me-2 text-success"></i>الصنف</th>
                            <th><i class="fas fa-cubes me-2 text-danger"></i>الكمية</th>
                            <th><i class="fas fa-calendar me-2 text-primary"></i>التاريخ</th>
                            <th><i class="fas fa-toggle-on me-2 text-info"></i>الحالة</th>
                            <th class="text-center"><i class="fas fa-cog me-2"></i>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td><strong style="color: #667eea;">{{ $movement->movement_number }}</strong></td>
                                <td>
                                    @if($movement->movement_type == 'stock_in')
                                        <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 6px 12px;">
                                            <i class="fas fa-arrow-down me-1"></i>إدخال
                                        </span>
                                    @elseif($movement->movement_type == 'stock_out')
                                        <span class="badge" style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); padding: 6px 12px;">
                                            <i class="fas fa-arrow-up me-1"></i>إخراج
                                        </span>
                                    @elseif($movement->movement_type == 'transfer')
                                        <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 6px 12px;">
                                            <i class="fas fa-exchange-alt me-1"></i>نقل
                                        </span>
                                    @elseif($movement->movement_type == 'adjustment')
                                        <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 6px 12px;">
                                            <i class="fas fa-balance-scale me-1"></i>تسوية
                                        </span>
                                    @else
                                        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 6px 12px;">
                                            <i class="fas fa-undo me-1"></i>إرجاع
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-warehouse me-2" style="color: #f093fb;"></i>
                                    {{ $movement->warehouse->name }}
                                </td>
                                <td>
                                    <i class="fas fa-box me-2" style="color: #4facfe;"></i>
                                    {{ $movement->item->name }}
                                </td>
                                <td>
                                    <strong style="font-size: 1.1em; color: #11998e;">
                                        {{ number_format($movement->quantity, 2) }}
                                    </strong>
                                </td>
                                <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($movement->status == 'pending')
                                        <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 6px 12px;">
                                            <i class="fas fa-clock me-1"></i>قيد الانتظار
                                        </span>
                                    @elseif($movement->status == 'approved')
                                        <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 6px 12px;">
                                            <i class="fas fa-check-circle me-1"></i>معتمد
                                        </span>
                                    @else
                                        <span class="badge" style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); padding: 6px 12px;">
                                            <i class="fas fa-times-circle me-1"></i>مرفوض
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('inventory.stock-movements.show', $movement) }}" 
                                           class="btn btn-sm" 
                                           style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;"
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($movement->status == 'pending')
                                            <form action="{{ route('inventory.stock-movements.approve', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm" 
                                                        style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none;"
                                                        title="اعتماد" 
                                                        onclick="return confirm('هل أنت متأكد من اعتماد هذه الحركة؟')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('inventory.stock-movements.destroy', $movement) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm" 
                                                        style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white; border: none;"
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div style="padding: 40px;">
                                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                            <i class="fas fa-inbox fa-3x" style="color: #f093fb;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">لا توجد حركات مخزون مسجلة</h5>
                                        <p class="text-muted">ابدأ بإضافة حركات مخزون جديدة</p>
                                        <div class="btn-group mt-3">
                                            <button type="button" class="btn btn-inventory-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة حركة جديدة
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'stock_in']) }}">
                                                    <i class="fas fa-arrow-down text-success me-2"></i>إدخال بضاعة
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('inventory.stock-movements.create', ['type' => 'stock_out']) }}">
                                                    <i class="fas fa-arrow-up text-danger me-2"></i>إخراج بضاعة
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($movements->hasPages())
            <div class="card-footer bg-white" style="border-top: 2px solid #e9ecef;">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<style>
.enhanced-table thead tr {
    background: linear-gradient(135deg, rgba(240, 147, 251, 0.05) 0%, rgba(245, 87, 108, 0.05) 100%);
}

.enhanced-table thead th {
    padding: 15px 12px;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.enhanced-table tbody td {
    padding: 15px 12px;
    vertical-align: middle;
}

.enhanced-table tbody tr {
    transition: all 0.3s ease;
}

.enhanced-table tbody tr:hover {
    background: rgba(240, 147, 251, 0.03);
    transform: translateX(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dropdown-menu {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush
@endsection
