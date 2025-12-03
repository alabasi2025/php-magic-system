@extends('layouts.app')

@section('title', 'تاريخ الـ Commits - Git Helper')

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
                                <i class="bi bi-clock-history text-primary"></i>
                                تاريخ الـ Commits
                            </h2>
                            <p class="text-muted mb-0">عرض سجل الـ Commits للمستودع</p>
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
                    <form action="{{ route('git-helper.history') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="branch" class="form-label">الفرع</label>
                            <select class="form-select" id="branch" name="branch">
                                <option value="">جميع الفروع</option>
                                @if(isset($branches['local']))
                                    @foreach($branches['local'] as $branch)
                                    <option value="{{ $branch['name'] }}">{{ $branch['name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="limit" class="form-label">عدد الـ Commits</label>
                            <select class="form-select" id="limit" name="limit">
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
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

    <!-- Commit History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul text-primary"></i>
                        سجل الـ Commits
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($history) && count($history) > 0)
                    <div class="timeline">
                        @foreach($history as $commit)
                        <div class="timeline-item mb-4">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-2">{{ $commit['message'] }}</h6>
                                            <div class="d-flex gap-3 text-muted small">
                                                <span>
                                                    <i class="bi bi-person"></i>
                                                    {{ $commit['author'] }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-envelope"></i>
                                                    {{ $commit['email'] }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-calendar"></i>
                                                    {{ \Carbon\Carbon::parse($commit['date'])->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <code class="fs-6">{{ substr($commit['hash'], 0, 7) }}</code>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-primary" onclick="copyHash('{{ $commit['hash'] }}')">
                                                    <i class="bi bi-clipboard"></i> نسخ Hash
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">لا توجد Commits</h5>
                        <p class="text-muted">لم يتم العثور على أي Commits في التاريخ</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyHash(hash) {
    navigator.clipboard.writeText(hash).then(() => {
        alert('تم نسخ الـ Hash: ' + hash.substring(0, 7));
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}
</script>

<style>
.timeline-item {
    position: relative;
    padding-right: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    width: 12px;
    height: 12px;
    background-color: #0d6efd;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 5px;
    top: 12px;
    width: 2px;
    height: calc(100% + 16px);
    background-color: #dee2e6;
}
</style>
@endsection
