@extends('layouts.app')

@section('title', 'إدارة الحسابات الوسيطة')

@section('content')
<div class="container-fluid">
    <!-- Header with Gradient -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-exchange-alt me-2"></i>
                        إدارة الحسابات الوسيطة
                    </h2>
                    <p class="mb-0 opacity-90">
                        <i class="fas fa-info-circle me-2"></i>
                        إدارة وتتبع الحسابات الوسيطة المرتبطة بالصناديق والبنوك والمحافظ
                    </p>
                </div>
                <div>
                    <a href="{{ route('intermediate-accounts.create') }}" class="btn btn-light btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        إضافة حساب وسيط جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-filter text-primary me-2"></i>
                فلترة الحسابات
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('intermediate-accounts.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الوحدة التنظيمية</label>
                        <select name="unit_id" class="form-select">
                            <option value="">جميع الوحدات</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الدليل المحاسبي</label>
                        <select name="chart_group_id" class="form-select">
                            <option value="">جميع الأدلة</option>
                            @foreach($chartGroups as $group)
                                <option value="{{ $group->id }}" {{ request('chart_group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الحساب</label>
                        <select name="intermediate_for" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="cash_boxes" {{ request('intermediate_for') == 'cash_boxes' ? 'selected' : '' }}>صناديق</option>
                            <option value="banks" {{ request('intermediate_for') == 'banks' ? 'selected' : '' }}>بنوك</option>
                            <option value="wallets" {{ request('intermediate_for') == 'wallets' ? 'selected' : '' }}>محافظ</option>
                            <option value="atms" {{ request('intermediate_for') == 'atms' ? 'selected' : '' }}>صرافات</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">حالة الربط</label>
                        <select name="is_linked" class="form-select">
                            <option value="">الكل</option>
                            <option value="0" {{ request('is_linked') === '0' ? 'selected' : '' }}>غير مرتبط</option>
                            <option value="1" {{ request('is_linked') === '1' ? 'selected' : '' }}>مرتبط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="الكود أو الاسم" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>
                        بحث
                    </button>
                    <a href="{{ route('intermediate-accounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-list text-success me-2"></i>
                    الحسابات الوسيطة
                    <span class="badge bg-success ms-2">{{ $accounts->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body">
            @if($accounts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-3">لا توجد حسابات وسيطة</h5>
                    <p class="text-muted mb-4">ابدأ بإنشاء أول حساب وسيط</p>
                    <a href="{{ route('intermediate-accounts.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>
                        إضافة حساب وسيط جديد
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">الكود</th>
                                <th class="border-0">الاسم</th>
                                <th class="border-0">الدليل المحاسبي</th>
                                <th class="border-0">الوحدة</th>
                                <th class="border-0 text-center">النوع</th>
                                <th class="border-0 text-center">حالة الربط</th>
                                <th class="border-0 text-center">الحالة</th>
                                <th class="border-0 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>
                                        <code class="bg-light p-1 rounded">{{ $account->code }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $account->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $account->chartGroup->name }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $account->chartGroup->unit->name }}</small>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $typeLabels = [
                                                'cash_boxes' => ['label' => 'صناديق', 'icon' => 'fa-cash-register', 'color' => 'primary'],
                                                'banks' => ['label' => 'بنوك', 'icon' => 'fa-university', 'color' => 'success'],
                                                'wallets' => ['label' => 'محافظ', 'icon' => 'fa-wallet', 'color' => 'warning'],
                                                'atms' => ['label' => 'صرافات', 'icon' => 'fa-credit-card', 'color' => 'info'],
                                            ];
                                            $type = $typeLabels[$account->intermediate_for] ?? ['label' => $account->intermediate_for, 'icon' => 'fa-circle', 'color' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $type['color'] }}">
                                            <i class="fas {{ $type['icon'] }} me-1"></i>
                                            {{ $type['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($account->is_linked)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                مرتبط
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-circle me-1"></i>
                                                متاح
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($account->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('intermediate-accounts.show', $account->id) }}" class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('intermediate-accounts.edit', $account->id) }}" class="btn btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$account->is_linked)
                                                <form action="{{ route('intermediate-accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
