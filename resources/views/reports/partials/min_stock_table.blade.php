<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>الصنف</th>
            <th>الرصيد الحالي</th>
            <th>الحد الأدنى المطلوب</th>
            <th>الفرق</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ number_format($item->current_stock, 2) }}</td>
                <td>{{ number_format($item->min_stock_level, 2) }}</td>
                <td>{{ number_format($item->min_stock_level - $item->current_stock, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">لا توجد أصناف تحت الحد الأدنى للمخزون.</td>
            </tr>
        @endforelse
    </tbody>
</table>
