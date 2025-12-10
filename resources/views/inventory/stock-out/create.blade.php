@extends('layouts.app')

@section('title', 'إنشاء أمر توريد مخزني')

@section('content')
<style>
    /* Modern Create Page Background */
    .create-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        position: relative;
        overflow: hidden;
        padding: 3rem 0;
    }
    
    .create-page::before {
        content: '';
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(56, 239, 125, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        top: -200px;
        left: -200px;
        animation: pulse 15s ease-in-out infinite;
    }
    
    .create-page::after {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(17, 153, 142, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        bottom: -150px;
        right: -150px;
        animation: pulse 20s ease-in-out infinite reverse;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }
    
    /* Glass Form Container */
    .glass-form {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 35px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.5);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }
    
    /* Form Header */
    .form-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        padding: 3rem;
        text-align: center;
        position: relative;
    }
    
    .form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center no-repeat;
        background-size: cover;
        opacity: 0.3;
    }
    
    .form-header h1 {
        color: white;
        font-weight: 800;
        font-size: 2.5rem;
        margin: 0 0 1rem 0;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .form-header p {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.1rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    /* Form Sections */
    .form-body {
        padding: 3rem;
    }
    
    .form-section {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.5) 0%, rgba(241, 245, 249, 0.5) 100%);
        border-radius: 25px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 2px solid rgba(17, 153, 142, 0.1);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        border-color: rgba(17, 153, 142, 0.3);
        box-shadow: 0 10px 30px rgba(17, 153, 142, 0.1);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .section-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
    }
    
    /* Form Inputs */
    .form-group {
        margin-bottom: 1.75rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.75rem;
        font-size: 1.05rem;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #11998e;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
        transform: translateY(-2px);
    }
    
    /* Items Table */
    .items-table-container {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-top: 1.5rem;
    }
    
    .items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    
    .items-table thead th {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 1.25rem 1rem;
        font-weight: 600;
        text-align: center;
        border: none;
    }
    
    .items-table thead th:first-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    
    .items-table thead th:last-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    
    .items-table tbody tr {
        background: #f8fafc;
        transition: all 0.3s ease;
    }
    
    .items-table tbody tr:hover {
        background: #f1f5f9;
        transform: scale(1.01);
    }
    
    .items-table tbody td {
        padding: 1rem;
        border: none;
        text-align: center;
    }
    
    .items-table tbody td:first-child {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    
    .items-table tbody td:last-child {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    
    .items-table input,
    .items-table select {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .items-table input:focus,
    .items-table select:focus {
        outline: none;
        border-color: #11998e;
        box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
    }
    
    /* Action Buttons */
    .btn-add-item {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 15px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
    }
    
    .btn-add-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(17, 153, 142, 0.4);
    }
    
    .btn-remove-item {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-remove-item:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
    }
    
    /* Total Summary */
    .total-summary {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-top: 2rem;
        color: white;
        text-align: center;
    }
    
    .total-label {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.95;
    }
    
    .total-value {
        font-size: 3rem;
        font-weight: 800;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        padding: 2rem 3rem 3rem;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 1.25rem 3rem;
        border-radius: 15px;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(17, 153, 142, 0.4);
    }
    
    .btn-cancel {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        color: white;
        padding: 1.25rem 3rem;
        border-radius: 15px;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(100, 116, 139, 0.3);
    }
    
    .btn-cancel:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(100, 116, 139, 0.4);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .form-header h1 {
            font-size: 1.8rem;
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        .form-section {
            padding: 1.5rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .items-table {
            font-size: 0.85rem;
        }
    }
</style>

<div class="create-page">
    <div class="container">
        <form action="{{ route('inventory.stock-out.store') }}" method="POST" class="glass-form" data-aos="fade-up">
            @csrf
            
            <!-- Form Header -->
            <div class="form-header">
                <h1><i class="fas fa-box-open"></i> إنشاء أمر توريد مخزني</h1>
                <p>قم بإدخال بيانات الصرف والأصناف المراد إضافتها للمخزن</p>
            </div>
            
            <!-- Form Body -->
            <div class="form-body">
                <!-- Basic Info Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="100">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <span>المعلومات الأساسية</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">المخزن <span class="text-danger">*</span></label>
                                <select name="warehouse_id" class="form-control-modern" required>
                                    <option value="">اختر المخزن</option>
                                    @foreach($warehouses ?? [] as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">تاريخ الصرف <span class="text-danger">*</span></label>
                                <input type="date" name="movement_date" class="form-control-modern" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">رقم الأمر</label>
                                <input type="text" class="form-control-modern" value="IN{{ date('Ymd') }}XXXX" readonly>
                                <small class="text-muted">سيتم توليده تلقائياً</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">ملاحظات</label>
                                <input type="text" name="notes" class="form-control-modern" placeholder="ملاحظات إضافية...">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Items Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span>الأصناف</span>
                    </div>
                    
                    <div class="items-table-container">
                        <table class="items-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th>الكمية</th>
                                    <th>تكلفة الوحدة</th>
                                    <th>الإجمالي</th>
                                    <th>رقم الدفعة</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr>
                                    <td>
                                        <select name="items[0][item_id]" class="item-select" required>
                                            <option value="">اختر الصنف</option>
                                            @foreach($items ?? [] as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="item-quantity" step="0.001" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_cost]" class="item-cost" step="0.01" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="item-total" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][batch_number]">
                                    </td>
                                    <td>
                                        <input type="date" name="items[0][expiry_date]">
                                    </td>
                                    <td>
                                        <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <button type="button" class="btn-add-item" onclick="addItem()">
                                <i class="fas fa-plus-circle"></i>
                                إضافة صنف جديد
                            </button>
                        </div>
                    </div>
                    
                    <!-- Total Summary -->
                    <div class="total-summary" data-aos="zoom-in" data-aos-delay="300">
                        <div class="total-label">الإجمالي الكلي</div>
                        <div class="total-value" id="grandTotal">0.00 ريال</div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>
                    حفظ أمر الصرف
                </button>
                <a href="{{ route('inventory.stock-out.index') }}" class="btn-cancel">
                    <i class="fas fa-times me-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<!-- AOS Animation -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true
    });
    
    let itemIndex = 1;
    
    function addItem() {
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${itemIndex}][item_id]" class="item-select" required>
                    <option value="">اختر الصنف</option>
                    @foreach($items ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="item-quantity" step="0.001" min="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_cost]" class="item-cost" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" class="item-total" readonly>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][batch_number]">
            </td>
            <td>
                <input type="date" name="items[${itemIndex}][expiry_date]">
            </td>
            <td>
                <button type="button" class="btn-remove-item" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        itemIndex++;
        attachCalculationListeners();
    }
    
    function removeItem(button) {
        const row = button.closest('tr');
        row.remove();
        calculateGrandTotal();
    }
    
    function attachCalculationListeners() {
        document.querySelectorAll('.item-quantity, .item-cost').forEach(input => {
            input.addEventListener('input', function() {
                const row = this.closest('tr');
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
                const total = quantity * cost;
                row.querySelector('.item-total').value = total.toFixed(2);
                calculateGrandTotal();
            });
        });
    }
    
    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-total').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' ريال';
    }
    
    // Initialize
    attachCalculationListeners();
</script>
@endsection
