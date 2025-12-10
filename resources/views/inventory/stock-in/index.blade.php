@extends('layouts.app')

@section('title', 'أوامر التوريد المخزني')

@section('content')
<style>
    /* Modern Animated Background */
    .inventory-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .inventory-page::before {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        top: -100px;
        right: -100px;
        animation: float 20s ease-in-out infinite;
    }
    
    .inventory-page::after {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(118, 75, 162, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        bottom: -100px;
        left: -100px;
        animation: float 15s ease-in-out infinite reverse;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }
    
    /* Glass Header */
    .glass-header {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        padding: 3rem;
        margin-bottom: 2.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 1;
    }
    
    .glass-header h1 {
        color: white;
        font-weight: 800;
        font-size: 3rem;
        margin: 0;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .glass-header .subtitle {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.2rem;
        margin-top: 0.75rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    /* Stats Cards with 3D Effect */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 2.5rem;
        position: relative;
        z-index: 1;
    }
    
    .stat-card-3d {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 2.5rem;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-3d::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s ease;
    }
    
    .stat-card-3d:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 25px 70px rgba(102, 126, 234, 0.4);
    }
    
    .stat-card-3d:hover::before {
        transform: scaleX(1);
    }
    
    .stat-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        transition: all 0.4s ease;
    }
    
    .stat-card-3d:hover .stat-icon-wrapper {
        transform: rotate(360deg) scale(1.1);
    }
    
    .stat-icon-wrapper i {
        font-size: 2.5rem;
        color: white;
    }
    
    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #64748b;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    /* Glass Table Container */
    .glass-table-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        padding: 2.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: relative;
        z-index: 1;
    }
    
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .table-title {
        font-size: 1.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Modern Buttons */
    .btn-modern {
        padding: 1rem 2rem;
        border-radius: 15px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }
    
    .btn-success-modern {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .btn-success-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(17, 153, 142, 0.4);
    }
    
    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    
    .modern-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 1rem;
        font-weight: 600;
        text-align: right;
        font-size: 1rem;
        border: none;
    }
    
    .modern-table thead th:first-child {
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }
    
    .modern-table thead th:last-child {
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }
    
    .modern-table tbody tr {
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }
    
    .modern-table tbody td {
        padding: 1.5rem 1rem;
        border: none;
        color: #334155;
    }
    
    .modern-table tbody td:first-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    
    .modern-table tbody td:last-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    /* Action Buttons */
    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 0.25rem;
    }
    
    .action-btn-view {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .action-btn-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .action-btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .action-btn:hover {
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    /* Filters */
    .filters-container {
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-input {
        flex: 1;
        min-width: 200px;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: #cbd5e1;
        margin-bottom: 1.5rem;
    }
    
    .empty-state h3 {
        color: #64748b;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #94a3b8;
        font-size: 1.1rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .glass-header h1 {
            font-size: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .table-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .modern-table {
            font-size: 0.9rem;
        }
    }
</style>

<div class="inventory-page">
    <div class="container py-5">
        <!-- Glass Header -->
        <div class="glass-header" data-aos="fade-down">
            <h1><i class="fas fa-box-open me-3"></i>أوامر التوريد المخزني</h1>
            <p class="subtitle">إدارة وتتبع جميع عمليات التوريد إلى المخازن</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card-3d" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-value">{{ $totalStockIns ?? 0 }}</div>
                <div class="stat-label">إجمالي أوامر التوريد</div>
            </div>
            
            <div class="stat-card-3d" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $pendingStockIns ?? 0 }}</div>
                <div class="stat-label">قيد الانتظار</div>
            </div>
            
            <div class="stat-card-3d" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value">{{ $approvedStockIns ?? 0 }}</div>
                <div class="stat-label">معتمدة</div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="glass-table-container" data-aos="fade-up" data-aos-delay="400">
            <div class="table-header">
                <h2 class="table-title"><i class="fas fa-list me-2"></i>قائمة أوامر التوريد</h2>
                <a href="{{ route('inventory.stock-in.create') }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-plus-circle"></i>
                    إنشاء أمر توريد جديد
                </a>
            </div>

            <!-- Filters -->
            <div class="filters-container">
                <input type="text" class="filter-input" placeholder="🔍 بحث برقم الأمر...">
                <select class="filter-input">
                    <option value="">جميع المخازن</option>
                    @foreach($warehouses ?? [] as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <select class="filter-input">
                    <option value="">جميع الحالات</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="approved">معتمد</option>
                    <option value="rejected">مرفوض</option>
                </select>
                <button class="btn-modern btn-success-modern">
                    <i class="fas fa-filter"></i>
                    تطبيق الفلتر
                </button>
            </div>

            <!-- Table -->
            @if(isset($stockIns) && $stockIns->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>رقم الأمر</th>
                                <th>المخزن</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>المنشئ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockIns as $stockIn)
                                <tr>
                                    <td><strong>{{ $stockIn->movement_number }}</strong></td>
                                    <td>{{ $stockIn->warehouse->name ?? '-' }}</td>
                                    <td>{{ $stockIn->movement_date }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $stockIn->status }}">
                                            @if($stockIn->status == 'pending') قيد الانتظار
                                            @elseif($stockIn->status == 'approved') معتمد
                                            @else مرفوض
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $stockIn->creator->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('inventory.stock-in.show', $stockIn->id) }}" class="action-btn action-btn-view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($stockIn->status == 'pending')
                                            <a href="{{ route('inventory.stock-in.edit', $stockIn->id) }}" class="action-btn action-btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="action-btn action-btn-delete" onclick="confirmDelete({{ $stockIn->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $stockIns->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>لا توجد أوامر توريد</h3>
                    <p>ابدأ بإنشاء أمر توريد جديد</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true
    });
    
    function confirmDelete(id) {
        if(confirm('هل أنت متأكد من حذف هذا الأمر؟')) {
            // Delete logic here
            console.log('Delete stock-in:', id);
        }
    }
</script>
@endsection
