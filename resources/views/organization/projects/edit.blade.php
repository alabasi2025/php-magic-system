@extends('layouts.app')

{{--
    ====================================================================================================================
    ملف Blade View لنموذج تعديل مشروع (projects/edit)
    المتطلبات:
    1. دعم RTL (العربية)
    2. Bootstrap 5
    3. Font Awesome Icons
    4. Flash Messages
    5. Validation Errors
    6. تصميم متجاوب (Responsive Design)
    ====================================================================================================================
--}}

@section('title', 'تعديل المشروع: ' . $project->name)

@section('content')
    <div class="container mt-5" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i> تعديل بيانات المشروع</h4>
                    </div>
                    <div class="card-body p-4">

                        {{-- ==========================================================================================
                            قسم رسائل الفلاش (Flash Messages)
                            يتم عرض رسائل النجاح أو الخطأ من الجلسة (Session)
                        ========================================================================================== --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show text-end" role="alert">
                                <i class="fas fa-check-circle ms-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show text-end" role="alert">
                                <i class="fas fa-exclamation-triangle ms-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- ==========================================================================================
                            نموذج التعديل
                            يستخدم طريقة POST مع حقل مخفي لتمرير PUT
                        ========================================================================================== --}}
                        <form action="{{ route('organization.projects.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                {{-- حقل الكود --}}
                                <div class="col-md-6">
                                    <label for="code" class="form-label d-block text-end">
                                        <i class="fas fa-barcode ms-1"></i> كود المشروع
                                    </label>
                                    <input type="text" class="form-control text-end @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $project->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الاسم --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label d-block text-end">
                                        <i class="fas fa-file-signature ms-1"></i> اسم المشروع
                                    </label>
                                    <input type="text" class="form-control text-end @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $project->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الوحدة التابع لها (unit_id) --}}
                                <div class="col-md-6">
                                    <label for="unit_id" class="form-label d-block text-end">
                                        <i class="fas fa-building ms-1"></i> الوحدة التابع لها
                                    </label>
                                    <select class="form-select text-end @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                        <option value="" disabled>اختر الوحدة</option>
                                        {{-- افتراض وجود متغير $units يحمل الوحدات --}}
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $project->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل القسم التابع له (department_id) --}}
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label d-block text-end">
                                        <i class="fas fa-sitemap ms-1"></i> القسم التابع له
                                    </label>
                                    <select class="form-select text-end @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                        <option value="" disabled>اختر القسم</option>
                                        {{-- افتراض وجود متغير $departments يحمل الأقسام --}}
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $project->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل النوع (type) --}}
                                <div class="col-md-4">
                                    <label for="type" class="form-label d-block text-end">
                                        <i class="fas fa-tag ms-1"></i> نوع المشروع
                                    </label>
                                    <select class="form-select text-end @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="" disabled>اختر النوع</option>
                                        {{-- افتراض وجود متغير $projectTypes يحمل أنواع المشاريع --}}
                                        @foreach (['داخلي', 'خارجي', 'استثماري'] as $projectType)
                                            <option value="{{ $projectType }}" {{ old('type', $project->type) == $projectType ? 'selected' : '' }}>
                                                {{ $projectType }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الأولوية (priority) --}}
                                <div class="col-md-4">
                                    <label for="priority" class="form-label d-block text-end">
                                        <i class="fas fa-star ms-1"></i> الأولوية
                                    </label>
                                    <select class="form-select text-end @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="" disabled>اختر الأولوية</option>
                                        {{-- افتراض وجود متغير $priorities يحمل مستويات الأولوية --}}
                                        @foreach (['منخفضة', 'متوسطة', 'عالية', 'عاجلة'] as $priority)
                                            <option value="{{ $priority }}" {{ old('priority', $project->priority) == $priority ? 'selected' : '' }}>
                                                {{ $priority }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الحالة (status) --}}
                                <div class="col-md-4">
                                    <label for="status" class="form-label d-block text-end">
                                        <i class="fas fa-info-circle ms-1"></i> حالة المشروع
                                    </label>
                                    <select class="form-select text-end @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="" disabled>اختر الحالة</option>
                                        {{-- افتراض وجود متغير $statuses يحمل حالات المشروع --}}
                                        @foreach (['مخطط', 'قيد التنفيذ', 'متوقف', 'مكتمل', 'ملغى'] as $status)
                                            <option value="{{ $status }}" {{ old('status', $project->status) == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الميزانية (budget) --}}
                                <div class="col-md-4">
                                    <label for="budget" class="form-label d-block text-end">
                                        <i class="fas fa-money-bill-wave ms-1"></i> الميزانية التقديرية
                                    </label>
                                    <input type="number" step="0.01" class="form-control text-end @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" required>
                                    @error('budget')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل التكلفة الفعلية (actual_cost) --}}
                                <div class="col-md-4">
                                    <label for="actual_cost" class="form-label d-block text-end">
                                        <i class="fas fa-hand-holding-usd ms-1"></i> التكلفة الفعلية
                                    </label>
                                    <input type="number" step="0.01" class="form-control text-end @error('actual_cost') is-invalid @enderror" id="actual_cost" name="actual_cost" value="{{ old('actual_cost', $project->actual_cost) }}">
                                    @error('actual_cost')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل الإيرادات (revenue) --}}
                                <div class="col-md-4">
                                    <label for="revenue" class="form-label d-block text-end">
                                        <i class="fas fa-chart-line ms-1"></i> الإيرادات المتوقعة/المحققة
                                    </label>
                                    <input type="number" step="0.01" class="form-control text-end @error('revenue') is-invalid @enderror" id="revenue" name="revenue" value="{{ old('revenue', $project->revenue) }}">
                                    @error('revenue')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- حقل التقدم (progress) --}}
                                <div class="col-12">
                                    <label for="progress" class="form-label d-block text-end">
                                        <i class="fas fa-tasks ms-1"></i> نسبة التقدم: <span id="progressValue">{{ old('progress', $project->progress) }}</span>%
                                    </label>
                                    <input type="range" class="form-range @error('progress') is-invalid @enderror" min="0" max="100" step="1" id="progress" name="progress" value="{{ old('progress', $project->progress) }}" oninput="document.getElementById('progressValue').innerText = this.value">
                                    @error('progress')
                                        <div class="invalid-feedback text-end">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- زر الإرسال --}}
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-save ms-2"></i> حفظ التعديلات
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- ==========================================================================================
                            نهاية نموذج التعديل
                        ========================================================================================== --}}

                    </div>
                    <div class="card-footer text-muted text-center">
                        <a href="{{ route('organization.projects.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-right ms-1"></i> العودة إلى قائمة المشاريع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--
    ====================================================================================================================
    نهاية ملف Blade View لنموذج تعديل مشروع
    ====================================================================================================================
--}}
