@extends('layouts.app')

{{-- تعليق: بداية محتوى تعديل القسم --}}
@section('title', 'تعديل بيانات القسم')

@section('content')
    <div class="container py-4" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i> تعديل بيانات القسم</h4>
                        <a href="{{ route('organization.departments.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-list-ul me-1"></i> قائمة الأقسام
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- تعليق: عرض رسائل الفلاش (النجاح/الخطأ) --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show text-end" role="alert">
                                <i class="fas fa-check-circle ms-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show text-end" role="alert">
                                <i class="fas fa-times-circle ms-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- تعليق: نموذج التعديل --}}
                        <form action="{{ route('organization.departments.update', $department->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- تعليق: حقل الوحدة التابع لها (unit_id) --}}
                            <div class="mb-3">
                                <label for="unit_id" class="form-label required"><i class="fas fa-sitemap me-1"></i> الوحدة التابع لها</label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                    <option value="">اختر الوحدة</option>
                                    {{-- تعليق: يجب تمرير قائمة الوحدات ($units) من الـ Controller --}}
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $department->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل الكود (code) --}}
                            <div class="mb-3">
                                <label for="code" class="form-label required"><i class="fas fa-barcode me-1"></i> كود القسم</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $department->code) }}" required maxlength="10">
                                @error('code')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل الاسم (name) --}}
                            <div class="mb-3">
                                <label for="name" class="form-label required"><i class="fas fa-tag me-1"></i> اسم القسم (عربي)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required maxlength="100">
                                @error('name')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل النوع (type) --}}
                            <div class="mb-3">
                                <label for="type" class="form-label"><i class="fas fa-layer-group me-1"></i> نوع القسم</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="">اختر النوع (اختياري)</option>
                                    {{-- تعليق: يجب تمرير قائمة أنواع الأقسام ($departmentTypes) من الـ Controller --}}
                                    @foreach (['تشغيلي', 'دعم', 'استراتيجي'] as $type)
                                        <option value="{{ $type }}" {{ old('type', $department->type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل المدير المسؤول (manager_id) --}}
                            <div class="mb-3">
                                <label for="manager_id" class="form-label"><i class="fas fa-user-tie me-1"></i> المدير المسؤول</label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                    <option value="">اختر المدير (اختياري)</option>
                                    {{-- تعليق: يجب تمرير قائمة الموظفين ($managers) من الـ Controller --}}
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id', $department->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل الميزانية (budget) --}}
                            <div class="mb-3">
                                <label for="budget" class="form-label"><i class="fas fa-money-bill-wave me-1"></i> الميزانية التقديرية (اختياري)</label>
                                <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget', $department->budget) }}" min="0">
                                @error('budget')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: حقل حالة التفعيل (is_active) --}}
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active"><i class="fas fa-toggle-on me-1"></i> القسم مفعل</label>
                                @error('is_active')
                                    <div class="invalid-feedback text-end">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- تعليق: زر حفظ التعديلات --}}
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i> حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
{{-- تعليق: نهاية محتوى تعديل القسم --}}
