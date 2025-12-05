@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تفاصيل عملية الجرد: {{ $stockCount->number }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">معلومات أساسية</div>
        <div class="card-body">
            <p><strong>المخزن:</strong> {{ $stockCount->warehouse->name ?? 'N/A' }}</p>
            <p><strong>التاريخ:</strong> {{ $stockCount->date->format('Y-m-d') }}</p>
            <p><strong>الحالة:</strong> <span class="badge bg-{{ $stockCount->status == 'Draft' ? 'warning' : ($stockCount->status == 'Adjusted' ? 'success' : 'info') }}">{{ $stockCount->status }}</span></p>
            <p><strong>المنشئ:</strong> {{ $stockCount->creator->name ?? 'N/A' }}</p>
            <p><strong>الموافق:</strong> {{ $stockCount->approver->name ?? 'N/A' }}</p>
            <p><strong>ملاحظات:</strong> {{ $stockCount->notes ?? 'لا توجد' }}</p>
        </div>
    </div>

    @if ($stockCount->status == 'Pending Approval')
        @can('approve', $stockCount)
            <form action="{{ route('stock_counts.approve', $stockCount) }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('هل أنت متأكد من الموافقة على الجرد وتعديل المخزون؟')">الموافقة على الجرد وتعديل المخزون</button>
            </form>
        @endcan
    @endif

    <h2>تفاصيل الجرد</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الصنف</th>
                <th>كمية النظام</th>
                <th>كمية الفعلية</th>
                <th>الفرق</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockCount->details as $detail)
                <tr class="{{ $detail->difference != 0 ? 'table-danger' : '' }}">
                    <td>{{ $detail->item->name ?? 'N/A' }}</td>
                    <td>{{ $detail->system_quantity }}</td>
                    <td>{{ $detail->actual_quantity ?? 'لم يتم الإدخال' }}</td>
                    <td>{{ $detail->difference }}</td>
                    <td>{{ $detail->notes ?? 'لا توجد' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('stock_counts.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    @can('update', $stockCount)
        <a href="{{ route('stock_counts.edit', $stockCount) }}" class="btn btn-warning">تعديل الكميات</a>
    @endcan
</div>
@endsection
