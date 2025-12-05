@extends('layouts.app')

@section('title', 'إدارة المخازن')

@section('content')
<div class="container">
    <h1>قائمة المخازن</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        @can('create', App\Models\Warehouse::class)
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary">إضافة مخزن جديد</a>
        @endcan
        @can('viewStatistics', App\Models\Warehouse::class)
            <a href="{{ route('warehouses.statistics') }}" class="btn btn-info">عرض الإحصائيات</a>
        @endcan
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>الرمز</th>
                <th>الاسم</th>
                <th>المدير</th>
                <th>القيمة الحالية</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warehouses as $warehouse)
                <tr>
                    <td>{{ $warehouse->code }}</td>
                    <td>{{ $warehouse->name }}</td>
                    <td>{{ $warehouse->manager->name ?? 'غير محدد' }}</td>
                    <td>{{ number_format($warehouse->current_stock_value, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">
                            {{ $warehouse->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td>
                        @can('view', $warehouse)
                            <a href="{{ route('warehouses.show', $warehouse) }}" class="btn btn-sm btn-secondary">عرض</a>
                        @endcan
                        @can('update', $warehouse)
                            <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-sm btn-warning">تعديل</a>
                        @endcan
                        @can('toggleStatus', $warehouse)
                            <form action="{{ route('warehouses.toggle-status', $warehouse) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $warehouse->is_active ? 'danger' : 'success' }}">
                                    {{ $warehouse->is_active ? 'تعطيل' : 'تفعيل' }}
                                </button>
                            </form>
                        @endcan
                        @can('delete', $warehouse)
                            <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">لا توجد مخازن مسجلة حالياً.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $warehouses->links() }}
    </div>
</div>
@endsection
