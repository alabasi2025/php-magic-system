@extends('layouts.app')

@section('content')
<div class="container">
    <h1>عمليات الجرد</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @can('create', App\Models\StockCount::class)
        <a href="{{ route('stock_counts.create') }}" class="btn btn-primary mb-3">إنشاء عملية جرد جديدة</a>
    @endcan

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الرقم</th>
                <th>المخزن</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>المنشئ</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stockCounts as $stockCount)
                <tr>
                    <td>{{ $stockCount->number }}</td>
                    <td>{{ $stockCount->warehouse->name ?? 'N/A' }}</td>
                    <td>{{ $stockCount->date->format('Y-m-d') }}</td>
                    <td><span class="badge bg-{{ $stockCount->status == 'Draft' ? 'warning' : ($stockCount->status == 'Adjusted' ? 'success' : 'info') }}">{{ $stockCount->status }}</span></td>
                    <td>{{ $stockCount->creator->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('stock_counts.show', $stockCount) }}" class="btn btn-sm btn-info">عرض</a>

                        @can('update', $stockCount)
                            <a href="{{ route('stock_counts.edit', $stockCount) }}" class="btn btn-sm btn-warning">إدخال الكميات</a>
                        @endcan

                        @can('approve', $stockCount)
                            <form action="{{ route('stock_counts.approve', $stockCount) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من الموافقة على الجرد وتعديل المخزون؟')">موافقة</button>
                            </form>
                        @endcan

                        @can('delete', $stockCount)
                            <form action="{{ route('stock_counts.destroy', $stockCount) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف عملية الجرد؟')">حذف</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">لا توجد عمليات جرد حالياً.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $stockCounts->links() }}
</div>
@endsection
