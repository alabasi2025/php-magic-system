@extends('layouts.app')

@section('content')
<div class="container">
    <h1>تحويلات المخزون</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary">إنشاء طلب تحويل جديد</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>الرقم</th>
                <th>من مخزن</th>
                <th>إلى مخزن</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>المنشئ</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transfers as $transfer)
                <tr>
                    <td>{{ $transfer->id }}</td>
                    <td><a href="{{ route('stock_transfers.show', $transfer) }}">{{ $transfer->number }}</a></td>
                    <td>{{ $transfer->fromWarehouse->name ?? 'N/A' }}</td>
                    <td>{{ $transfer->toWarehouse->name ?? 'N/A' }}</td>
                    <td>{{ $transfer->date->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge bg-{{ $transfer->status == 'approved' || $transfer->status == 'completed' ? 'success' : ($transfer->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ __("statuses.{$transfer->status}") }}
                        </span>
                    </td>
                    <td>{{ $transfer->creator->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('stock_transfers.show', $transfer) }}" class="btn btn-sm btn-info">عرض</a>
                        @can('update', $transfer)
                            <a href="{{ route('stock_transfers.edit', $transfer) }}" class="btn btn-sm btn-warning">تعديل</a>
                        @endcan
                        @can('approve', $transfer)
                            <form action="{{ route('stock_transfers.approve', $transfer) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا التحويل؟ سيتم خصم وإضافة المخزون.')">موافقة</button>
                            </form>
                        @endcan
                        @can('delete', $transfer)
                            <form action="{{ route('stock_transfers.destroy', $transfer) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا التحويل؟')">حذف</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">لا توجد تحويلات مخزون متاحة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $transfers->links() }}
</div>
@endsection
