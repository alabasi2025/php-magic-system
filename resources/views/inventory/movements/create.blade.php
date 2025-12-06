@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('stock-movements.index') }}">حركات المخزون</a></li>
                    <li class="breadcrumb-item active">إضافة حركة جديدة</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="fas fa-exchange-alt me-2"></i>
                إضافة حركة مخزون - 
                @if($movementType == 'stock_in')
                    إدخال بضاعة
                @elseif($movementType == 'stock_out')
                    إخراج بضاعة
                @elseif($movementType == 'transfer')
                    نقل بين المخازن
                @elseif($movementType == 'adjustment')
                    تسوية مخزون
                @else
                    إرجاع بضاعة
                @endif
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('stock-movements.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="movement_type" value="{{ $movementType }}">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="warehouse_id" class="form-label">
                                    @if($movementType == 'transfer')
                                        المخزن المصدر
                                    @else
                                        المخزن
                                    @endif
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">اختر المخزن</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($movementType == 'transfer')
                                <div class="col-md-6">
                                    <label for="to_warehouse_id" class="form-label">المخزن الوجهة <span class="text-danger">*</span></label>
                                    <select class="form-select @error('to_warehouse_id') is-invalid @enderror" id="to_warehouse_id" name="to_warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_warehouse_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label for="movement_date" class="form-label">تاريخ الحركة <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('movement_date') is-invalid @enderror" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required>
                                    @error('movement_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        @if($movementType == 'transfer')
                            <div class="mb-3">
                                <label for="movement_date" class="form-label">تاريخ الحركة <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('movement_date') is-invalid @enderror" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required>
                                @error('movement_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="item_id" class="form-label">الصنف <span class="text-danger">*</span></label>
                            <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                <option value="">اختر الصنف</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit->name }}" data-price="{{ $item->unit_price }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="unit_cost" class="form-label">تكلفة الوحدة <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('unit_cost') is-invalid @enderror" id="unit_cost" name="unit_cost" value="{{ old('unit_cost', 0) }}" required>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">التكلفة الإجمالية</label>
                                <input type="text" class="form-control" id="total_cost_display" readonly value="0.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i>
                                رجوع
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات مهمة
                    </h5>
                    <ul class="list-unstyled">
                        @if($movementType == 'stock_in')
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إدخال بضاعة للمخزن</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>يزيد من المخزون المتاح</li>
                        @elseif($movementType == 'stock_out')
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إخراج بضاعة من المخزن</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>ينقص من المخزون المتاح</li>
                        @elseif($movementType == 'transfer')
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نقل بضاعة بين مخزنين</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>يجب اختيار مخزن مصدر ووجهة مختلفين</li>
                        @elseif($movementType == 'adjustment')
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>تسوية المخزون</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>استخدم كمية موجبة للزيادة وسالبة للنقصان</li>
                        @else
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إرجاع بضاعة</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>يزيد من المخزون المتاح</li>
                        @endif
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>الحركة تحتاج اعتماد قبل التأثير على المخزون</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>سيتم إنشاء قيد محاسبي تلقائياً عند الاعتماد</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemSelect = document.getElementById('item_id');
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    const totalCostDisplay = document.getElementById('total_cost_display');

    // Auto-fill unit cost when item is selected
    itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            unitCostInput.value = price;
            calculateTotal();
        }
    });

    // Calculate total cost
    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const total = quantity * unitCost;
        totalCostDisplay.value = total.toFixed(2);
    }

    quantityInput.addEventListener('input', calculateTotal);
    unitCostInput.addEventListener('input', calculateTotal);
});
</script>
@endsection
