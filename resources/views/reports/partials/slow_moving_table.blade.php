<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>الصنف</th>
            <th>الرصيد الحالي</th>
            <th>سعر التكلفة</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ number_format($item->current_stock, 2) }}</td>
                <td>{{ number_format($item->cost_price, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">لا توجد أصناف راكدة (جميع الأصناف تحركت مؤخراً).</td>
            </tr>
        @endforelse
    </tbody>
</table>
