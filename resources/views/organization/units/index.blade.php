@extends('layouts.app')

@section('title', 'إدارة الوحدات التنظيمية')

@section('content')
    {{-- حاوية الصفحة الرئيسية مع دعم RTL --}}
    <div class="container-fluid" dir="rtl">
        
        {{-- العنوان الرئيسي --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-sitemap text-primary"></i> إدارة الوحدات التنظيمية
            </h1>
            <a href="{{ route('organization.units.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 ml-1"></i> إضافة وحدة جديدة
            </a>
        </div>

        {{-- رسائل الفلاش (Flash Messages) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-right shadow-sm" role="alert">
                <i class="fas fa-check-circle ml-2"></i> 
                <strong>نجاح!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-right shadow-sm" role="alert">
                <i class="fas fa-times-circle ml-2"></i> 
                <strong>خطأ!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- بطاقات الإحصائيات --}}
        <div class="row mb-4">
            {{-- إجمالي الوحدات --}}
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-right-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col ml-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الوحدات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUnits }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الوحدات النشطة --}}
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-right-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col ml-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    الوحدات النشطة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUnits }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الوحدات غير النشطة --}}
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-right-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col ml-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    الوحدات غير النشطة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveUnits }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- بطاقة الجدول الرئيسية --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list ml-1"></i> قائمة الوحدات التنظيمية
                        </h6>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-success" onclick="window.print()">
                            <i class="fas fa-print ml-1"></i> طباعة
                        </button>
                        <button class="btn btn-sm btn-info" onclick="exportToExcel()">
                            <i class="fas fa-file-excel ml-1"></i> تصدير Excel
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                {{-- نموذج البحث والفلاتر المتقدمة --}}
                <form action="{{ route('organization.units.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        {{-- حقل البحث --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control text-right" 
                                       placeholder="ابحث بالاسم أو الكود..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- فلتر الشركة القابضة --}}
                        <div class="col-md-3 mb-3">
                            <select name="holding_id" class="form-control text-right">
                                <option value="">كل الشركات القابضة</option>
                                @foreach($holdings as $holding)
                                    <option value="{{ $holding->id }}" {{ request('holding_id') == $holding->id ? 'selected' : '' }}>
                                        {{ $holding->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- فلتر النوع --}}
                        <div class="col-md-2 mb-3">
                            <select name="type" class="form-control text-right">
                                <option value="">كل الأنواع</option>
                                <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>شركة</option>
                                <option value="branch" {{ request('type') == 'branch' ? 'selected' : '' }}>فرع</option>
                                <option value="department" {{ request('type') == 'department' ? 'selected' : '' }}>قسم</option>
                            </select>
                        </div>

                        {{-- فلتر الحالة --}}
                        <div class="col-md-2 mb-3">
                            <select name="status" class="form-control text-right">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>

                        {{-- زر مسح الفلاتر --}}
                        <div class="col-md-1 mb-3">
                            @if(request()->hasAny(['search', 'holding_id', 'type', 'status']))
                                <a href="{{ route('organization.units.index') }}" class="btn btn-outline-danger btn-block">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- جدول البيانات المحسّن --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-right align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th><i class="fas fa-building text-primary ml-1"></i> الشركة القابضة</th>
                                <th><i class="fas fa-barcode text-info ml-1"></i> الكود</th>
                                <th><i class="fas fa-tag text-success ml-1"></i> الاسم</th>
                                <th class="text-center"><i class="fas fa-layer-group text-warning ml-1"></i> النوع</th>
                                <th><i class="fas fa-user-tie text-secondary ml-1"></i> المدير</th>
                                <th class="text-center" style="width: 100px;"><i class="fas fa-toggle-on text-success ml-1"></i> الحالة</th>
                                <th class="text-center" style="width: 150px;"><i class="fas fa-cogs text-danger ml-1"></i> الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($units as $unit)
                                <tr class="hover-row">
                                    <td class="text-center font-weight-bold">{{ $loop->iteration + ($units->currentPage() - 1) * $units->perPage() }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-light">
                                            {{ $unit->holding->name ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $unit->code }}</code>
                                    </td>
                                    <td class="font-weight-bold">{{ $unit->name }}</td>
                                    <td class="text-center">
                                        @if($unit->type == 'company')
                                            <span class="badge badge-primary">
                                                <i class="fas fa-building ml-1"></i> شركة
                                            </span>
                                        @elseif($unit->type == 'branch')
                                            <span class="badge badge-info">
                                                <i class="fas fa-code-branch ml-1"></i> فرع
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-sitemap ml-1"></i> {{ $unit->type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($unit->manager)
                                            <i class="fas fa-user-circle text-success ml-1"></i>
                                            {{ $unit->manager->name }}
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash ml-1"></i> غير محدد
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($unit->is_active)
                                            <span class="badge badge-success badge-pill px-3 py-2">
                                                <i class="fas fa-check-circle ml-1"></i> نشط
                                            </span>
                                        @else
                                            <span class="badge badge-danger badge-pill px-3 py-2">
                                                <i class="fas fa-times-circle ml-1"></i> غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            {{-- زر عرض التفاصيل --}}
                                            <a href="{{ route('organization.units.show', $unit->id) }}" 
                                               class="btn btn-info btn-sm" 
                                               data-toggle="tooltip" 
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- زر التعديل --}}
                                            <a href="{{ route('organization.units.edit', $unit->id) }}" 
                                               class="btn btn-warning btn-sm" 
                                               data-toggle="tooltip" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- زر الحذف --}}
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm" 
                                                    data-toggle="tooltip" 
                                                    title="حذف"
                                                    onclick="confirmDelete({{ $unit->id }}, '{{ $unit->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        {{-- نموذج الحذف المخفي --}}
                                        <form id="delete-form-{{ $unit->id }}" 
                                              action="{{ route('organization.units.destroy', $unit->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">لا توجد وحدات تنظيمية لعرضها.</p>
                                        <a href="{{ route('organization.units.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus ml-1"></i> إضافة وحدة جديدة
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- روابط التنقل بين الصفحات (Pagination) المحسّنة --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        عرض {{ $units->firstItem() ?? 0 }} إلى {{ $units->lastItem() ?? 0 }} من أصل {{ $units->total() }} وحدة
                    </div>
                    <div>
                        {{ $units->links() }}
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('styles')
<style>
    /* تحسينات CSS مخصصة */
    .hover-row {
        transition: all 0.3s ease;
    }
    
    .hover-row:hover {
        background-color: #f8f9fc;
        transform: scale(1.01);
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    
    .border-right-primary {
        border-right: 0.25rem solid #4e73df !important;
    }
    
    .border-right-success {
        border-right: 0.25rem solid #1cc88a !important;
    }
    
    .border-right-warning {
        border-right: 0.25rem solid #f6c23e !important;
    }
    
    .badge-pill {
        border-radius: 10rem;
    }
    
    .btn-group .btn {
        margin: 0 2px;
    }
    
    code {
        font-size: 90%;
        padding: 0.2rem 0.4rem;
        background-color: #f1f3f5;
        border-radius: 0.25rem;
    }
    
    @media print {
        .card-header .col-auto,
        .btn-group,
        form {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // تفعيل Tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // دالة تأكيد الحذف المحسّنة
    function confirmDelete(unitId, unitName) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            html: `سيتم حذف الوحدة: <strong>${unitName}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + unitId).submit();
            }
        });
    }

    // دالة تصدير إلى Excel (بسيطة)
    function exportToExcel() {
        // يمكن تطويرها لاحقاً باستخدام مكتبة مثل SheetJS
        window.location.href = '{{ route("organization.units.index") }}?export=excel';
    }
</script>
@endpush
