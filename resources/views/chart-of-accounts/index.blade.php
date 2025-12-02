@extends('layouts.app')

@section('title', 'دليل الحسابات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-book"></i> دليل الحسابات
            </h2>
            <p class="text-muted">إدارة الحسابات الرئيسية والفرعية</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('chart-of-accounts.tree') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-sitemap"></i> عرض الشجرة
            </a>
            <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة حساب جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('chart-of-accounts.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">الوحدة</label>
                    <select name="unit_id" class="form-select">
                        <option value="">جميع الوحدات</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">نوع الحساب</label>
                    <select name="account_type" class="form-select">
                        <option value="">الكل</option>
                        <option value="asset" {{ request('account_type') == 'asset' ? 'selected' : '' }}>أصول</option>
                        <option value="liability" {{ request('account_type') == 'liability' ? 'selected' : '' }}>خصوم</option>
                        <option value="equity" {{ request('account_type') == 'equity' ? 'selected' : '' }}>حقوق ملكية</option>
                        <option value="revenue" {{ request('account_type') == 'revenue' ? 'selected' : '' }}>إيرادات</option>
                        <option value="expense" {{ request('account_type') == 'expense' ? 'selected' : '' }}>مصروفات</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">المستوى</label>
                    <select name="account_level" class="form-select">
                        <option value="">الكل</option>
                        <option value="parent" {{ request('account_level') == 'parent' ? 'selected' : '' }}>رئيسي</option>
                        <option value="sub" {{ request('account_level') == 'sub' ? 'selected' : '' }}>فرعي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control" placeholder="رقم أو اسم الحساب" value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم الحساب</th>
                            <th>اسم الحساب</th>
                            <th>الوحدة</th>
                            <th>النوع</th>
                            <th>المستوى</th>
                            <th>الحساب الأب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>
                                    <strong>{{ $account->code }}</strong>
                                    @if($account->is_root)
                                        <span class="badge bg-warning text-dark ms-1">جذر</span>
                                    @endif
                                </td>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->unit->name ?? '-' }}</td>
                                <td>
                                    @if($account->account_type)
                                        <span class="badge bg-info">{{ $account->account_type_name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($account->account_level == 'parent')
                                        <span class="badge bg-primary">رئيسي</span>
                                    @else
                                        <span class="badge bg-secondary">فرعي</span>
                                    @endif
                                </td>
                                <td>{{ $account->parent->name ?? '-' }}</td>
                                <td>
                                    @if($account->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('chart-of-accounts.show', $account) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('chart-of-accounts.edit', $account) }}" class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($account->canBeDeleted())
                                        <form action="{{ route('chart-of-accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>لا توجد حسابات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
