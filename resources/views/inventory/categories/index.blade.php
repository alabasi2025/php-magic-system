@extends('layouts.app')

@section('title', 'إدارة الفئات')

@section('content')
<div class="container">
    <h1>إدارة فئات المخزون</h1>
    <a href="{{ route('inventory.categories.create') }}" class="btn btn-primary mb-3">إضافة فئة جديدة</a>

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
                <th>الاسم</th>
                <th>الفئة الأب</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->parent->name ?? 'فئة رئيسية' }}</td>
                    <td>
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                            {{ $category->is_active ? 'مفعلة' : 'معطلة' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('inventory.categories.show', $category) }}" class="btn btn-sm btn-info">عرض</a>
                        <a href="{{ route('inventory.categories.edit', $category) }}" class="btn btn-sm btn-warning">تعديل</a>
                        <form action="{{ route('inventory.categories.destroy', $category) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">لا توجد فئات متاحة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
