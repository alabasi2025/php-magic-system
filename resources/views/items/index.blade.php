@extends('layouts.app') {{-- نفترض وجود ملف تخطيط رئيسي --}}

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>إدارة الأصناف</h1>
            <a href="{{ route('items.create') }}" class="btn btn-primary mb-3">إضافة صنف جديد</a>

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
                        <th>الرمز</th>
                        <th>الاسم</th>
                        <th>الفئة</th>
                        <th>الوحدة</th>
                        <th>سعر البيع</th>
                        <th>مستوى الطلب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'غير محدد' }}</td>
                        <td>{{ $item->unit->name ?? 'غير محدد' }}</td>
                        <td>{{ number_format($item->selling_price, 2) }}</td>
                        <td>{{ $item->reorder_level }}</td>
                        <td>
                            <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                {{ $item->is_active ? 'مفعل' : 'غير مفعل' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info">عرض</a>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning">تعديل</a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">لا توجد أصناف مسجلة بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
