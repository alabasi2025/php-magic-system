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
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-left: 15px;">
                        <i class="fas fa-boxes fa-2x text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">إدارة الأصناف</h2>
                        <p class="text-muted mb-0">إدارة وتتبع جميع الأصناف في النظام</p>
                    </div>
                </div>
                <a href="{{ route('inventory.items.create') }}" class="btn btn-inventory-primary" style="padding: 12px 30px;">
                    <i class="fas fa-plus me-2"></i>
                    إضافة صنف جديد
                </a>
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
                <i class="fas fa-filter me-2" style="color: #4facfe;"></i>
                البحث والفلترة
            </h5>
            <form method="GET" action="{{ route('inventory.items.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-search me-2 text-primary"></i>
                            البحث
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control-enhanced" 
                               placeholder="ابحث بالاسم أو SKU أو الباركود" 
                               value="{{ request('search') }}">
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
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group-enhanced">
                        <label>
                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                            فلترة
                        </label>
                        <div class="form-check mt-2" style="padding: 10px; background: rgba(245, 87, 108, 0.05); border-radius: 8px;">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="below_min_stock" 
                                   value="1" 
                                   id="below_min_stock" 
                                   {{ request('below_min_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="below_min_stock">
                                أقل من الحد الأدنى
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-inventory-primary w-100">
                        <i class="fas fa-search me-1"></i>
                        بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
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
                            <th><i class="fas fa-barcode me-2 text-primary"></i>SKU</th>
                            <th><i class="fas fa-box me-2 text-success"></i>الاسم</th>
                            <th><i class="fas fa-balance-scale me-2 text-info"></i>الوحدة</th>
                            <th><i class="fas fa-cubes me-2 text-warning"></i>المخزون الحالي</th>
                            <th><i class="fas fa-arrow-down me-2 text-danger"></i>الحد الأدنى</th>
                            <th><i class="fas fa-arrow-up me-2 text-success"></i>الحد الأقصى</th>
                            <th><i class="fas fa-dollar-sign me-2 text-success"></i>السعر</th>
                            <th><i class="fas fa-toggle-on me-2 text-info"></i>الحالة</th>
                            <th class="text-center"><i class="fas fa-cog me-2"></i>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="{{ $item->current_stock < $item->min_stock ? 'table-row-warning' : '' }}">
                                <td><strong style="color: #667eea;">{{ $item->sku }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;">
                                        @else
                                            <div class="me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                        @endif
                                        <span>{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 6px 12px;">
                                        {{ $item->unit->name }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="font-size: 1.1em; color: {{ $item->current_stock < $item->min_stock ? '#f5576c' : '#11998e' }};">
                                        {{ number_format($item->current_stock, 2) }}
                                    </strong>
                                    @if($item->current_stock < $item->min_stock)
                                        <i class="fas fa-exclamation-triangle text-warning ms-1" 
                                           title="أقل من الحد الأدنى"
                                           style="animation: pulse 2s infinite;"></i>
                                    @endif
                                </td>
                                <td>{{ number_format($item->min_stock, 2) }}</td>
                                <td>{{ number_format($item->max_stock, 2) }}</td>
                                <td>
                                    <strong style="color: #11998e;">
                                        {{ number_format($item->unit_price, 2) }} ريال
                                    </strong>
                                </td>
                                <td>
                                    @if($item->status == 'active')
                                        <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 6px 12px;">
                                            <i class="fas fa-check-circle me-1"></i>
                                            نشط
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" style="padding: 6px 12px;">
                                            <i class="fas fa-times-circle me-1"></i>
                                            معطل
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('inventory.items.show', $item) }}" 
                                           class="btn btn-sm" 
                                           style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;"
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.items.edit', $item) }}" 
                                           class="btn btn-sm" 
                                           style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.items.destroy', $item) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white; border: none;"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div style="padding: 40px;">
                                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                            <i class="fas fa-inbox fa-3x" style="color: #667eea;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">لا توجد أصناف مسجلة</h5>
                                        <p class="text-muted">ابدأ بإضافة أصناف جديدة إلى النظام</p>
                                        <a href="{{ route('inventory.items.create') }}" class="btn btn-inventory-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>
                                            إضافة صنف جديد
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($items->hasPages())
            <div class="card-footer bg-white" style="border-top: 2px solid #e9ecef;">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.table-row-warning {
    background: rgba(245, 87, 108, 0.05);
    border-right: 3px solid #f5576c;
}

.enhanced-table thead tr {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
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
    background: rgba(102, 126, 234, 0.03);
    transform: translateX(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>
@endpush
@endsection
