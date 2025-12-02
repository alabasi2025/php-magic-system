@extends('layouts.app')

@section('title', 'إدارة الشركات القابضة')

@section('content')
    <!-- تعليق: بداية قسم المحتوى الرئيسي -->
    <div class="container-fluid" dir="rtl">
        <h1 class="h3 mb-4 text-gray-800 text-right">الشركات القابضة</h1>

        <!-- تعليق: قسم رسائل الفلاش (Flash Messages) -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-right" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
                <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- تعليق: قسم أخطاء التحقق (Validation Errors) -->
        @if ($errors->any())
            <div class="alert alert-danger text-right">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> خطأ في الإدخال!</h4>
                <ul class="mb-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الشركات القابضة</h6>
                <!-- تعليق: زر إضافة شركة قابضة جديدة -->
                <a href="{{ route('organization.holdings.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-sm me-1"></i> إضافة شركة قابضة
                </a>
            </div>
            <div class="card-body">
                <!-- تعليق: نموذج البحث -->
                <form action="{{ route('organization.holdings.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control text-right" placeholder="ابحث بالاسم أو الكود أو البريد الإلكتروني..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        @if(request('search'))
                            <a href="{{ route('organization.holdings.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> مسح
                            </a>
                        @endif
                    </div>
                </form>

                <!-- تعليق: جدول بيانات الشركات القابضة -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-right" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>الاسم (عربي)</th>
                                <th>الاسم (إنجليزي)</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>الرقم الضريبي</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- تعليق: حلقة تكرار لعرض بيانات الشركات القابضة. يتم افتراض وجود متغير $holdings -->
                            @forelse ($holdings as $holding)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $holding->code }}</td>
                                    <td>{{ $holding->name }}</td>
                                    <td>{{ $holding->name_en }}</td>
                                    <td>{{ $holding->email }}</td>
                                    <td>{{ $holding->phone }}</td>
                                    <td>{{ $holding->tax_number }}</td>
                                    <td>
                                        @if ($holding->is_active)
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> نشط</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- تعليق: زر عرض التفاصيل -->
                                        <a href="{{ route('organization.holdings.show', $holding->id) }}" class="btn btn-info btn-sm" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <!-- تعليق: زر التعديل -->
                                        <a href="{{ route('organization.holdings.edit', $holding->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- تعليق: زر الحذف (نموذج) -->
                                        <form action="{{ route('organization.holdings.destroy', $holding->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه الشركة القابضة؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد شركات قابضة لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- تعليق: قسم الترقيم (Pagination) -->
                <div class="d-flex justify-content-center">
                    {{-- يتم افتراض أن $holdings هو كائن Paginator --}}
                    {{ $holdings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <!-- تعليق: نهاية قسم المحتوى الرئيسي -->
@endsection

@push('scripts')
    <!-- تعليق: يمكن إضافة أي سكريبتات خاصة بهذه الصفحة هنا -->
@endpush
