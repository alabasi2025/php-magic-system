{{-- /home/ubuntu/php-magic-system/resources/views/organization/projects/index.blade.php --}}
{{-- Blade View لصفحة فهرس المشاريع (Projects Index) --}}
{{-- المتطلبات: Bootstrap 5, RTL, Responsive, Font Awesome, Flash Messages, Validation Errors --}}

@extends('layouts.app') {{-- افتراض وجود ملف تخطيط رئيسي (Layout) --}}

@section('title', 'إدارة المشاريع')

@section('content')
    {{-- استخدام dir="rtl" لضمان دعم RTL في حال لم يكن مضمناً في التخطيط الرئيسي --}}
    <div class="container-fluid" dir="rtl">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4 text-right">
                    {{-- أيقونة Font Awesome للمشاريع --}}
                    <i class="fas fa-project-diagram"></i> إدارة المشاريع
                </h1>

                {{-- قسم رسائل الفلاش (Flash Messages) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-right" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- قسم أخطاء التحقق (Validation Errors) - يمكن استخدامه لعرض أخطاء عامة --}}
                @if ($errors->any())
                    <div class="alert alert-danger text-right">
                        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> خطأ في الإدخال!</h4>
                        <ul class="mb-0 list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">قائمة المشاريع</h6>
                        {{-- زر الإضافة (Add Button) --}}
                        <a href="{{ route('organization.projects.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle"></i> إضافة مشروع جديد
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- نموذج البحث (Search Form) --}}
                        <form action="{{ route('organization.projects.index') }}" method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="ابحث عن مشروع بالاسم أو الكود..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('organization.projects.index') }}" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> مسح البحث
                                    </a>
                                @endif
                            </div>
                        </form>

                        {{-- جدول البيانات (Data Table) - تصميم متجاوب (Responsive) --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-right" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الكود</th>
                                        <th>الاسم</th>
                                        <th>الوحدة</th>
                                        <th>القسم</th>
                                        <th>الميزانية</th>
                                        <th>التكلفة الفعلية</th>
                                        <th>التقدم</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- حلقة تكرار لعرض المشاريع --}}
                                    @forelse ($projects as $project)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $project->code }}</td>
                                            <td>{{ $project->name }}</td>
                                            <td>{{ $project->unit->name ?? 'N/A' }}</td> {{-- افتراض وجود علاقة --}}
                                            <td>{{ $project->department->name ?? 'N/A' }}</td> {{-- افتراض وجود علاقة --}}
                                            <td>{{ number_format($project->budget, 2) }}</td>
                                            <td>{{ number_format($project->actual_cost, 2) }}</td>
                                            <td>
                                                {{-- شريط التقدم (Progress Bar) --}}
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ $project->progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- عرض الحالة بلون مناسب --}}
                                                <span class="badge bg-{{ $project->status_color ?? 'secondary' }} text-white">{{ $project->status }}</span>
                                            </td>
                                            <td>{{ $project->priority }}</td>
                                            <td>
                                                {{-- أزرار الإجراءات (Actions Buttons) --}}
                                                <div class="btn-group" role="group" aria-label="إجراءات المشروع">
                                                    {{-- زر العرض (Show) --}}
                                                    <a href="{{ route('organization.projects.show', $project->id) }}" class="btn btn-info btn-sm" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    {{-- زر التعديل (Edit) --}}
                                                    <a href="{{ route('organization.projects.edit', $project->id) }}" class="btn btn-warning btn-sm" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    {{-- زر الحذف (Delete) - يستخدم نموذج لحذف آمن --}}
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $project->id }}" title="حذف">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- نموذج الحذف (Delete Modal) --}}
                                        <div class="modal fade" id="deleteModal{{ $project->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $project->id }}" aria-hidden="true" dir="rtl">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-right">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $project->id }}">تأكيد الحذف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من رغبتك في حذف المشروع: <strong>{{ $project->name }}</strong>؟
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
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                <i class="fas fa-info-circle"></i> لا توجد مشاريع متاحة حالياً.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- قسم Pagination --}}
                        <div class="d-flex justify-content-center">
                            {{ $projects->links('pagination::bootstrap-5') }} {{-- افتراض استخدام Bootstrap 5 Pagination --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- قسم السكربتات الإضافية (Optional Scripts) --}}
@push('scripts')
    <script>
        // يمكن إضافة سكربتات خاصة بهذه الصفحة هنا، مثل تهيئة مكتبة DataTable أو معالجة AJAX
        console.log('صفحة فهرس المشاريع جاهزة.');
    </script>
@endpush
