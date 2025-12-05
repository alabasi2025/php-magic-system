@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">تفاصيل الصنف: {{ $item->name }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="صورة الصنف" class="img-fluid rounded" style="max-height: 200px;">
                            @else
                                <p class="text-muted">لا توجد صورة</p>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <p><strong>الرمز:</strong> {{ $item->code }}</p>
                            <p><strong>الباركود:</strong> {{ $item->barcode ?? 'لا يوجد' }}</p>
                            <p><strong>الفئة:</strong> {{ $item->category->name ?? 'غير محدد' }}</p>
                            <p><strong>الوحدة:</strong> {{ $item->unit->name ?? 'غير محدد' }}</p>
                            <p><strong>سعر التكلفة:</strong> {{ number_format($item->cost_price, 2) }}</p>
                            <p><strong>سعر البيع:</strong> {{ number_format($item->selling_price, 2) }}</p>
                            <p><strong>الحد الأدنى للمخزون:</strong> {{ $item->min_stock }}</p>
                            <p><strong>مستوى إعادة الطلب:</strong> {{ $item->reorder_level }}</p>
                            <p><strong>الحالة:</strong>
                                <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                    {{ $item->is_active ? 'مفعل' : 'غير مفعل' }}
                                </span>
                            </p>
                            <p><strong>الوصف:</strong> {{ $item->description ?? 'لا يوجد وصف' }}</p>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">تعديل</a>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">العودة إلى القائمة</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
