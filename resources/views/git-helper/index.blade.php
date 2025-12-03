@extends('layouts.app')

@section('title', 'Git Helper - مساعد Git الذكي')

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
                                <i class="bi bi-git text-primary"></i>
                                Git Helper v3.22.0
                            </h2>
                            <p class="text-muted mb-0">مساعد Git الذكي - إدارة المستودع بسهولة</p>
                        </div>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $status['current_branch'] ?? 'main' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error') || isset($error))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') ?? $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">اليوم</h6>
                            <h3 class="mb-0">{{ $statistics['today']['commits'] ?? 0 }}</h3>
                            <small class="text-muted">Commits</small>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">هذا الأسبوع</h6>
                            <h3 class="mb-0">{{ $statistics['week']['commits'] ?? 0 }}</h3>
                            <small class="text-muted">Commits</small>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-calendar-week fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">هذا الشهر</h6>
                            <h3 class="mb-0">{{ $statistics['month']['commits'] ?? 0 }}</h3>
                            <small class="text-muted">Commits</small>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-calendar-month fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">التغييرات الحالية</h6>
                            <h3 class="mb-0">{{ $status['total_changes'] ?? 0 }}</h3>
                            <small class="text-muted">ملفات</small>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-file-earmark-diff fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repository Status -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-folder2-open text-primary"></i>
                        حالة المستودع
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($status['files']) && count($status['files']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الملف</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($status['files'] as $file)
                                <tr>
                                    <td>
                                        <code>{{ $file['file'] }}</code>
                                    </td>
                                    <td>
                                        @if($file['status'] == 'modified')
                                        <span class="badge bg-warning">معدل</span>
                                        @elseif($file['status'] == 'added')
                                        <span class="badge bg-success">مضاف</span>
                                        @elseif($file['status'] == 'deleted')
                                        <span class="badge bg-danger">محذوف</span>
                                        @elseif($file['status'] == 'untracked')
                                        <span class="badge bg-secondary">غير متتبع</span>
                                        @else
                                        <span class="badge bg-info">{{ $file['status'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDiff('{{ $file['file'] }}')">
                                            <i class="bi bi-eye"></i> عرض التغييرات
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Commit Form -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-3">إنشاء Commit جديد</h6>
                        <form action="{{ route('git-helper.commit') }}" method="POST" id="commitForm">
                            @csrf
                            <div class="mb-3">
                                <label for="commitMessage" class="form-label">رسالة الـ Commit</label>
                                <textarea class="form-control" id="commitMessage" name="message" rows="3" required placeholder="اكتب رسالة الـ Commit هنا..."></textarea>
                                <div class="form-text">
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="generateCommitMessage()">
                                        <i class="bi bi-magic"></i> توليد رسالة ذكية
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="analyzeChanges()">
                                        <i class="bi bi-graph-up"></i> تحليل التغييرات
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> إنشاء Commit
                                </button>
                                <button type="button" class="btn btn-success" onclick="commitAndPush()">
                                    <i class="bi bi-cloud-upload"></i> Commit & Push
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">لا توجد تغييرات</h5>
                        <p class="text-muted">المستودع نظيف - لا توجد ملفات معدلة</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge text-warning"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('git-helper.pull') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-cloud-download"></i> Pull من GitHub
                            </button>
                        </form>
                        
                        <form action="{{ route('git-helper.push') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-cloud-upload"></i> Push إلى GitHub
                            </button>
                        </form>
                        
                        <a href="{{ route('git-helper.branches') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-diagram-3"></i> إدارة الفروع
                        </a>
                        
                        <a href="{{ route('git-helper.history') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-clock-history"></i> عرض التاريخ
                        </a>
                        
                        <a href="{{ route('git-helper.operations') }}" class="btn btn-outline-dark w-100">
                            <i class="bi bi-list-ul"></i> سجل العمليات
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sync Status -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-repeat text-info"></i>
                        حالة المزامنة
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($status['ahead']) && $status['ahead'] > 0)
                    <div class="alert alert-warning mb-2">
                        <i class="bi bi-arrow-up-circle"></i>
                        لديك {{ $status['ahead'] }} commit(s) غير مرفوعة
                    </div>
                    @endif
                    
                    @if(isset($status['behind']) && $status['behind'] > 0)
                    <div class="alert alert-info mb-2">
                        <i class="bi bi-arrow-down-circle"></i>
                        لديك {{ $status['behind'] }} commit(s) جديدة للتحميل
                    </div>
                    @endif
                    
                    @if((!isset($status['ahead']) || $status['ahead'] == 0) && (!isset($status['behind']) || $status['behind'] == 0))
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i>
                        المستودع متزامن مع GitHub
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Operations -->
    @if(isset($recentOperations) && count($recentOperations) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary"></i>
                        آخر العمليات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>الوصف</th>
                                    <th>الفرع</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOperations as $operation)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $operation->getFormattedOperationType() }}</span>
                                    </td>
                                    <td>{{ Str::limit($operation->description ?? $operation->commit_message, 50) }}</td>
                                    <td>
                                        @if($operation->branch_name)
                                        <code>{{ $operation->branch_name }}</code>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $operation->getStatusBadgeColor() }}">
                                            {{ $operation->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $operation->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function viewDiff(file) {
    // TODO: Implement diff viewer modal
    alert('عرض التغييرات لـ: ' + file);
}

function generateCommitMessage() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري التوليد...';
    
    fetch('{{ route("git-helper.generate-commit-message") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commitMessage').value = data.message;
        } else {
            alert('فشل في توليد الرسالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء توليد الرسالة');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function analyzeChanges() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري التحليل...';
    
    fetch('{{ route("git-helper.analyze-changes") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تحليل التغييرات:\n\n' + data.analysis);
        } else {
            alert('فشل في تحليل التغييرات');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء التحليل');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function commitAndPush() {
    if (confirm('هل تريد إنشاء Commit ورفعه إلى GitHub؟')) {
        const form = document.getElementById('commitForm');
        const formData = new FormData(form);
        
        // First commit
        fetch('{{ route("git-helper.commit") }}', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            // Then push
            return fetch('{{ route("git-helper.push") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        })
        .then(() => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء العملية');
        });
    }
}
</script>
@endsection
