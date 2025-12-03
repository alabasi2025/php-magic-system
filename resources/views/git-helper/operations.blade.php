@extends('layouts.app')

@section('title', 'سجل العمليات - Git Helper')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="bi bi-list-ul text-primary"></i>
                                سجل العمليات
                            </h2>
                            <p class="text-muted mb-0">عرض جميع عمليات Git المنفذة</p>
                        </div>
                        <div>
                            <a href="{{ route('git-helper.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-right"></i> العودة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') ?? $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('git-helper.operations') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label">نوع العملية</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">جميع الأنواع</option>
                                <option value="commit">Commit</option>
                                <option value="push">Push</option>
                                <option value="pull">Pull</option>
                                <option value="branch">Branch</option>
                                <option value="merge">Merge</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="success">نجح</option>
                                <option value="failed">فشل</option>
                                <option value="pending">قيد الانتظار</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> تطبيق الفلتر
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-table text-primary"></i>
                        قائمة العمليات
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($operations) && count($operations) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>الفرع</th>
                                    <th>المؤلف</th>
                                    <th>الملفات</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operations as $operation)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $operation->getFormattedOperationType() }}</span>
                                    </td>
                                    <td>
                                        {{ Str::limit($operation->description ?? $operation->commit_message, 60) }}
                                        @if($operation->commit_hash)
                                        <br>
                                        <small><code>{{ substr($operation->commit_hash, 0, 7) }}</code></small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operation->branch_name)
                                        <code>{{ $operation->branch_name }}</code>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $operation->author ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($operation->lines_added > 0 || $operation->lines_deleted > 0)
                                        <small class="text-success">+{{ $operation->lines_added }}</small>
                                        <small class="text-danger">-{{ $operation->lines_deleted }}</small>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $operation->getStatusBadgeColor() }}">
                                            {{ $operation->status }}
                                        </span>
                                        @if($operation->error_message)
                                        <i class="bi bi-exclamation-circle text-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="{{ $operation->error_message }}"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $operation->created_at->format('Y-m-d H:i') }}
                                            <br>
                                            {{ $operation->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $operations->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">لا توجد عمليات</h5>
                        <p class="text-muted">لم يتم تنفيذ أي عمليات بعد</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
@endsection
