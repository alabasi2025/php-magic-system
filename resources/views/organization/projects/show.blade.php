{{-- /home/ubuntu/php-magic-system/resources/views/organization/projects/show.blade.php --}}
{{-- Blade View لعرض تفاصيل مشروع محدد --}}

@extends('layouts.app') {{-- افتراض وجود ملف تخطيط رئيسي --}}

@section('title', 'عرض تفاصيل المشروع: ' . $project->name)

@section('content')
<div class="container mt-5" dir="rtl">
    {{-- عنوان الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary">
            <i class="fas fa-project-diagram me-2"></i>
            عرض تفاصيل المشروع
        </h1>
        {{-- زر العودة إلى قائمة المشاريع --}}
        <a href="{{ route('organization.projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            العودة إلى المشاريع
        </a>
    </div>

    {{-- رسائل الفلاش (Flash Messages) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-end" role="alert">
            <i class="fas fa-check-circle ms-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" dir="ltr"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-end" role="alert">
            <i class="fas fa-times-circle ms-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" dir="ltr"></button>
        </div>
    @endif

    {{-- عرض أخطاء التحقق (Validation Errors) --}}
    @if ($errors->any())
        <div class="alert alert-danger text-end">
            <h4 class="alert-heading"><i class="fas fa-exclamation-triangle ms-2"></i> خطأ في البيانات المدخلة</h4>
            <ul class="list-unstyled mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-caret-left ms-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- بطاقة تفاصيل المشروع الرئيسية (Responsive) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-end">
            <h5 class="mb-0">{{ $project->name }} ({{ $project->code }})</h5>
        </div>
        <div class="card-body">
            <div class="row text-end">
                {{-- تفاصيل عامة --}}
                <div class="col-md-6">
                    <p><strong><i class="fas fa-tag ms-2"></i> الاسم بالإنجليزية:</strong> {{ $project->name_en ?? 'غير متوفر' }}</p>
                    <p><strong><i class="fas fa-code ms-2"></i> الكود:</strong> {{ $project->code }}</p>
                    <p><strong><i class="fas fa-list-alt ms-2"></i> النوع:</strong> {{ $project->type }}</p>
                    <p><strong><i class="fas fa-signal ms-2"></i> الحالة:</strong>
                        <span class="badge bg-{{ $project->status == 'مكتمل' ? 'success' : ($project->status == 'قيد التنفيذ' ? 'warning' : 'danger') }}">
                            {{ $project->status }}
                        </span>
                    </p>
                </div>
                {{-- تفاصيل العلاقات والأولوية --}}
                <div class="col-md-6">
                    {{-- افتراض وجود علاقات Unit و Department --}}
                    <p><strong><i class="fas fa-building ms-2"></i> الوحدة التابعة:</strong>
                        @if ($project->unit)
                            <a href="{{ route('organization.units.show', $project->unit->id) }}">{{ $project->unit->name }}</a>
                        @else
                            غير محدد
                        @endif
                    </p>
                    <p><strong><i class="fas fa-sitemap ms-2"></i> القسم التابع:</strong>
                        @if ($project->department)
                            <a href="{{ route('organization.departments.show', $project->department->id) }}">{{ $project->department->name }}</a>
                        @else
                            غير محدد
                        @endif
                    </p>
                    <p><strong><i class="fas fa-exclamation-circle ms-2"></i> الأولوية:</strong>
                        <span class="badge bg-{{ $project->priority == 'عالية' ? 'danger' : ($project->priority == 'متوسطة' ? 'info' : 'secondary') }}">
                            {{ $project->priority }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted text-end">
            {{-- أزرار الإجراءات --}}
            <a href="{{ route('organization.projects.edit', $project->id) }}" class="btn btn-sm btn-warning ms-2">
                <i class="fas fa-edit ms-1"></i> تعديل
            </a>
            {{-- زر الحذف (يتطلب نموذج) --}}
            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash-alt ms-1"></i> حذف
            </button>
        </div>
    </div>

    {{-- قسم الإحصائيات المالية والتقدم (Statistics) --}}
    <h2 class="h4 text-secondary mb-3 text-end"><i class="fas fa-chart-bar me-2"></i> الإحصائيات المالية والتقدم</h2>
    <div class="row text-end">
        {{-- الميزانية --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-5 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">الميزانية (Budget)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($project->budget, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- التكلفة الفعلية --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-5 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">التكلفة الفعلية (Actual Cost)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($project->actual_cost, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الإيرادات --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-start border-success border-5 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">الإيرادات (Revenue)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($project->revenue, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- التقدم --}}
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-start border-info border-5 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">التقدم (Progress)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $project->progress }}%</div>
                            {{-- شريط التقدم --}}
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $project->progress }}%" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- قسم العلاقات (Relations) --}}
    <h2 class="h4 text-secondary mb-3 text-end"><i class="fas fa-link me-2"></i> العلاقات</h2>
    <div class="row text-end">
        {{-- تفاصيل الوحدة --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i> الوحدة التابعة</h6>
                </div>
                <div class="card-body">
                    @if ($project->unit)
                        <p><strong>الاسم:</strong> {{ $project->unit->name }}</p>
                        <p><strong>الكود:</strong> {{ $project->unit->code }}</p>
                        <a href="{{ route('organization.units.show', $project->unit->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye ms-1"></i> عرض التفاصيل
                        </a>
                    @else
                        <p class="text-muted">لا توجد وحدة تابعة محددة لهذا المشروع.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- تفاصيل القسم --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-sitemap me-2"></i> القسم التابع</h6>
                </div>
                <div class="card-body">
                    @if ($project->department)
                        <p><strong>الاسم:</strong> {{ $project->department->name }}</p>
                        <p><strong>الكود:</strong> {{ $project->department->code }}</p>
                        <a href="{{ route('organization.departments.show', $project->department->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye ms-1"></i> عرض التفاصيل
                        </a>
                    @else
                        <p class="text-muted">لا يوجد قسم تابع محدد لهذا المشروع.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- نموذج (Modal) تأكيد الحذف --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog">
        <div class="modal-content text-end">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-trash-alt ms-2"></i> تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" dir="ltr"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من أنك تريد حذف المشروع <strong>{{ $project->name }}</strong>؟ لا يمكن التراجع عن هذا الإجراء.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('organization.projects.destroy', $project->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">نعم، احذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- قسم السكربتات (Scripts) --}}
@push('scripts')
<script>
    // يمكن إضافة أي سكربتات خاصة بهذه الصفحة هنا
    console.log('صفحة عرض تفاصيل المشروع جاهزة.');
</script>
@endpush
