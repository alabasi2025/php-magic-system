@extends('layouts.app')

{{-- تعليق: تحديد عنوان الصفحة --}}
@section('title', 'عرض تفاصيل القسم: ' . $department->name)

@section('content')
    {{-- تعليق: استخدام حاوية Bootstrap 5 مع دعم RTL --}}
    <div class="container mt-4" dir="rtl">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4 text-primary">
                    <i class="fas fa-building me-2"></i>
                    تفاصيل القسم: {{ $department->name }}
                </h1>

                {{-- تعليق: عرض رسائل الفلاش (Flash Messages) --}}
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

                {{-- تعليق: عرض أخطاء التحقق (Validation Errors) - على الرغم من أن صفحة العرض لا تتطلب تحققًا، يتم تضمينها للمحافظة على التناسق في حال وجود نماذج فرعية --}}
                @if ($errors->any())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- تعليق: بطاقة عرض التفاصيل الأساسية للقسم --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات أساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-hashtag me-2"></i> كود القسم:</strong> {{ $department->code }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-signature me-2"></i> اسم القسم:</strong> {{ $department->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- افتراض وجود علاقة Unit --}}
                                <p><strong><i class="fas fa-sitemap me-2"></i> الوحدة التابع لها:</strong>
                                    @if ($department->unit)
                                        <a href="{{ route('organization.units.show', $department->unit->id) }}">{{ $department->unit->name }}</a>
                                    @else
                                        غير محدد
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-tag me-2"></i> نوع القسم:</strong> {{ $department->type }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- افتراض وجود علاقة Manager --}}
                                <p><strong><i class="fas fa-user-tie me-2"></i> مدير القسم:</strong>
                                    @if ($department->manager)
                                        {{ $department->manager->name }}
                                    @else
                                        غير محدد
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-money-bill-wave me-2"></i> الميزانية المخصصة:</strong> {{ number_format($department->budget, 2) }} ريال</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-toggle-on me-2"></i> الحالة:</strong>
                                    @if ($department->is_active)
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> نشط</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> غير نشط</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        {{-- تعليق: أزرار الإجراءات --}}
                        <a href="{{ route('organization.departments.edit', $department->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i> تعديل
                        </a>
                        <a href="{{ route('organization.departments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                        </a>
                    </div>
                </div>

                {{-- تعليق: قسم الإحصائيات --}}
                <h2 class="mb-3 mt-5 text-secondary"><i class="fas fa-chart-bar me-2"></i> إحصائيات القسم</h2>
                <div class="row">
                    {{-- افتراض وجود دالة لحساب الإحصائيات مثل $department->projects->count() --}}
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-info h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase">إجمالي المشاريع</h6>
                                        <h3 class="display-6">{{ $department->projects->count() ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-project-diagram fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-success h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase">إجمالي الإيرادات</h6>
                                        {{-- افتراض وجود دالة لحساب الإيرادات --}}
                                        <h3 class="display-6">{{ number_format($department->projects->sum('revenue') ?? 0, 2) }}</h3>
                                    </div>
                                    <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-white bg-warning h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-uppercase">الميزانية المتبقية</h6>
                                        {{-- افتراض وجود دالة لحساب التكلفة الفعلية --}}
                                        @php
                                            $actual_cost = $department->projects->sum('actual_cost') ?? 0;
                                            $remaining_budget = $department->budget - $actual_cost;
                                        @endphp
                                        <h3 class="display-6">{{ number_format($remaining_budget, 2) }}</h3>
                                    </div>
                                    <i class="fas fa-wallet fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تعليق: قسم العلاقات (المشاريع التابعة للقسم) --}}
                <h2 class="mb-3 mt-5 text-secondary"><i class="fas fa-tasks me-2"></i> المشاريع التابعة للقسم</h2>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        @if ($department->projects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> الكود</th>
                                            <th><i class="fas fa-signature"></i> اسم المشروع</th>
                                            <th><i class="fas fa-tag"></i> النوع</th>
                                            <th><i class="fas fa-percent"></i> التقدم</th>
                                            <th><i class="fas fa-info-circle"></i> الحالة</th>
                                            <th><i class="fas fa-cogs"></i> الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($department->projects as $project)
                                            <tr>
                                                <td>{{ $project->code }}</td>
                                                <td>{{ $project->name }}</td>
                                                <td>{{ $project->type }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px; direction: ltr;">
                                                        <div class="progress-bar progress-bar-striped {{ $project->progress == 100 ? 'bg-success' : 'bg-info' }}"
                                                             role="progressbar" style="width: {{ $project->progress }}%"
                                                             aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $project->progress }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $project->status == 'مكتمل' ? 'success' : ($project->status == 'قيد التنفيذ' ? 'primary' : 'warning') }}">
                                                        {{ $project->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('organization.projects.show', $project->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i> لا توجد مشاريع تابعة لهذا القسم حاليًا.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

{{-- تعليق: قسم السكربتات الإضافية (يمكن إضافة سكربتات Bootstrap هنا إذا لم تكن في الـ layout الرئيسي) --}}
@push('scripts')
    <script>
        // يمكن إضافة أي سكربتات خاصة بهذه الصفحة هنا
        console.log('صفحة عرض تفاصيل القسم جاهزة.');
    </script>
@endpush
