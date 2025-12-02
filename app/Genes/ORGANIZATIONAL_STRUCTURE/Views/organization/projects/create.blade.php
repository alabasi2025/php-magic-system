@extends('layouts.app') {{-- افتراض وجود تخطيط أساسي يدعم Bootstrap 5 و RTL --}}

{{-- تعيين عنوان الصفحة --}}
@section('title', 'إنشاء مشروع جديد')

@section('content')
    {{-- التعليق: حاوية رئيسية بتصميم متجاوب ودعم RTL --}}
    <div class="container mt-5" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{-- التعليق: بطاقة النموذج --}}
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i> إنشاء مشروع جديد</h3>
                    </div>
                    <div class="card-body">

                        {{-- التعليق: قسم رسائل الفلاش (Flash Messages) --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- التعليق: نموذج إنشاء المشروع --}}
                        <form action="{{ route('organization.projects.store') }}" method="POST">
                            @csrf {{-- حماية CSRF --}}

                            {{-- التعليق: حقول المفاتيح الخارجية (يفترض أنها تُملأ من قوائم منسدلة) --}}
                            <div class="row">
                                {{-- حقل الوحدة (Unit ID) --}}
                                <div class="col-md-6 mb-3">
                                    <label for="unit_id" class="form-label required">
                                        <i class="fas fa-building me-1"></i> الوحدة التابعة
                                    </label>
                                    {{-- التعليق: يجب استبدال هذا بقائمة منسدلة حقيقية في التطبيق --}}
                                    <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                        <option value="">اختر الوحدة</option>
                                        {{-- @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach --}}
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل القسم (Department ID) --}}
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label required">
                                        <i class="fas fa-sitemap me-1"></i> القسم التابع
                                    </label>
                                    {{-- التعليق: يجب استبدال هذا بقائمة منسدلة حقيقية في التطبيق --}}
                                    <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                        <option value="">اختر القسم</option>
                                        {{-- @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach --}}
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- التعليق: حقول البيانات الأساسية --}}
                            <div class="row">
                                {{-- حقل رمز المشروع (Code) --}}
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label required">
                                        <i class="fas fa-barcode me-1"></i> رمز المشروع
                                    </label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل اسم المشروع (Name) --}}
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">
                                        <i class="fas fa-file-signature me-1"></i> اسم المشروع
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- التعليق: حقول النوع والأولوية والحالة --}}
                            <div class="row">
                                {{-- حقل نوع المشروع (Type) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="type" class="form-label">
                                        <i class="fas fa-tag me-1"></i> النوع
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">اختر النوع</option>
                                        <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>داخلي</option>
                                        <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>خارجي</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل الأولوية (Priority) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="priority" class="form-label">
                                        <i class="fas fa-exclamation-triangle me-1"></i> الأولوية
                                    </label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                        <option value="">اختر الأولوية</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل الحالة (Status) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-info-circle me-1"></i> الحالة
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="">اختر الحالة</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- التعليق: حقول الميزانية والتكاليف والإيرادات --}}
                            <div class="row">
                                {{-- حقل الميزانية (Budget) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="budget" class="form-label">
                                        <i class="fas fa-money-bill-wave me-1"></i> الميزانية التقديرية
                                    </label>
                                    <input type="number" step="0.01" class="form-control @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget') }}">
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل التكلفة الفعلية (Actual Cost) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="actual_cost" class="form-label">
                                        <i class="fas fa-hand-holding-usd me-1"></i> التكلفة الفعلية
                                    </label>
                                    <input type="number" step="0.01" class="form-control @error('actual_cost') is-invalid @enderror" id="actual_cost" name="actual_cost" value="{{ old('actual_cost') }}">
                                    @error('actual_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- حقل الإيرادات (Revenue) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="revenue" class="form-label">
                                        <i class="fas fa-chart-line me-1"></i> الإيرادات المتوقعة
                                    </label>
                                    <input type="number" step="0.01" class="form-control @error('revenue') is-invalid @enderror" id="revenue" name="revenue" value="{{ old('revenue') }}">
                                    @error('revenue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- التعليق: حقل التقدم --}}
                            <div class="mb-4">
                                <label for="progress" class="form-label">
                                    <i class="fas fa-tasks me-1"></i> نسبة التقدم (%)
                                </label>
                                <input type="range" class="form-range @error('progress') is-invalid @enderror" min="0" max="100" step="1" id="progress" name="progress" value="{{ old('progress', 0) }}" oninput="this.nextElementSibling.value = this.value">
                                <output class="badge bg-info text-dark">{{ old('progress', 0) }}</output>%
                                @error('progress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- التعليق: أزرار الإجراءات --}}
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success btn-lg me-md-2">
                                    <i class="fas fa-save me-1"></i> حفظ المشروع
                                </button>
                                <a href="{{ route('organization.projects.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-right me-1"></i> إلغاء والعودة
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- التعليق: قسم السكريبتات الإضافية (لتحسين تجربة المستخدم) --}}
@push('scripts')
<script>
    // التعليق: سكريبت لتحديث قيمة التقدم عند تحريك شريط التمرير
    document.addEventListener('DOMContentLoaded', function() {
        const progressInput = document.getElementById('progress');
        const progressOutput = progressInput.nextElementSibling;

        progressInput.addEventListener('input', function() {
            progressOutput.textContent = this.value;
        });
    });
</script>
@endpush
