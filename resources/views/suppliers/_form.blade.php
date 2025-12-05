{{-- نموذج جزئي يستخدم لإنشاء وتعديل الموردين --}}
<div class="form-group">
    <label for="name">اسم المورد <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
</div>

<div class="form-group">
    <label for="phone">رقم الهاتف <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
</div>

<div class="form-group">
    <label for="email">البريد الإلكتروني</label>
    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}">
</div>

<div class="form-group">
    <label for="contact_person">شخص الاتصال</label>
    <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}">
</div>

<div class="form-group">
    <label for="address">العنوان</label>
    <textarea class="form-control" id="address" name="address">{{ old('address', $supplier->address) }}</textarea>
</div>

<div class="form-group">
    <label for="initial_balance">الرصيد الافتتاحي</label>
    <input type="number" step="0.01" class="form-control" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', $supplier->initial_balance ?? 0) }}">
    <small class="form-text text-muted">الرصيد الافتتاحي للمورد (مبلغ مستحق له يكون موجباً).</small>
</div>

<div class="form-group form-check">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">نشط</label>
</div>
