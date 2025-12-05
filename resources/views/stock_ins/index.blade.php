@extends('layouts.app')

@section('title', 'حركات الإدخال')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>إذونات الإدخال</h1>
        <a href="{{ route('stock_ins.create') }}" class="btn btn-primary">إنشاء إذن إدخال جديد</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>التاريخ</th>
                        <th>المخزن</th>
                        <th>المورد</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>المنشئ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stockIns as $stockIn)
                        <tr>
                            <td>{{ $stockIn->number }}</td>
                            <td>{{ $stockIn->date->format('Y-m-d') }}</td>
                            <td>{{ $stockIn->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $stockIn->supplier->name ?? 'N/A' }}</td>
                            <td>{{ number_format($stockIn->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $stockIn->status === 'Completed' ? 'success' : ($stockIn->status === 'Draft' ? 'warning' : 'danger') }}">{{ $stockIn->status }}</span></td>
                            <td>{{ $stockIn->createdBy->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('stock_ins.show', $stockIn) }}" class="btn btn-sm btn-info">عرض</a>
                                @can('update', $stockIn)
                                    @if ($stockIn->status === 'Draft')
                                        <a href="{{ route('stock_ins.edit', $stockIn) }}" class="btn btn-sm btn-warning">تعديل</a>
                                        <form action="{{ route('stock_ins.destroy', $stockIn) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                        <form action="{{ route('stock_ins.complete', $stockIn) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من ترحيل الإذن؟ سيؤثر ذلك على المخزون.')">ترحيل</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد إذونات إدخال حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $stockIns->links() }}
        </div>
    </div>
</div>
@endsection
