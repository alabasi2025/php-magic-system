@extends('layouts.app')

@section('title', 'إدارة الموردين')

@section('content')
<div class="container">
    <h1>قائمة الموردين</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">إضافة مورد جديد</a>
        <form method="GET" action="{{ route('suppliers.index') }}" class="form-inline">
            <input type="text" name="search" class="form-control mr-sm-2" placeholder="بحث بالاسم أو الهاتف" value="{{ request('search') }}">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">بحث</button>
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الهاتف</th>
                <th>البريد الإلكتروني</th>
                <th>الرصيد الحالي</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->id }}</td>
                <td><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email ?? 'لا يوجد' }}</td>
                <td>
                    <span class="badge {{ $supplier->balance >= 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ number_format($supplier->balance, 2) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-info">تعديل</a>
                    <a href="{{ route('suppliers.history', $supplier) }}" class="btn btn-sm btn-secondary">تعاملات</a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف المورد؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">لا يوجد موردون مسجلون حالياً.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $suppliers->links() }}
</div>
@endsection
