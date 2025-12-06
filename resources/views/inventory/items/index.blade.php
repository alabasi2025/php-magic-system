@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    إدارة الأصناف
                </h2>
                <a href="{{ route('items.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    إضافة صنف جديد
                </a>
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
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <form method="GET" action="{{ route('items.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="البحث بالاسم أو SKU أو الباركود" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="below_min_stock" value="1" id="below_min_stock" {{ request('below_min_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="below_min_stock">
                            أقل من الحد الأدنى
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search me-1"></i>
                        بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-1"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>الاسم</th>
                            <th>الوحدة</th>
                            <th>المخزون الحالي</th>
                            <th>الحد الأدنى</th>
                            <th>الحد الأقصى</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="{{ $item->current_stock < $item->min_stock ? 'table-warning' : '' }}">
                                <td><strong>{{ $item->sku }}</strong></td>
                                <td>
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="me-2" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;">
                                    @endif
                                    {{ $item->name }}
                                </td>
                                <td>{{ $item->unit->name }}</td>
                                <td>
                                    <strong>{{ number_format($item->current_stock, 2) }}</strong>
                                    @if($item->current_stock < $item->min_stock)
                                        <i class="fas fa-exclamation-triangle text-warning ms-1" title="أقل من الحد الأدنى"></i>
                                    @endif
                                </td>
                                <td>{{ number_format($item->min_stock, 2) }}</td>
                                <td>{{ number_format($item->max_stock, 2) }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    @if($item->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    لا توجد أصناف مسجلة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($items->hasPages())
            <div class="card-footer bg-white">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
