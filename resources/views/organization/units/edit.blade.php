@extends('layouts.app')

{{-- تعيين عنوان الصفحة --}}
@section('title', 'تعديل الوحدة: ' . $unit->name)

{{-- محتوى الصفحة --}}
@section('content')
    <div class="container mt-5">
        {{-- عنوان الصفحة --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-primary">
                <i class="fas fa-pencil-alt me-2"></i> تعديل بيانات الوحدة
            </h1>
            <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>

        {{-- عرض رسائل الفلاش (Flash Messages) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-end" role="alert" dir="rtl">
                <i class="fas fa-check-circle ms-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-end" role="alert" dir="rtl">
                <i class="fas fa-times-circle ms-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- بطاقة النموذج --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-end">
                {{-- عنوان النموذج --}}
                <h5 class="mb-0">نموذج تعديل الوحدة: {{ $unit->name }}</h5>
            </div>
            <div class="card-body" dir="rtl">
                {{-- بداية النموذج --}}
                <form action="{{ route('units.update', $unit->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- حقل holding_id (الشركة القابضة) --}}
                    <div class="mb-3">
                        <label for="holding_id" class="form-label float-end">
                            <i class="fas fa-building me-1"></i> الشركة القابضة <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('holding_id') is-invalid @enderror" id="holding_id" name="holding_id" required>
                            <option value="">اختر الشركة القابضة</option>
                            {{-- افتراض وجود متغير $holdings يحتوي على الشركات القابضة --}}
                            @foreach ($holdings as $holding)
                                <option value="{{ $holding->id }}" {{ old('holding_id', $unit->holding_id) == $holding->id ? 'selected' : '' }}>
                                    {{ $holding->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('holding_id')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- حقل code (رمز الوحدة) --}}
                    <div class="mb-3">
                        <label for="code" class="form-label float-end">
                            <i class="fas fa-barcode me-1"></i> رمز الوحدة <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $unit->code) }}" required>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('code')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- حقل name (اسم الوحدة) --}}
                    <div class="mb-3">
                        <label for="name" class="form-label float-end">
                            <i class="fas fa-file-signature me-1"></i> اسم الوحدة (عربي) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('name')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- حقل type (نوع الوحدة) --}}
                    <div class="mb-3">
                        <label for="type" class="form-label float-end">
                            <i class="fas fa-tags me-1"></i> نوع الوحدة <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">اختر نوع الوحدة</option>
                            {{-- افتراض وجود متغير $unitTypes يحتوي على أنواع الوحدات --}}
                            @php
                                $unitTypes = ['فرع', 'إدارة مستقلة', 'مركز تكلفة']; // مثال للأنواع
                            @endphp
                            @foreach ($unitTypes as $type)
                                <option value="{{ $type }}" {{ old('type', $unit->type) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('type')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- حقل manager_id (مدير الوحدة) --}}
                    <div class="mb-3">
                        <label for="manager_id" class="form-label float-end">
                            <i class="fas fa-user-tie me-1"></i> مدير الوحدة
                        </label>
                        <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                            <option value="">لا يوجد مدير محدد</option>
                            {{-- افتراض وجود متغير $managers يحتوي على قائمة المدراء/المستخدمين --}}
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id', $unit->manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('manager_id')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- حقل is_active (حالة النشاط) --}}
                    <div class="mb-3 form-check text-end">
                        <input type="hidden" name="is_active" value="0"> {{-- قيمة افتراضية لـ is_active في حال عدم تحديد المربع --}}
                        <input type="checkbox" class="form-check-input float-end @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label me-4" for="is_active">
                            <i class="fas fa-toggle-on me-1"></i> الوحدة نشطة
                        </label>
                        {{-- عرض أخطاء التحقق (Validation Errors) --}}
                        @error('is_active')
                            <div class="invalid-feedback text-end">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- زر الإرسال --}}
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
                {{-- نهاية النموذج --}}
            </div>
        </div>
    </div>
@endsection

{{-- قسم خاص بالـ JavaScript إذا لزم الأمر (مثل مكتبات إضافية) --}}
@push('scripts')
    <script>
        // يمكن إضافة أي سكريبتات خاصة بالنموذج هنا
        console.log('صفحة تعديل الوحدة جاهزة.');
    </script>
@endpush
