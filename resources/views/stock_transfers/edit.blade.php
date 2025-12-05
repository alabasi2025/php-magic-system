@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تعديل طلب تحويل المخزون #{{ $stockTransfer->number }}</h1>

    {{-- يتم التحقق من حالة التحويل في Policy، إذا لم يكن مسموحاً بالتعديل، يجب أن يتم توجيه المستخدم بعيداً أو عرض رسالة --}}
    @if ($stockTransfer->status !== 'pending')
        <div class="alert alert-info">لا يمكن تعديل طلب التحويل إلا إذا كانت حالته "قيد الانتظار". الحالة الحالية: {{ __("statuses.{$stockTransfer->status}") }}</div>
    @endif

    <form action="{{ route('stock_transfers.update', $stockTransfer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">بيانات التحويل الأساسية</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="from_warehouse_id" class="form-label">المخزن المصدر (من)</label>
                        <select class="form-control @error('from_warehouse_id') is-invalid @enderror" id="from_warehouse_id" name="from_warehouse_id" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                            <option value="">اختر المخزن</option>
                            {{-- يجب تمرير $warehouses من المتحكم --}}
                            {{-- @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id', $stockTransfer->from_warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('from_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="to_warehouse_id" class="form-label">المخزن المستقبل (إلى)</label>
                        <select class="form-control @error('to_warehouse_id') is-invalid @enderror" id="to_warehouse_id" name="to_warehouse_id" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                            <option value="">اختر المخزن</option>
                            {{-- @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id', $stockTransfer->to_warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('to_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">تاريخ التحويل</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $stockTransfer->date->format('Y-m-d')) }}" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>{{ old('notes', $stockTransfer->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">تفاصيل المواد المحولة</div>
            <div class="card-body">
                {{-- ملاحظة: تحديث التفاصيل في التعديل يتطلب منطقاً معقداً في الخدمة والمتحكم، هنا نكتفي بعرضها فقط إذا لم يكن مسموحاً بالتعديل --}}
                <table class="table table-sm" id="details-table">
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>الكمية</th>
                            @if ($stockTransfer->status === 'pending')
                                <th>الإجراء</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockTransfer->details as $index => $detail)
                            <tr>
                                <td>
                                    <select name="details[{{ $index }}][item_id]" class="form-control item-select" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                        <option value="{{ $detail->item_id }}" selected>{{ $detail->item->name ?? 'N/A' }}</option>
                                        {{-- باقي الخيارات --}}
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="details[{{ $index }}][quantity]" class="form-control" step="0.01" min="0.01" value="{{ old("details.{$index}.quantity", $detail->quantity) }}" required {{ $stockTransfer->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                @if ($stockTransfer->status === 'pending')
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-detail">حذف</button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($stockTransfer->status === 'pending')
                    <button type="button" class="btn btn-success btn-sm" id="add-detail">إضافة مادة</button>
                @endif
            </div>
        </div>

        @if ($stockTransfer->status === 'pending')
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        @endif
        <a href="{{ route('stock_transfers.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </form>
</div>

{{-- يمكن إعادة استخدام نفس سكريبت JavaScript من صفحة الإنشاء مع تعديلات بسيطة --}}
@endsection
