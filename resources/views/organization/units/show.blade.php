{{-- /home/ubuntu/php-magic-system/resources/views/organization/units/show.blade.php --}}
{{-- عرض تفاصيل الوحدة التنظيمية --}}
@extends('layouts.app')

@section('title', 'عرض تفاصيل الوحدة')

@section('content')
    <div class="container mt-4" dir="rtl">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4 text-right">
                    <i class="fas fa-building me-2"></i>
                    عرض تفاصيل الوحدة: {{ $unit->name }}
                </h1>

                {{-- رسائل الفلاش (Flash Messages) --}}
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

                {{-- لا يوجد حاجة لأخطاء التحقق (Validation Errors) في صفحة العرض (Show)، ولكن نتركها كنموذج --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> خطأ في البيانات</h4>
                        <ul class="mb-0 list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- بطاقة عرض التفاصيل الرئيسية --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات الوحدة الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- حقل اسم الوحدة --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">الاسم:</p>
                                <p>{{ $unit->name }}</p>
                            </div>
                            {{-- حقل الكود --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">الكود:</p>
                                <p>{{ $unit->code }}</p>
                            </div>
                            {{-- حقل الشركة القابضة (العلاقة) --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">الشركة القابضة:</p>
                                <p>
                                    @if ($unit->holding)
                                        <a href="{{ route('organization.holdings.show', $unit->holding_id) }}" class="text-decoration-none">
                                            <i class="fas fa-sitemap me-1"></i> {{ $unit->holding->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير محددة</span>
                                    @endif
                                </p>
                            </div>
                            {{-- حقل النوع --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">النوع:</p>
                                <p>{{ $unit->type }}</p>
                            </div>
                            {{-- حقل المدير (العلاقة) --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">المدير المسؤول:</p>
                                <p>
                                    @if ($unit->manager)
                                        <a href="{{ route('users.show', $unit->manager_id) }}" class="text-decoration-none">
                                            <i class="fas fa-user-tie me-1"></i> {{ $unit->manager->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </p>
                            </div>
                            {{-- حقل حالة النشاط --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold">حالة النشاط:</p>
                                <p>
                                    @if ($unit->is_active)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> نشط</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> غير نشط</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        {{-- زر التعديل --}}
                        <a href="{{ route('organization.units.edit', $unit->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> تعديل
                        </a>
                        {{-- زر العودة --}}
                        <a href="{{ route('organization.units.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                        </a>
                    </div>
                </div>

                {{-- قسم الإحصائيات (مثال) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> إحصائيات الوحدة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <i class="fas fa-project-diagram fa-2x text-primary mb-2"></i>
                                    <h6 class="fw-bold">عدد الأقسام</h6>
                                    <p class="fs-4">{{ $unit->departments_count ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <i class="fas fa-tasks fa-2x text-success mb-2"></i>
                                    <h6 class="fw-bold">عدد المشاريع</h6>
                                    <p class="fs-4">{{ $unit->projects_count ?? 0 }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light">
                                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                                    <h6 class="fw-bold">عدد الموظفين</h6>
                                    <p class="fs-4">{{ $unit->employees_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- قسم العلاقات (مثال: الأقسام التابعة) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> الأقسام التابعة</h5>
                    </div>
                    <div class="card-body">
                        {{-- مثال على جدول بسيط لعرض الأقسام --}}
                        @if ($unit->departments && $unit->departments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped text-right">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-hashtag me-1"></i> الكود</th>
                                            <th><i class="fas fa-tag me-1"></i> الاسم</th>
                                            <th><i class="fas fa-user-tie me-1"></i> المدير</th>
                                            <th><i class="fas fa-cogs me-1"></i> الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unit->departments as $department)
                                            <tr>
                                                <td>{{ $department->code }}</td>
                                                <td>{{ $department->name }}</td>
                                                <td>{{ $department->manager->name ?? 'غير محدد' }}</td>
                                                <td>
                                                    <a href="{{ route('organization.departments.show', $department->id) }}" class="btn btn-sm btn-info text-white">
                                                        <i class="fas fa-eye"></i> عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> لا توجد أقسام تابعة لهذه الوحدة حالياً.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

{{-- ملاحظة: يجب أن يكون ملف layouts/app.blade.php موجوداً ويحتوي على تضمين Bootstrap 5 و Font Awesome ودعم RTL --}}
