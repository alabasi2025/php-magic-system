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
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-warehouse me-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        إدارة المخازن
                    </h2>
                    <p class="text-muted mb-0">إدارة وتتبع جميع المخازن في النظام</p>
                </div>
                <a href="{{ route('inventory.warehouses.create') }}" class="btn btn-inventory-primary">
                    <i class="fas fa-plus me-2"></i>
                    إضافة مخزن جديد
                </a>
            </div>
        </div>
    </div>

    <!-- رسائل النجاح والخطأ المحسنة -->
    @if(session('success'))
        <div class="alert-inventory alert-inventory-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div class="flex-grow-1">
                    <strong>رائع!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-inventory alert-inventory-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                <div class="flex-grow-1">
                    <strong>تنبيه!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- قسم البحث والفلترة المحسن -->
    <div class="filter-section fade-in">
        <h5>
            <i class="fas fa-filter"></i>
            البحث والفلترة
        </h5>
        <form method="GET" action="{{ route('inventory.warehouses.index') }}" class="row g-3">
            <div class="col-md-5">
                <div class="form-group-enhanced">
                    <label>
                        <i class="fas fa-search me-1"></i>
                        البحث
                    </label>
                    <input type="text" name="search" class="form-control-enhanced search-box" 
                           placeholder="ابحث بالاسم أو الرمز أو الموقع..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group-enhanced">
                    <label>
                        <i class="fas fa-toggle-on me-1"></i>
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
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-inventory-primary w-100">
                        <i class="fas fa-search me-1"></i>
                        بحث
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group-enhanced">
                    <label>&nbsp;</label>
                    <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-1"></i>
                        إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- الجدول المحسن -->
    <div class="inventory-table fade-in">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-barcode me-1"></i>
                            الرمز
                        </th>
                        <th>
                            <i class="fas fa-warehouse me-1"></i>
                            الاسم
                        </th>
                        <th>
                            <i class="fas fa-map-marker-alt me-1"></i>
                            الموقع
                        </th>
                        <th>
                            <i class="fas fa-user-tie me-1"></i>
                            المسؤول
                        </th>
                        <th>
                            <i class="fas fa-toggle-on me-1"></i>
                            الحالة
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>
                            تاريخ الإنشاء
                        </th>
                        <th class="text-center">
                            <i class="fas fa-cog me-1"></i>
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $warehouse->code }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <strong>{{ $warehouse->name }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($warehouse->location)
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $warehouse->location }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($warehouse->manager)
                                    <i class="fas fa-user-circle text-primary me-1"></i>
                                    {{ $warehouse->manager->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($warehouse->status == 'active')
                                    <span class="badge-inventory badge-inventory-active">
                                        <i class="fas fa-check-circle me-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="badge-inventory badge-inventory-inactive">
                                        <i class="fas fa-times-circle me-1"></i>
                                        معطل
                                    </span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ $warehouse->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('inventory.warehouses.show', $warehouse) }}" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.warehouses.edit', $warehouse) }}" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventory.warehouses.destroy', $warehouse) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟\n\nسيتم حذف جميع البيانات المرتبطة به.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm" 
                                                style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <h3>لا توجد مخازن مسجلة</h3>
                                    <p>ابدأ بإضافة مخزن جديد لتتبع مخزونك</p>
                                    <a href="{{ route('inventory.warehouses.create') }}" class="btn btn-inventory-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        إضافة مخزن جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($warehouses->hasPages())
            <div class="p-3 bg-white border-top">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
