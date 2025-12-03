@extends('layouts.app')

@section('title', 'تفاصيل الحساب الوسيط')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <a href="{{ route('intermediate-accounts.index') }}" class="btn btn-light btn-sm me-3">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <h2 class="mb-0 fw-bold">
                            <i class="fas fa-exchange-alt me-2"></i>
                            {{ $account->name }}
                        </h2>
                    </div>
                    <p class="mb-0 opacity-90">
                        <code class="bg-white bg-opacity-25 px-2 py-1 rounded">{{ $account->code }}</code>
                    </p>
                </div>
                <div>
                    <a href="{{ route('intermediate-accounts.edit', $account->id) }}" class="btn btn-warning btn-lg shadow-sm">
                        <i class="fas fa-edit me-2"></i>
                        تعديل
                    </a>
                    @if(!$account->is_linked)
                        <form action="{{ route('intermediate-accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg shadow-sm">
                                <i class="fas fa-trash me-2"></i>
                                حذف
                            </button>
                        </form>
                    @endif
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

    <div class="row g-4">
        <!-- معلومات أساسية -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        معلومات أساسية
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" width="40%">الكود:</th>
                            <td><code class="bg-light p-1 rounded">{{ $account->code }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">الاسم:</th>
                            <td><strong>{{ $account->name }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">النوع:</th>
                            <td>
                                @php
                                    $typeLabels = [
                                        'cash_boxes' => ['label' => 'صناديق نقدية', 'icon' => 'fa-cash-register', 'color' => 'primary'],
                                        'banks' => ['label' => 'بنوك', 'icon' => 'fa-university', 'color' => 'success'],
                                        'wallets' => ['label' => 'محافظ إلكترونية', 'icon' => 'fa-wallet', 'color' => 'warning'],
                                        'atms' => ['label' => 'صرافات آلية', 'icon' => 'fa-credit-card', 'color' => 'info'],
                                    ];
                                    $type = $typeLabels[$account->intermediate_for] ?? ['label' => $account->intermediate_for, 'icon' => 'fa-circle', 'color' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $type['color'] }}">
                                    <i class="fas {{ $type['icon'] }} me-1"></i>
                                    {{ $type['label'] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">الحالة:</th>
                            <td>
                                @if($account->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i>
                                        غير نشط
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">حالة الربط:</th>
                            <td>
                                @if($account->is_linked)
                                    <span class="badge bg-success">
                                        <i class="fas fa-link me-1"></i>
                                        مرتبط
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-unlink me-1"></i>
                                        متاح
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @if($account->description)
                            <tr>
                                <th class="text-muted align-top">الوصف:</th>
                                <td>{{ $account->description }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- معلومات التنظيم -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-sitemap text-success me-2"></i>
                        معلومات التنظيم
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" width="40%">الوحدة التنظيمية:</th>
                            <td>
                                <strong>{{ $account->chartGroup->unit->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $account->chartGroup->unit->code }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">الدليل المحاسبي:</th>
                            <td>
                                <strong>{{ $account->chartGroup->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $account->chartGroup->code }}</small>
                            </td>
                        </tr>
                        @if($account->parent)
                            <tr>
                                <th class="text-muted">الحساب الأب:</th>
                                <td>
                                    <strong>{{ $account->parent->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $account->parent->code }}</small>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-muted">تاريخ الإنشاء:</th>
                            <td>
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ $account->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">آخر تحديث:</th>
                            <td>
                                <i class="fas fa-clock text-muted me-1"></i>
                                {{ $account->updated_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- الكيان المرتبط -->
        @if($account->is_linked && $linkedEntity)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-link text-info me-2"></i>
                            الكيان المرتبط
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas {{ $type['icon'] }} fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ $linkedEntity->name }}</h6>
                                    <p class="mb-0 text-muted">
                                        <code>{{ $linkedEntity->code }}</code>
                                        @if(isset($linkedEntity->balance))
                                            | الرصيد: <strong>{{ number_format($linkedEntity->balance, 2) }}</strong>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
