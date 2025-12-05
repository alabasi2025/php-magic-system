<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>الصنف</th>
            <th>إجمالي الكمية المباعة</th>
            <th>إجمالي الإيرادات</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sales as $sale)
            <tr>
                <td>{{ $sale->item->name }}</td>
                <td>{{ number_format($sale->total_quantity, 2) }}</td>
                <td>{{ number_format($sale->total_revenue, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">لا توجد مبيعات في الفترة المحددة.</td>
            </tr>
        @endforelse
    </tbody>
</table>
