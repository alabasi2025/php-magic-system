@extends('layouts.app')

@section('title', 'إدارة الفروع - Git Helper')

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
                                <i class="bi bi-diagram-3 text-primary"></i>
                                إدارة الفروع
                            </h2>
                            <p class="text-muted mb-0">إدارة فروع Git للمستودع</p>
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

    <div class="row">
        <!-- Local Branches -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-laptop text-primary"></i>
                        الفروع المحلية
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($branches['local']) && count($branches['local']) > 0)
                    <div class="list-group">
                        @foreach($branches['local'] as $branch)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <code class="fs-6">{{ $branch['name'] }}</code>
                                @if($branch['is_current'])
                                <span class="badge bg-success ms-2">الحالي</span>
                                @endif
                            </div>
                            <div>
                                @if(!$branch['is_current'])
                                <form action="{{ route('git-helper.branches.switch') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $branch['name'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-left-right"></i> التبديل
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-3">لا توجد فروع محلية</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Remote Branches -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-cloud text-info"></i>
                        الفروع البعيدة
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($branches['remote']) && count($branches['remote']) > 0)
                    <div class="list-group">
                        @foreach($branches['remote'] as $branch)
                        <div class="list-group-item">
                            <code class="fs-6">{{ $branch['name'] }}</code>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-3">لا توجد فروع بعيدة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create New Branch -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle text-success"></i>
                        إنشاء فرع جديد
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('git-helper.branches.create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branchName" class="form-label">اسم الفرع</label>
                                    <input type="text" class="form-control" id="branchName" name="name" required 
                                           pattern="[a-zA-Z0-9_\-\/]+" 
                                           placeholder="feature/new-feature">
                                    <div class="form-text">استخدم حروف، أرقام، شرطات، وشرطات سفلية فقط</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الخيارات</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkoutBranch" name="checkout" value="1" checked>
                                        <label class="form-check-label" for="checkoutBranch">
                                            التبديل إلى الفرع الجديد تلقائياً
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> إنشاء الفرع
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Branch Info -->
    @if(isset($branches['current']))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="mb-1">الفرع الحالي</h6>
                            <code class="fs-5">{{ $branches['current'] }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
