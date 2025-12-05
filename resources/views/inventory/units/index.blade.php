@extends('layouts.app')

@section('title', 'إدارة الوحدات')

@section('content')
<div class="container">
    <h1>إدارة وحدات القياس</h1>
    <a href="{{ route('inventory.units.create') }}" class="btn btn-primary mb-3">إضافة وحدة جديدة</a>
    <a href="{{ route('inventory.units.conversionForm') }}" class="btn btn-info mb-3">تحويل الوحدات</a>

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
                <th>الرمز</th>
                <th>وحدة أساسية؟</th>
                <th>الوحدة الأساسية</th>
                <th>معامل التحويل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($units as $unit)
                <tr>
                    <td>{{ $unit->id }}</td>
                    <td>{{ $unit->name }}</td>
                    <td>{{ $unit->symbol }}</td>
                    <td>
                        <span class="badge bg-{{ $unit->is_base_unit ? 'primary' : 'secondary' }}">
                            {{ $unit->is_base_unit ? 'نعم' : 'لا' }}
                        </span>
                    </td>
                    <td>{{ $unit->baseUnit->name ?? '-' }}</td>
                    <td>{{ $unit->conversion_factor }}</td>
                    <td>
                        <a href="{{ route('inventory.units.show', $unit) }}" class="btn btn-sm btn-info">عرض</a>
                        <a href="{{ route('inventory.units.edit', $unit) }}" class="btn btn-sm btn-warning">تعديل</a>
                        <form action="{{ route('inventory.units.destroy', $unit) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">لا توجد وحدات قياس متاحة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
