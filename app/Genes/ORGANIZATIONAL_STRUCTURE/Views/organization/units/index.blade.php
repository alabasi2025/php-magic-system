@extends('layouts.app')

@section('title', 'إدارة الوحدات التنظيمية')

@section('content')
    {{-- حاوية الصفحة الرئيسية مع دعم RTL --}}
    <div class="container-fluid" dir="rtl">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 text-gray-800 text-right">إدارة الوحدات التنظيمية</h1>

                {{-- رسائل الفلاش (Flash Messages) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-right" role="alert">
                        <i class="fas fa-check-circle ml-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
                        <i class="fas fa-times-circle ml-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- نموذج البحث والإضافة --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">قائمة الوحدات</h6>
                        <a href="{{ route('organization.units.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus ml-1"></i> إضافة وحدة جديدة
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('organization.units.index') }}" method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm text-right" placeholder="ابحث بالاسم أو الكود..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('organization.units.index') }}" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times"></i> مسح
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        {{-- جدول البيانات (Data Table) --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-right" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الشركة القابضة</th>
                                        <th>الكود</th>
                                        <th>الاسم</th>
                                        <th>النوع</th>
                                        <th>المدير</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- حلقة تكرارية لعرض بيانات الوحدات --}}
                                    @forelse ($units as $unit)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $unit->holding->name ?? 'غير محدد' }}</td>
                                            <td>{{ $unit->code }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->type }}</td>
                                            <td>{{ $unit->manager->name ?? 'غير محدد' }}</td>
                                            <td>
                                                @if ($unit->is_active)
                                                    <span class="badge badge-success">نشط</span>
                                                @else
                                                    <span class="badge badge-danger">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- زر عرض التفاصيل --}}
                                                <a href="{{ route('organization.units.show', $unit->id) }}" class="btn btn-info btn-sm" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- زر التعديل --}}
                                                <a href="{{ route('organization.units.edit', $unit->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- زر الحذف (نموذج) --}}
                                                <form action="{{ route('organization.units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذه الوحدة؟')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد وحدات تنظيمية لعرضها.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- روابط التنقل بين الصفحات (Pagination) --}}
                        <div class="d-flex justify-content-center">
                            {{ $units->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- يمكن إضافة أكواد JavaScript خاصة بالصفحة هنا، مثل تهيئة مكتبة DataTables أو أكواد الحذف عبر AJAX --}}
@endpush
