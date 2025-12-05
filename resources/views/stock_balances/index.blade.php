@extends('layouts.app')

@section('content')
<div class="container">
    <h1>رصيد المخزون الحالي</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        @can('create', \App\Models\StockBalance::class)
            <a href="{{ route('stock_balances.create') }}" class="btn btn-primary">إضافة رصيد جديد</a>
        @endcan
        <a href="{{ route('stock_balances.alerts') }}" class="btn btn-warning">تنبيهات الحد الأدنى</a>
        <a href="{{ route('stock_balances.slow_moving_report') }}" class="btn btn-info">تقرير الأصناف الراكدة</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>المخزن</th>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>متوسط التكلفة</th>
                <th>القيمة الإجمالية</th>
                <th>آخر تحديث</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($balances as $balance)
                <tr>
                    <td>{{ $balance->id }}</td>
                    <td>{{ $balance->warehouse->name ?? 'N/A' }}</td>
                    <td>{{ $balance->item->name ?? 'N/A' }}</td>
                    <td>{{ number_format($balance->quantity, 2) }}</td>
                    <td>{{ number_format($balance->average_cost, 2) }}</td>
                    <td>{{ number_format($balance->total_value, 2) }}</td>
                    <td>{{ $balance->last_updated->diffForHumans() }}</td>
                    <td>
                        @can('update', $balance)
                            <a href="{{ route('stock_balances.edit', $balance) }}" class="btn btn-sm btn-secondary">تعديل</a>
                        @endcan
                        @can('delete', $balance)
                            <form action="{{ route('stock_balances.destroy', $balance) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">لا توجد أرصدة مخزون مسجلة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $balances->links() }}
</div>
@endsection
