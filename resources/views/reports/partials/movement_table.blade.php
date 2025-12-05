<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>الصنف</th>
            <th>النوع</th>
            <th>الكمية</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date->format('Y-m-d H:i') }}</td>
                <td>{{ $transaction->item->name }}</td>
                <td>{{ $transaction->type == 'in' ? 'دخول' : 'خروج' }}</td>
                <td>{{ number_format($transaction->quantity, 2) }}</td>
                <td>{{ $transaction->notes ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">لا توجد حركات مخزون في الفترة المحددة.</td>
            </tr>
        @endforelse
    </tbody>
</table>
