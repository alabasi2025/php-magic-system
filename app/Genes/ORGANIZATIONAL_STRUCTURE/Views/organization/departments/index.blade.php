@extends('layouts.app')

{{-- تعليق: هذا الملف هو Blade View لعرض قائمة الإدارات (Departments) --}}
{{-- يستخدم Bootstrap 5 ويدعم اللغة العربية (RTL) مع أيقونات Font Awesome --}}

@section('title', 'إدارة الإدارات')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="row">
        <div class="col-12">
            <h1 class="text-right mb-4">
                <i class="fas fa-building"></i> إدارة الإدارات
            </h1>

            {{-- تعليق: قسم الرسائل الومضية (Flash Messages) --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show text-right" role="alert">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
                    <i class="fas fa-times-circle ml-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- تعليق: قسم أخطاء التحقق (Validation Errors) --}}
            @if ($errors->any())
                <div class="alert alert-danger text-right">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle ml-2"></i> خطأ في البيانات المدخلة</h4>
                    <ul class="list-unstyled mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">قائمة الإدارات</h6>
                    {{-- تعليق: زر إضافة جديد --}}
                    <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle ml-1"></i> إضافة إدارة جديدة
                    </a>
                </div>
                <div class="card-body">
                    {{-- تعليق: نموذج البحث --}}
                    <form action="{{ route('departments.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm text-right" placeholder="ابحث بالرمز أو الاسم..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary btn-sm" type="submit">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            @if(request('search'))
                                <a href="{{ route('departments.index') }}" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times"></i> مسح
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- تعليق: جدول عرض البيانات --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-right" id="departmentsTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>الرمز</th>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>المدير</th>
                                    <th>الميزانية</th>
                                    <th>الوحدة التابعة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- تعليق: حلقة تكرار لعرض بيانات الإدارات --}}
                                @forelse ($departments as $department)
                                <tr>
                                    <td>{{ $department->code }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->type }}</td>
                                    <td>{{ $department->manager->name ?? 'غير محدد' }}</td>
                                    <td>{{ number_format($department->budget, 2) }}</td>
                                    <td>{{ $department->unit->name ?? 'غير محدد' }}</td>
                                    <td>
                                        @if ($department->is_active)
                                            <span class="badge bg-success text-white">نشط</span>
                                        @else
                                            <span class="badge bg-danger text-white">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- تعليق: أزرار الإجراءات (عرض، تعديل، حذف) --}}
                                        <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه الإدارة؟')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <i class="fas fa-info-circle"></i> لا توجد إدارات مسجلة حالياً.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- تعليق: قسم الـ Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $departments->links('pagination::bootstrap-5') }}
                    </div>
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
    console.log('صفحة الإدارات جاهزة.');
</script>
@endpush
