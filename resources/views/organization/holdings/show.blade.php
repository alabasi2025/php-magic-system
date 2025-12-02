@extends('layouts.app')

{{-- تعيين عنوان الصفحة --}}
@section('title', 'عرض تفاصيل الهيكل التنظيمي: ' . $holding->name)

@section('content')
    <div class="container" dir="rtl">
        <div class="row">
            <div class="col-12">
                {{-- عنوان الصفحة --}}
                <h1 class="mb-4 text-primary">
                    <i class="fas fa-building me-2"></i>
                    عرض تفاصيل الهيكل التنظيمي
                </h1>

                {{-- عرض رسائل الفلاش (Flash Messages) --}}
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

                {{-- بطاقة عرض التفاصيل الأساسية --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            البيانات الأساسية لـ: {{ $holding->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- حقل الكود --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الكود:</p>
                                <p class="text-muted">{{ $holding->code }}</p>
                            </div>

                            {{-- حقل الاسم (العربية) --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الاسم (العربية):</p>
                                <p class="text-muted">{{ $holding->name }}</p>
                            </div>

                            {{-- حقل الاسم (الإنجليزية) --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الاسم (الإنجليزية):</p>
                                <p class="text-muted">{{ $holding->name_en }}</p>
                            </div>

                            {{-- حقل البريد الإلكتروني --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">البريد الإلكتروني:</p>
                                <p class="text-muted">{{ $holding->email }}</p>
                            </div>

                            {{-- حقل الهاتف --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الهاتف:</p>
                                <p class="text-muted">{{ $holding->phone }}</p>
                            </div>

                            {{-- حقل الرقم الضريبي --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الرقم الضريبي:</p>
                                <p class="text-muted">{{ $holding->tax_number }}</p>
                            </div>

                            {{-- حقل حالة النشاط --}}
                            <div class="col-md-6 mb-3">
                                <p class="fw-bold mb-1">الحالة:</p>
                                @if ($holding->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        غير نشط
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- أزرار الإجراءات --}}
                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ route('organization.holdings.edit', $holding->id) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>
                                تعديل
                            </a>
                            <a href="{{ route('organization.holdings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                {{-- قسم الإحصائيات (Placeholder) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            إحصائيات الهيكل التنظيمي
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- مثال على إحصائية --}}
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-sitemap fa-2x text-info mb-2"></i>
                                    <h6 class="fw-bold">عدد الوحدات التابعة</h6>
                                    <p class="fs-4 text-info">5</p> {{-- قيمة وهمية --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-users fa-2x text-info mb-2"></i>
                                    <h6 class="fw-bold">إجمالي الموظفين</h6>
                                    <p class="fs-4 text-info">120</p> {{-- قيمة وهمية --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-project-diagram fa-2x text-info mb-2"></i>
                                    <h6 class="fw-bold">المشاريع النشطة</h6>
                                    <p class="fs-4 text-info">15</p> {{-- قيمة وهمية --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- قسم العلاقات (Placeholder) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>
                            الوحدات التابعة
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- جدول لعرض الوحدات التابعة (مثال) --}}
                        <p class="text-muted">
                            هنا سيتم عرض جدول أو قائمة بالوحدات التابعة لهذا الهيكل التنظيمي (Units).
                            <a href="#" class="btn btn-sm btn-outline-secondary float-start">
                                <i class="fas fa-eye me-1"></i>
                                عرض الكل
                            </a>
                        </p>
                        {{-- يمكن استخدام حلقة Blade لعرض البيانات الفعلية:
                        @if ($holding->units->count() > 0)
                            <ul class="list-group">
                                @foreach ($holding->units as $unit)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $unit->name }}
                                        <a href="{{ route('organization.units.show', $unit->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-light text-center" role="alert">
                                لا توجد وحدات تابعة مسجلة حالياً.
                            </div>
                        @endif
                        --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

{{-- ملاحظة: لا حاجة لقسم خاص بـ Validation Errors في صفحة العرض (Show)، ولكن تم تضمين Flash Messages. --}}
