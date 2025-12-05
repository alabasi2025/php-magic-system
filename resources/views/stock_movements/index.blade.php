@extends('layouts.app')

@section('content')
<div class="container">
    <h1>سجل حركات المخزون</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('stock_movements.create') }}" class="btn btn-primary">تسجيل حركة جديدة</a>
        <a href="{{ route('stock_movements.item_report') }}" class="btn btn-info">تقرير حركة صنف</a>
        <a href="{{ route('stock_movements.warehouse_report') }}" class="btn btn-info">تقرير حركة مخزن</a>
    </div>

    {{-- نموذج الترشيح (Filters) --}}
    <form method="GET" action="{{ route('stock_movements.index') }}" class="mb-4 p-3 border rounded">
        <div class="row">
            <div class="col-md-3">
                <label for="warehouse_id">المخزن</label>
                <select name="warehouse_id" id="warehouse_id" class="form-control">
                    <option value="">كل المخازن</option>
                    {{-- افترض أن لديك قائمة بالمخازن $allWarehouses --}}
                    {{-- @foreach ($allWarehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-3">
                <label for="item_id">الصنف</label>
                <select name="item_id" id="item_id" class="form-control">
                    <option value="">كل الأصناف</option>
                    {{-- افترض أن لديك قائمة بالأصناف $allItems --}}
                    {{-- @foreach ($allItems as $item)
                        <option value="{{ $item->id }}" {{ ($filters['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-3">
                <label for="movement_type">نوع الحركة</label>
                <select name="movement_type" id="movement_type" class="form-control">
                    <option value="">كل الأنواع</option>
                    @foreach (['in' => 'دخول', 'out' => 'خروج', 'adjustment' => 'تسوية', 'transfer' => 'نقل'] as $key => $value)
                        <option value="{{ $key }}" {{ ($filters['movement_type'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">تطبيق المرشحات</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>المخزن</th>
                <th>الصنف</th>
                <th>النوع</th>
                <th>الكمية</th>
                <th>الرصيد قبل</th>
                <th>الرصيد بعد</th>
                <th>المرجع</th>
                <th>المسجل</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->id }}</td>
                    <td>{{ $movement->date->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->warehouse->name ?? 'N/A' }}</td>
                    <td>{{ $movement->item->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $type = [
                                'in' => 'دخول',
                                'out' => 'خروج',
                                'adjustment' => 'تسوية',
                                'transfer' => 'نقل'
                            ][$movement->movement_type] ?? $movement->movement_type;
                            $class = $movement->quantity > 0 ? 'badge bg-success' : 'badge bg-danger';
                        @endphp
                        <span class="{{ $class }}">{{ $type }}</span>
                    </td>
                    <td>{{ abs($movement->quantity) }}</td>
                    <td>{{ $movement->balance_before }}</td>
                    <td>{{ $movement->balance_after }}</td>
                    <td>{{ $movement->reference_type }} #{{ $movement->reference_id }}</td>
                    <td>{{ $movement->creator->name ?? 'نظام آلي' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">لا توجد حركات مخزون مسجلة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $movements->links() }}
</div>
@endsection
