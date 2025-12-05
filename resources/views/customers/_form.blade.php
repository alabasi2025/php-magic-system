{{-- نموذج جزئي يستخدم لإنشاء وتعديل العملاء --}}
<div class="form-group">
    <label for="name">اسم العميل <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
</div>

<div class="form-group">
    <label for="phone">رقم الهاتف <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
</div>

<div class="form-group">
    <label for="email">البريد الإلكتروني</label>
    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}">
</div>

<div class="form-group">
    <label for="contact_person">شخص الاتصال</label>
    <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $customer->contact_person) }}">
</div>

<div class="form-group">
    <label for="address">العنوان</label>
    <textarea class="form-control" id="address" name="address">{{ old('address', $customer->address) }}</textarea>
</div>

<div class="form-group">
    <label for="initial_balance">الرصيد الافتتاحي</label>
    <input type="number" step="0.01" class="form-control" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', $customer->initial_balance ?? 0) }}">
    <small class="form-text text-muted">الرصيد الافتتاحي للعميل (مبلغ مستحق للشركة يكون موجباً).</small>
</div>

<div class="form-group form-check">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">نشط</label>
</div>
