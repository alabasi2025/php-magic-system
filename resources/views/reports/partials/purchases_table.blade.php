<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>الصنف</th>
            <th>إجمالي الكمية المشتراة</th>
            <th>إجمالي التكلفة</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($purchases as $purchase)
            <tr>
                <td>{{ $purchase->item->name }}</td>
                <td>{{ number_format($purchase->total_quantity, 2) }}</td>
                <td>{{ number_format($purchase->total_cost, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">لا توجد مشتريات في الفترة المحددة.</td>
            </tr>
        @endforelse
    </tbody>
</table>
