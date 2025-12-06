@extends('layouts.app')

@section('title', 'قوالب القيود اليومية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group"></i> قوالب القيود اليومية الذكية</h3>
                    <div class="card-tools">
                        <a href="{{ route('journal-templates.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> إضافة قالب جديد
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 20%;">اسم القالب</th>
                                    <th style="width: 15%;">الفئة</th>
                                    <th style="width: 30%;">الوصف</th>
                                    <th style="width: 10%;">الحالة</th>
                                    <th style="width: 10%;">تاريخ الإنشاء</th>
                                    <th style="width: 10%;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><strong class="text-primary">{{ $template->name }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $template->category ?? 'عام' }}</span>
                                    </td>
                                    <td>{{ Str::limit($template->description, 50) ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($template->is_active)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> نشط</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i> غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $template->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('journal-templates.use', $template) }}" 
                                               class="btn btn-sm btn-primary" title="استخدام القالب">
                                                <i class="fas fa-magic"></i>
                                            </a>
                                            <a href="{{ route('journal-templates.edit', $template) }}" 
                                               class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('journal-templates.destroy', $template) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        لا توجد قوالب حالياً. <a href="{{ route('journal-templates.create') }}">أنشئ قالباً جديداً</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
