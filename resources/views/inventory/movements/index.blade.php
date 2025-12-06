@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    حركات المخزون
                </h2>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-plus me-1"></i>
                        إضافة حركة جديدة
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create', ['type' => 'stock_in']) }}">
                            <i class="fas fa-arrow-down text-success me-2"></i>إدخال بضاعة
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create', ['type' => 'stock_out']) }}">
                            <i class="fas fa-arrow-up text-danger me-2"></i>إخراج بضاعة
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create', ['type' => 'transfer']) }}">
                            <i class="fas fa-exchange-alt text-primary me-2"></i>نقل بين المخازن
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create', ['type' => 'adjustment']) }}">
                            <i class="fas fa-balance-scale text-warning me-2"></i>تسوية مخزون
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create', ['type' => 'return']) }}">
                            <i class="fas fa-undo text-info me-2"></i>إرجاع بضاعة
                        </a></li>
                    </ul>
                </div>
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
            <form method="GET" action="{{ route('stock-movements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="رقم الحركة" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="movement_type" class="form-select">
                        <option value="">جميع الأنواع</option>
                        <option value="stock_in" {{ request('movement_type') == 'stock_in' ? 'selected' : '' }}>إدخال</option>
                        <option value="stock_out" {{ request('movement_type') == 'stock_out' ? 'selected' : '' }}>إخراج</option>
                        <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>نقل</option>
                        <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                        <option value="return" {{ request('movement_type') == 'return' ? 'selected' : '' }}>إرجاع</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="warehouse_id" class="form-select">
                        <option value="">جميع المخازن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary w-100">
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
                            <th>رقم الحركة</th>
                            <th>النوع</th>
                            <th>المخزن</th>
                            <th>الصنف</th>
                            <th>الكمية</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td><strong>{{ $movement->movement_number }}</strong></td>
                                <td>
                                    @if($movement->movement_type == 'stock_in')
                                        <span class="badge bg-success">إدخال</span>
                                    @elseif($movement->movement_type == 'stock_out')
                                        <span class="badge bg-danger">إخراج</span>
                                    @elseif($movement->movement_type == 'transfer')
                                        <span class="badge bg-primary">نقل</span>
                                    @elseif($movement->movement_type == 'adjustment')
                                        <span class="badge bg-warning">تسوية</span>
                                    @else
                                        <span class="badge bg-info">إرجاع</span>
                                    @endif
                                </td>
                                <td>{{ $movement->warehouse->name }}</td>
                                <td>{{ $movement->item->name }}</td>
                                <td>{{ number_format($movement->quantity, 2) }}</td>
                                <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($movement->status == 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif($movement->status == 'approved')
                                        <span class="badge bg-success">معتمد</span>
                                    @else
                                        <span class="badge bg-danger">مرفوض</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($movement->status == 'pending')
                                            <form action="{{ route('stock-movements.approve', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success" title="اعتماد" onclick="return confirm('هل أنت متأكد من اعتماد هذه الحركة؟')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('stock-movements.destroy', $movement) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    لا توجد حركات مخزون مسجلة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($movements->hasPages())
            <div class="card-footer bg-white">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
