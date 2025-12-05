<div class="row mb-3 detail-row" data-index="{{ $index }}">
    <div class="col-md-4">
        <label for="item_id_{{ $index }}" class="form-label">الصنف <span class="text-danger">*</span></label>
        <select name="details[{{ $index }}][item_id]" id="item_id_{{ $index }}" class="form-control" required>
            <option value="">اختر الصنف</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" {{ (isset($detail['item_id']) && $detail['item_id'] == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="quantity_{{ $index }}" class="form-label">الكمية <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" name="details[{{ $index }}][quantity]" id="quantity_{{ $index }}" class="form-control" value="{{ $detail['quantity'] ?? '' }}" required>
    </div>
    <div class="col-md-2">
        <label for="unit_price_{{ $index }}" class="form-label">سعر الوحدة <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="details[{{ $index }}][unit_price]" id="unit_price_{{ $index }}" class="form-control" value="{{ $detail['unit_price'] ?? '' }}" required>
    </div>
    <div class="col-md-2">
        <label for="total_price_{{ $index }}" class="form-label">الإجمالي</label>
        <input type="text" name="details[{{ $index }}][total_price]" id="total_price_{{ $index }}" class="form-control" value="{{ $detail['total_price'] ?? '' }}" readonly>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        @if ($index > 0 || old('details'))
            <button type="button" class="btn btn-danger w-100 remove-detail-row">حذف</button>
        @endif
    </div>
</div>
