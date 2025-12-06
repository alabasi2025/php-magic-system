@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.stock-movements.index') }}">حركات المخزون</a></li>
                    <li class="breadcrumb-item active">تفاصيل الحركة</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    حركة مخزون رقم: {{ $stockMovement->movement_number }}
                </h2>
                <div>
                    @if($stockMovement->status == 'pending')
                        <form action="{{ route('inventory.stock-movements.approve', $stockMovement) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من اعتماد هذه الحركة؟')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>
                                اعتماد
                            </button>
                        </form>
                        <form action="{{ route('inventory.stock-movements.reject', $stockMovement) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رفض هذه الحركة؟')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-1"></i>
                                رفض
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('inventory.stock-movements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الحركة -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الحركة
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%"><strong>رقم الحركة:</strong></td>
                            <td>{{ $stockMovement->movement_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>نوع الحركة:</strong></td>
                            <td>
                                @php
                                    $typeLabels = [
                                        'stock_in' => 'إدخال',
                                        'stock_out' => 'إخراج',
                                        'transfer_in' => 'تحويل وارد',
                                        'transfer_out' => 'تحويل صادر',
                                        'adjustment' => 'تسوية',
                                        'return' => 'مرتجع'
                                    ];
                                    $typeColors = [
                                        'stock_in' => 'success',
                                        'stock_out' => 'danger',
                                        'transfer_in' => 'info',
                                        'transfer_out' => 'warning',
                                        'adjustment' => 'secondary',
                                        'return' => 'primary'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $typeColors[$stockMovement->movement_type] ?? 'secondary' }}">
                                    {{ $typeLabels[$stockMovement->movement_type] ?? $stockMovement->movement_type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>تاريخ الحركة:</strong></td>
                            <td>{{ $stockMovement->movement_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الحالة:</strong></td>
                            <td>
                                @if($stockMovement->status == 'approved')
                                    <span class="badge bg-success">معتمد</span>
                                @elseif($stockMovement->status == 'pending')
                                    <span class="badge bg-warning">معلق</span>
                                @else
                                    <span class="badge bg-danger">مرفوض</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>المستخدم:</strong></td>
                            <td>{{ $stockMovement->creator->name ?? 'N/A' }}</td>
                        </tr>
                        @if($stockMovement->approved_by)
                            <tr>
                                <td class="text-muted"><strong>معتمد بواسطة:</strong></td>
                                <td>{{ $stockMovement->approver->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>تاريخ الاعتماد:</strong></td>
                                <td>{{ $stockMovement->approved_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted"><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $stockMovement->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>

                    @if($stockMovement->notes)
                        <hr>
                        <div>
                            <strong class="text-muted">ملاحظات:</strong>
                            <p class="mt-2">{{ $stockMovement->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($stockMovement->journal_entry_id)
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>
                            القيد المحاسبي
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>رقم القيد:</strong> {{ $stockMovement->journal_entry_id }}
                        </p>
                        <a href="{{ route('journal-entries.show', $stockMovement->journal_entry_id) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye me-1"></i>
                            عرض القيد
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- تفاصيل الصنف والمخزن -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        تفاصيل الصنف
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($stockMovement->item->image_path)
                            <img src="{{ Storage::url($stockMovement->item->image_path) }}" alt="{{ $stockMovement->item->name }}" class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $stockMovement->item->name }}</h5>
                            <p class="text-muted mb-0">
                                <small>SKU: {{ $stockMovement->item->sku }}</small>
                            </p>
                        </div>
                    </div>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%"><strong>الكمية:</strong></td>
                            <td>
                                <h4 class="mb-0 text-primary">
                                    {{ number_format($stockMovement->quantity, 2) }}
                                    {{ $stockMovement->item->unit->name }}
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>سعر الوحدة:</strong></td>
                            <td>{{ number_format($stockMovement->unit_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>التكلفة الإجمالية:</strong></td>
                            <td>
                                <strong>{{ number_format($stockMovement->total_cost, 2) }}</strong>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <div class="text-center">
                        <a href="{{ route('inventory.items.show', $stockMovement->item) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye me-1"></i>
                            عرض تفاصيل الصنف
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        معلومات المخزن
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        @if(in_array($stockMovement->movement_type, ['stock_in', 'stock_out', 'adjustment', 'return']))
                            <tr>
                                <td class="text-muted" width="40%"><strong>المخزن:</strong></td>
                                <td>
                                    <a href="{{ route('inventory.warehouses.show', $stockMovement->warehouse) }}">
                                        {{ $stockMovement->warehouse->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>رمز المخزن:</strong></td>
                                <td>{{ $stockMovement->warehouse->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>الموقع:</strong></td>
                                <td>{{ $stockMovement->warehouse->location ?? 'غير محدد' }}</td>
                            </tr>
                        @else
                            <!-- For transfers -->
                            <tr>
                                <td class="text-muted" width="40%"><strong>من مخزن:</strong></td>
                                <td>
                                    @if($stockMovement->movement_type == 'transfer_out')
                                        <a href="{{ route('inventory.warehouses.show', $stockMovement->warehouse) }}">
                                            {{ $stockMovement->warehouse->name }}
                                        </a>
                                    @else
                                        {{ $stockMovement->warehouse->name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>إلى مخزن:</strong></td>
                                <td>
                                    @if($stockMovement->toWarehouse)
                                        <a href="{{ route('inventory.warehouses.show', $stockMovement->toWarehouse) }}">
                                            {{ $stockMovement->toWarehouse->name }}
                                        </a>
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($stockMovement->reference_number || $stockMovement->reference_type)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>
                            معلومات المرجع
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($stockMovement->reference_type)
                                <div class="col-md-6">
                                    <strong class="text-muted">نوع المرجع:</strong>
                                    <p>{{ $stockMovement->reference_type }}</p>
                                </div>
                            @endif
                            @if($stockMovement->reference_number)
                                <div class="col-md-6">
                                    <strong class="text-muted">رقم المرجع:</strong>
                                    <p>{{ $stockMovement->reference_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
