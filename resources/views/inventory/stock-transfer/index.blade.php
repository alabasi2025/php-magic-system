@extends('layouts.app')

@section('title', 'أوامر التحويل المخزني')

@section('content')
<style>
    /* Professional Stock In Page Styles */
    .stock-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        padding: 2.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .stock-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .stock-header h1 {
        color: white;
        font-weight: 700;
        font-size: 2.5rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    .stock-header .subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    }
    
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card.success {
        --gradient-start: #11998e;
        --gradient-end: #38ef7d;
    }
    
    .stat-card.warning {
        --gradient-start: #f093fb;
        --gradient-end: #f5576c;
    }
    
    .stat-card.info {
        --gradient-start: #4facfe;
        --gradient-end: #00f2fe;
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .stat-value {
        font-size: 2.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0.5rem 0;
    }
    
    .stat-label {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .main-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 2rem;
        border-bottom: none;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box input {
        border-radius: 50px;
        padding: 0.75rem 1.5rem 0.75rem 3rem;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .search-box input:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
        outline: none;
    }
    
    .search-box i {
        position: absolute;
        right: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    
    .filter-select {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .filter-select:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
        outline: none;
    }
    
    .btn-gradient {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5);
        color: white;
    }
    
    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
        margin: 0 3px;
    }
    
    .btn-action.btn-view {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .btn-action.btn-approve {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .btn-action.btn-delete {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    
    .btn-action:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .modern-table {
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    
    .modern-table thead th {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        font-weight: 600;
        padding: 1.2rem 1rem;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modern-table thead th:first-child {
        border-top-right-radius: 12px;
    }
    
    .modern-table thead th:last-child {
        border-top-left-radius: 12px;
    }
    
    .modern-table tbody tr {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .modern-table tbody td {
        padding: 1.2rem 1rem;
        border: none;
        vertical-align: middle;
    }
    
    .modern-table tbody tr td:first-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    
    .modern-table tbody tr td:last-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    
    .badge-modern {
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }
    
    .badge-pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .badge-approved {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .badge-rejected {
        background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        color: white;
    }
    
    .order-number {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 0.4rem 1rem;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #11998e;
    }
    
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 5rem;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid" dir="rtl">
    
    {{-- Header Section --}}
    <div class="stock-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1>
                    <i class="fas fa-arrow-down ml-2"></i>
                    أوامر التحويل المخزني
                </h1>
                <p class="subtitle mb-0">
                    <i class="fas fa-info-circle ml-1"></i>
                    إدارة وتتبع جميع أوامر إدخال البضائع إلى المخازن
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('inventory.stock-transfer.create') }}" class="btn btn-light btn-lg" style="border-radius: 12px; font-weight: 600; padding: 0.75rem 2rem;">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء أمر تحويل جديد
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
            <i class="fas fa-check-circle ml-2" style="font-size: 1.2rem;"></i> 
            <strong>رائع!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
            <i class="fas fa-exclamation-circle ml-2" style="font-size: 1.2rem;"></i> 
            <strong>تنبيه!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-2">إجمالي الأوامر</p>
                        <h2 class="stat-value">{{ $totalOrders }}</h2>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-clipboard-list ml-1"></i>
                            جميع أوامر التحويل
                        </p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-2">قيد الانتظار</p>
                        <h2 class="stat-value">{{ $pendingOrders }}</h2>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-clock ml-1"></i>
                            تحتاج إلى اعتماد
                        </p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label mb-2">المعتمدة</p>
                        <h2 class="stat-value">{{ $approvedOrders }}</h2>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-check-double ml-1"></i>
                            أوامر مكتملة
                        </p>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="main-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0" style="font-weight: 700; color: #1e293b;">
                    <i class="fas fa-list-ul ml-2"></i>
                    قائمة أوامر التحويل
                </h4>
                <div class="mt-3 mt-md-0">
                    <button class="btn btn-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 10px; padding: 0.5rem 1.2rem; font-weight: 600; margin-left: 8px;">
                        <i class="fas fa-print ml-1"></i> طباعة
                    </button>
                    <button class="btn btn-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-radius: 10px; padding: 0.5rem 1.2rem; font-weight: 600;">
                        <i class="fas fa-file-excel ml-1"></i> تصدير Excel
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body" style="padding: 2rem;">
            {{-- Search and Filters --}}
            <form action="{{ route('inventory.stock-transfer.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="ابحث برقم الأمر..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="warehouse_id" class="form-control filter-select">
                            <option value="">🏢 كل المخازن</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <select name="status" class="form-control filter-select">
                            <option value="">⚡ كل الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <input type="date" name="date_from" class="form-control filter-select" value="{{ request('date_from') }}" placeholder="من تاريخ">
                    </div>

                    <div class="col-md-1 mb-3">
                        <button type="submit" class="btn btn-gradient w-100" style="height: 100%;">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Data Table --}}
            <div class="table-responsive">
                <table class="modern-table table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>رقم الأمر</th>
                            <th>المخزن</th>
                            <th>التاريخ</th>
                            <th class="text-center">عدد الأصناف</th>
                            <th class="text-center">الحالة</th>
                            <th>المنشئ</th>
                            <th class="text-center" style="width: 140px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockTransfers as $stockIn)
                            <tr>
                                <td class="text-center">
                                    <span style="font-weight: 700; color: #11998e; font-size: 1.1rem;">
                                        {{ $loop->iteration + ($stockTransfers->currentPage() - 1) * $stockTransfers->perPage() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="order-number">{{ $stockIn->movement_number }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-left: 12px;">
                                            <i class="fas fa-warehouse"></i>
                                        </div>
                                        <div style="font-weight: 600; color: #1e293b;">
                                            {{ $stockIn->warehouse->name ?? 'غير محدد' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $stockIn->movement_date->format('Y-m-d') }}
                                    </div>
                                    <div style="font-size: 0.85rem; color: #94a3b8;">
                                        {{ $stockIn->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        {{ $stockIn->items->count() }} صنف
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($stockIn->status == 'pending')
                                        <span class="badge-modern badge-pending">
                                            <i class="fas fa-clock ml-1"></i> قيد الانتظار
                                        </span>
                                    @elseif ($stockIn->status == 'approved')
                                        <span class="badge-modern badge-approved">
                                            <i class="fas fa-check-circle ml-1"></i> معتمد
                                        </span>
                                    @else
                                        <span class="badge-modern badge-rejected">
                                            <i class="fas fa-times-circle ml-1"></i> مرفوض
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; margin-left: 10px; font-size: 0.85rem;">
                                            {{ substr($stockIn->creator->name ?? 'غ', 0, 1) }}
                                        </div>
                                        <span style="font-weight: 600; color: #1e293b;">
                                            {{ $stockIn->creator->name ?? 'غير محدد' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.stock-transfer.show', $stockIn->id) }}" 
                                       class="btn-action btn-view" 
                                       data-toggle="tooltip" 
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($stockIn->status == 'pending')
                                        <form action="{{ route('inventory.stock-transfer.approve', $stockIn->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn-action btn-approve" 
                                                    data-toggle="tooltip" 
                                                    title="اعتماد"
                                                    onclick="return confirm('هل أنت متأكد من اعتماد هذا الأمر؟')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                data-toggle="tooltip" 
                                                title="حذف"
                                                onclick="confirmDelete({{ $stockIn->id }}, '{{ $stockIn->movement_number }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <form id="delete-form-{{ $stockIn->id }}" 
                                              action="{{ route('inventory.stock-transfer.destroy', $stockIn->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h4>لا توجد أوامر تحويل</h4>
                                        <p>ابدأ بإنشاء أول أمر تحويل للنظام</p>
                                        <a href="{{ route('inventory.stock-transfer.create') }}" class="btn btn-gradient mt-3">
                                            <i class="fas fa-plus ml-2"></i>
                                            إنشاء أول أمر تحويل
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($stockTransfers->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div style="color: #64748b; font-weight: 600;">
                        عرض {{ $stockTransfers->firstItem() ?? 0 }} إلى {{ $stockTransfers->lastItem() ?? 0 }} من أصل {{ $stockTransfers->total() }} أمر
                    </div>
                    <div>
                        {{ $stockTransfers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, number) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                html: `سيتم حذف الأمر: <strong>${number}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ee0979',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        } else {
            if (confirm('هل أنت متأكد من حذف الأمر: ' + number + '؟')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
