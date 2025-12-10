@extends('layouts.app')

@section('content')
<style>
    /* Luxury Design Styles */
    .luxury-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #f59e0b 100%);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .luxury-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .luxury-header h1 {
        color: white;
        font-weight: 700;
        font-size: 2.5rem;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }
    
    .luxury-header p {
        color: rgba(255,255,255,0.9);
        margin: 10px 0 0 0;
        font-size: 1.1rem;
        position: relative;
        z-index: 1;
    }
    
    .luxury-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        border: none;
    }
    
    .luxury-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .invoice-card {
        padding: 25px;
        border-left: 5px solid #3b82f6;
    }
    
    .invoice-card.draft {
        border-left-color: #6b7280;
    }
    
    .invoice-card.pending {
        border-left-color: #f59e0b;
    }
    
    .invoice-card.approved {
        border-left-color: #10b981;
    }
    
    .invoice-card.cancelled {
        border-left-color: #ef4444;
    }
    
    .invoice-number {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 10px;
    }
    
    .invoice-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-item i {
        color: #3b82f6;
        font-size: 1.1rem;
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 2px;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .amount-display {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f59e0b;
    }
    
    .status-badge {
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }
    
    .status-draft {
        background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        color: white;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .btn-luxury {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-luxury-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .btn-luxury-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
    }
    
    .btn-luxury-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-luxury-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    }
    
    .btn-luxury-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .btn-luxury-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
    }
    
    .btn-luxury-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-luxury-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
    }
    
    .add-invoice-btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 15px 30px;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 20px rgba(245, 158, 11, 0.3);
        position: relative;
        z-index: 10;
    }
    
    .add-invoice-btn:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 20px;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: #9ca3af;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #4b5563;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6b7280;
        font-size: 1.1rem;
    }
</style>

<div class="container-fluid">
    <!-- Luxury Header -->
    <div class="luxury-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-file-invoice-dollar"></i> فواتير المشتريات</h1>
                <p>إدارة ومتابعة فواتير الموردين</p>
            </div>
            <a href="/purchases/invoices/create" class="add-invoice-btn">
                <i class="fas fa-plus-circle"></i>
                إضافة فاتورة جديدة
            </a>
        </div>
    </div>

    <!-- Invoices List -->
    <div class="row">
        <div class="col-12">
            @forelse($invoices as $invoice)
            <div class="luxury-card invoice-card {{ $invoice->status }}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="invoice-number">
                            <i class="fas fa-hashtag"></i> {{ $invoice->invoice_number }}
                        </div>
                        
                        <div class="invoice-info">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <div class="info-label">التاريخ</div>
                                    <div class="info-value">{{ $invoice->invoice_date }}</div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-user-tie"></i>
                                <div>
                                    <div class="info-label">المورد</div>
                                    <div class="info-value">{{ $invoice->supplier->name }}</div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <span class="status-badge status-{{ $invoice->status }}">
                                    @if($invoice->status == 'draft')
                                        <i class="fas fa-file-alt"></i> مسودة
                                    @elseif($invoice->status == 'pending')
                                        <i class="fas fa-clock"></i> معلقة
                                    @elseif($invoice->status == 'approved')
                                        <i class="fas fa-check-circle"></i> معتمدة
                                    @else
                                        <i class="fas fa-times-circle"></i> ملغاة
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-end">
                        <div class="info-label">الإجمالي</div>
                        <div class="amount-display">
                            {{ number_format($invoice->total, 2) }} <small>ريال</small>
                        </div>
                        
                        <div class="action-buttons justify-content-end">
                            <a href="{{ route('purchases.invoices.show', $invoice->id) }}" 
                               class="btn btn-luxury btn-luxury-primary" 
                               title="عرض">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            
                            @if(!$invoice->isApproved())
                            <a href="{{ route('purchases.invoices.edit', $invoice->id) }}" 
                               class="btn btn-luxury btn-luxury-warning" 
                               title="تعديل">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            @endif
                            
                            <form action="{{ route('purchases.invoices.destroy', $invoice->id) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-luxury btn-luxury-danger" 
                                        title="حذف">
                                    <i class="fas fa-trash-alt"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>لا توجد فواتير بعد</h3>
                <p>ابدأ بإضافة فاتورة مشتريات جديدة</p>
                <a href="/purchases/invoices/create" class="add-invoice-btn mt-3">
                    <i class="fas fa-plus-circle"></i>
                    إضافة أول فاتورة
                </a>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Pagination -->
    @if($invoices->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $invoices->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
