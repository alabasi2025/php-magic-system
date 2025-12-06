@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    إدارة المخازن
                </h2>
                <a href="{{ route('inventory.warehouses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    إضافة مخزن جديد
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
            <form method="GET" action="{{ route('inventory.warehouses.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="البحث بالاسم أو الرمز أو الموقع" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search me-1"></i>
                        بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>الرمز</th>
                            <th>الاسم</th>
                            <th>الموقع</th>
                            <th>المسؤول</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $warehouse)
                            <tr>
                                <td><strong>{{ $warehouse->code }}</strong></td>
                                <td>{{ $warehouse->name }}</td>
                                <td>{{ $warehouse->location ?? '-' }}</td>
                                <td>{{ $warehouse->manager->name ?? '-' }}</td>
                                <td>
                                    @if($warehouse->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </td>
                                <td>{{ $warehouse->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('inventory.warehouses.show', $warehouse) }}" class="btn btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.warehouses.edit', $warehouse) }}" class="btn btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.warehouses.destroy', $warehouse) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟')">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    لا توجد مخازن مسجلة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($warehouses->hasPages())
            <div class="card-footer bg-white">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
