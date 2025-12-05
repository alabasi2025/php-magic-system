<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>الصنف</th>
            <th>إجمالي الحركات</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($movements as $movement)
            <tr>
                <td>{{ $movement->item->name }}</td>
                <td>{{ $movement->total_movements }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center">لا توجد حركات أصناف في الفترة المحددة.</td>
            </tr>
        @endforelse
    </tbody>
</table>
