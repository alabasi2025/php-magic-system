@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">قائمة أذونات الإخراج</h3>
                    @can('create', App\Models\StockOut::class)
                        <a href="{{ route('stock_outs.create') }}" class="btn btn-primary float-end">إضافة إذن إخراج جديد</a>
                    @endcan
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الإذن</th>
                                <th>المخزن</th>
                                <th>العميل</th>
                                <th>التاريخ</th>
                                <th>الإجمالي</th>
                                <th>الحالة</th>
                                <th>المنشئ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stockOuts as $stockOut)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stockOut->number }}</td>
                                    <td>{{ $stockOut->warehouse->name ?? 'N/A' }}</td>
                                    <td>{{ $stockOut->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $stockOut->date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($stockOut->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $stockOut->status === 'canceled' ? 'danger' : 'success' }}">
                                            {{ $stockOut->status === 'canceled' ? 'ملغي' : 'مكتمل' }}
                                        </span>
                                    </td>
                                    <td>{{ $stockOut->creator->name ?? 'N/A' }}</td>
                                    <td>
                                        @can('view', $stockOut)
                                            <a href="{{ route('stock_outs.show', $stockOut) }}" class="btn btn-sm btn-info">عرض</a>
                                        @endcan

                                        @can('cancel', $stockOut)
                                            @if ($stockOut->status !== 'canceled')
                                                <form action="{{ route('stock_outs.cancel', $stockOut) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('هل أنت متأكد من إلغاء إذن الإخراج هذا؟ سيتم إعادة الكميات إلى المخزون.')">إلغاء</button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('delete', $stockOut)
                                            @if ($stockOut->status === 'canceled')
                                                <form action="{{ route('stock_outs.destroy', $stockOut) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف إذن الإخراج هذا؟')">حذف</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد أذونات إخراج مسجلة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $stockOuts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
