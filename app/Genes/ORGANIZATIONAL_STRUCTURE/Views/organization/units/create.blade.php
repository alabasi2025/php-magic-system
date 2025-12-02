@extends('layouts.app')

{{-- تعيين اتجاه الصفحة من اليمين لليسار لدعم اللغة العربية (RTL) --}}
@section('styles')
    {{-- يمكن إضافة CSS مخصص هنا إذا لم يكن القالب الأساسي يدعم RTL بشكل كامل --}}
    <style>
        body {
            direction: rtl;
            text-align: right;
        }
        .form-check-label {
            padding-right: 1.5rem; /* لضبط محاذاة النص بجانب مربع الاختيار */
        }
    </style>
@endsection

@section('content')
<div class="container my-5">
    {{-- عنوان الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-sitemap fa-fw me-2"></i>
            إنشاء وحدة تنظيمية جديدة
        </h1>
        <a href="{{ route('organization.units.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right fa-fw me-1"></i>
            العودة إلى قائمة الوحدات
        </a>
    </div>

    {{-- عرض رسائل الفلاش (Flash Messages) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle fa-fw me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle fa-fw me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus-circle fa-fw me-2"></i>
                نموذج إنشاء وحدة
            </h5>
        </div>
        <div class="card-body">
            {{-- نموذج الإرسال --}}
            <form action="{{ route('organization.units.store') }}" method="POST">
                @csrf

                {{-- حقل Holding ID (الكيان القابض) --}}
                <div class="mb-3">
                    <label for="holding_id" class="form-label">
                        <i class="fas fa-building fa-fw me-1"></i>
                        الكيان القابض <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('holding_id') is-invalid @enderror" id="holding_id" name="holding_id" required>
                        <option value="">اختر الكيان القابض</option>
                        {{-- يجب تكرار الخيارات من قاعدة البيانات --}}
                        @foreach ($holdings as $holding)
                            <option value="{{ $holding->id }}" {{ old('holding_id') == $holding->id ? 'selected' : '' }}>
                                {{ $holding->name }} ({{ $holding->code }})
                            </option>
                        @endforeach
                    </select>
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('holding_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- حقل Code (رمز الوحدة) --}}
                <div class="mb-3">
                    <label for="code" class="form-label">
                        <i class="fas fa-barcode fa-fw me-1"></i>
                        رمز الوحدة <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required maxlength="10">
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- حقل Name (اسم الوحدة) --}}
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-tag fa-fw me-1"></i>
                        اسم الوحدة (بالعربية) <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- حقل Type (نوع الوحدة) --}}
                <div class="mb-3">
                    <label for="type" class="form-label">
                        <i class="fas fa-list-alt fa-fw me-1"></i>
                        نوع الوحدة <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">اختر نوع الوحدة</option>
                        {{-- افتراض أنواع للوحدات --}}
                        @php
                            $unitTypes = ['Main Unit' => 'وحدة رئيسية', 'Branch' => 'فرع', 'Department Group' => 'مجموعة إدارات'];
                        @endphp
                        @foreach ($unitTypes as $key => $value)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- حقل Manager ID (مدير الوحدة) --}}
                <div class="mb-3">
                    <label for="manager_id" class="form-label">
                        <i class="fas fa-user-tie fa-fw me-1"></i>
                        مدير الوحدة
                    </label>
                    <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                        <option value="">لا يوجد مدير محدد</option>
                        {{-- يجب تكرار الخيارات من قائمة المستخدمين/المديرين --}}
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('manager_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- حقل Is Active (حالة التفعيل) --}}
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <i class="fas fa-toggle-on fa-fw me-1"></i>
                        الوحدة مفعلة
                    </label>
                    {{-- عرض أخطاء التحقق (Validation Errors) --}}
                    @error('is_active')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- زر الإرسال --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save fa-fw me-2"></i>
                        حفظ الوحدة الجديدة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- قسم السكريبتات (Scripts) --}}
@section('scripts')
    {{-- يمكن إضافة سكريبتات JavaScript هنا، مثل مكتبة Select2 لتحسين حقول الاختيار --}}
@endsection
