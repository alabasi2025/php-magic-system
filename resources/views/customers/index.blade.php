@extends('layouts.app')

@section('title', 'إدارة العملاء')

@section('content')
<div class="container">
    <h1>قائمة العملاء</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">إضافة عميل جديد</a>
        <form method="GET" action="{{ route('customers.index') }}" class="form-inline">
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
            @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email ?? 'لا يوجد' }}</td>
                <td>
                    <span class="badge {{ $customer->balance >= 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ number_format($customer->balance, 2) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $customer->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-info">تعديل</a>
                    <a href="{{ route('customers.history', $customer) }}" class="btn btn-sm btn-secondary">تعاملات</a>
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف العميل؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">لا يوجد عملاء مسجلون حالياً.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $customers->links() }}
</div>
@endsection
