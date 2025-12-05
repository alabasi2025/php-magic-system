@extends('layouts.app')

@section('title', 'تفاصيل المخزن: ' . $warehouse->name)

@section('content')
<div class="container">
    <h1>تفاصيل المخزن: {{ $warehouse->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            بيانات المخزن الأساسية
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الرمز:</strong> {{ $warehouse->code }}</p>
                    <p><strong>الاسم:</strong> {{ $warehouse->name }}</p>
                    <p><strong>المدير:</strong> {{ $warehouse->manager->name ?? 'غير محدد' }}</p>
                    <p><strong>الحالة:</strong>
                        <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">
                            {{ $warehouse->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>الهاتف:</strong> {{ $warehouse->phone ?? 'لا يوجد' }}</p>
                    <p><strong>البريد الإلكتروني:</strong> {{ $warehouse->email ?? 'لا يوجد' }}</p>
                    <p><strong>الموقع:</strong> {{ $warehouse->location ?? 'لا يوجد' }}</p>
                    <p><strong>السعة التخزينية:</strong> {{ $warehouse->capacity ?? 'غير محدد' }}</p>
                </div>
            </div>
            <p><strong>العنوان:</strong> {{ $warehouse->address ?? 'لا يوجد' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            بيانات المخزون
        </div>
        <div class="card-body">
            <p><strong>القيمة الحالية للمخزون:</strong> {{ number_format($warehouse->current_stock_value, 2) }}</p>
            {{-- هنا يمكن إضافة جدول أو تفاصيل للمواد الموجودة في المخزن --}}
        </div>
    </div>

    <div class="d-flex justify-content-start">
        @can('update', $warehouse)
            <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-warning me-2">تعديل</a>
        @endcan
        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
</div>
@endsection
