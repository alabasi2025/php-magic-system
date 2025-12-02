{{-- /home/ubuntu/php-magic-system/resources/views/organization/departments/create.blade.php --}}
{{--
    ملف Blade View لإنشاء قسم جديد (departments/create)
    المتطلبات: Bootstrap 5, RTL, Responsive, Font Awesome, Flash Messages, Validation Errors
    الحقول: unit_id, code, name, type, manager_id, budget, is_active
--}}

@extends('layouts.app') {{-- افتراض استخدام تخطيط أساسي --}}

@section('title', 'إنشاء قسم جديد')

@section('content')
<div class="container my-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- بطاقة النموذج --}}
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> إنشاء قسم جديد</h4>
                </div>
                <div class="card-body p-4">

                    {{-- رسائل الفلاش (Flash Messages) --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- نموذج الإدخال --}}
                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf {{-- توكن الحماية من هجمات CSRF --}}

                        {{-- حقل unit_id (الوحدة الأم) --}}
                        <div class="mb-3">
                            <label for="unit_id" class="form-label"><i class="fas fa-sitemap me-2"></i> الوحدة الأم</label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                <option value="">اختر الوحدة الأم</option>
                                {{-- يتم ملء الخيارات ديناميكياً من قاعدة البيانات --}}
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل code (كود القسم) --}}
                        <div class="mb-3">
                            <label for="code" class="form-label"><i class="fas fa-barcode me-2"></i> كود القسم</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل name (اسم القسم) --}}
                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="fas fa-building me-2"></i> اسم القسم</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل type (نوع القسم) --}}
                        <div class="mb-3">
                            <label for="type" class="form-label"><i class="fas fa-tag me-2"></i> نوع القسم</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">اختر نوع القسم</option>
                                {{-- يتم ملء الخيارات ديناميكياً (مثال) --}}
                                <option value="main" {{ old('type') == 'main' ? 'selected' : '' }}>رئيسي</option>
                                <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>دعم</option>
                                <option value="operational" {{ old('type') == 'operational' ? 'selected' : '' }}>تشغيلي</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل manager_id (مدير القسم) --}}
                        <div class="mb-3">
                            <label for="manager_id" class="form-label"><i class="fas fa-user-tie me-2"></i> مدير القسم</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                <option value="">اختر المدير (اختياري)</option>
                                {{-- يتم ملء الخيارات ديناميكياً من جدول المستخدمين/الموظفين --}}
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل budget (الميزانية) --}}
                        <div class="mb-3">
                            <label for="budget" class="form-label"><i class="fas fa-money-bill-wave me-2"></i> الميزانية</label>
                            <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget') }}" required>
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حقل is_active (حالة النشاط) --}}
                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active"><i class="fas fa-toggle-on me-2"></i> نشط</label>
                            @error('is_active')
                                <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block لجعل رسالة الخطأ تظهر أسفل الـ switch --}}
                            @enderror
                        </div>

                        {{-- زر الإرسال --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- قسم السكريبتات الإضافية (إذا لزم الأمر) --}}
@push('scripts')
<script>
    // يمكن إضافة أي سكريبتات خاصة بالنموذج هنا
    // مثال: تهيئة مكتبة Select2 إذا كانت مستخدمة
</script>
@endpush
