@extends('layouts.app')

@section('content')
<div class="container">
    <h1>إدخال الكميات الفعلية لعملية الجرد: {{ $stockCount->number }}</h1>
    <p><strong>المخزن:</strong> {{ $stockCount->warehouse->name ?? 'N/A' }}</p>
    <p><strong>التاريخ:</strong> {{ $stockCount->date->format('Y-m-d') }}</p>
    <p><strong>الحالة:</strong> <span class="badge bg-warning">{{ $stockCount->status }}</span></p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('stock_counts.update', $stockCount) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>الكمية في النظام</th>
                    <th>الكمية الفعلية</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockCount->details as $index => $detail)
                    <tr>
                        <td>{{ $detail->item->name ?? 'N/A' }}</td>
                        <td>{{ $detail->system_quantity }}</td>
                        <td>
                            <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                            <input type="number" step="0.01" class="form-control @error("details.{$index}.actual_quantity") is-invalid @enderror"
                                name="details[{{ $index }}][actual_quantity]"
                                value="{{ old("details.{$index}.actual_quantity", $detail->actual_quantity ?? '') }}"
                                required>
                            @error("details.{$index}.actual_quantity")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="text" class="form-control" name="details[{{ $index }}][notes]"
                                value="{{ old("details.{$index}.notes", $detail->notes) }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">حفظ الكميات وإرسال للمراجعة</button>
    </form>
</div>
@endsection
