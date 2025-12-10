@extends('layouts.app')

@section('content')
<style>
/* 4K Ultra HD Optimized Styles */
@media (min-width: 2560px) {
    .container-fluid {
        max-width: 2400px;
        margin: 0 auto;
    }
    
    h2 { font-size: 3.5rem !important; }
    h5 { font-size: 1.8rem !important; }
    .form-label { font-size: 1.3rem !important; }
    .form-control, .form-select { font-size: 1.2rem !important; padding: 1rem 1.5rem !important; }
    .btn-lg { font-size: 1.4rem !important; padding: 1.2rem 2.5rem !important; }
    .card { margin-bottom: 2.5rem !important; }
    .card-body { padding: 2.5rem !important; }
}

/* Premium Gradient Backgrounds */
.gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
}

.gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 10px 40px rgba(240, 147, 251, 0.3);
}

.gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 10px 40px rgba(79, 172, 254, 0.3);
}

/* Glass Morphism Effect */
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border-radius: 20px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
}

/* Premium Input Styles */
.premium-input {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #ffffff;
}

.premium-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

/* Animated Icons */
.icon-animated {
    transition: all 0.3s ease;
}

.icon-animated:hover {
    transform: scale(1.2) rotate(10deg);
}

/* Premium Buttons */
.btn-premium {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 15px 40px;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.btn-premium:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

/* Floating Labels Effect */
.floating-label-group {
    position: relative;
    margin-bottom: 2rem;
}

.floating-label {
    position: absolute;
    top: -10px;
    left: 15px;
    background: white;
    padding: 0 10px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #667eea;
    z-index: 1;
}

/* Stats Card Animation */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
    }
}

.stats-card {
    animation: pulse-glow 3s infinite;
}

/* Smooth Page Load Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: fadeInUp 0.6s ease-out;
}

.animate-in-delay-1 { animation-delay: 0.1s; }
.animate-in-delay-2 { animation-delay: 0.2s; }
.animate-in-delay-3 { animation-delay: 0.3s; }
.animate-in-delay-4 { animation-delay: 0.4s; }

/* Premium Badge */
.premium-badge {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 5px 15px rgba(255, 216, 155, 0.4);
}

/* Ultra HD Typography */
.ultra-hd-title {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -1px;
}

/* Responsive Grid for 4K */
@media (min-width: 3840px) {
    .container-fluid {
        max-width: 3600px;
    }
    
    .ultra-hd-title {
        font-size: 5rem;
    }
}
</style>

<div class="container-fluid py-5">
    <!-- Ultra HD Header Section -->
    <div class="row mb-5 animate-in">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="ultra-hd-title mb-2">
                        <i class="fas fa-plus-circle icon-animated me-3"></i>
                        إضافة صنف جديد
                    </h2>
                    <p class="text-muted fs-5 mb-0">
                        <span class="premium-badge">نظام إدارة متقدم</span>
                    </p>
                </div>
                <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('inventory.items.store') }}" method="POST" enctype="multipart/form-data" id="itemForm">
        @csrf
        
        <div class="row g-4">
            <!-- Main Form Section -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="glass-card animate-in animate-in-delay-1">
                    <div class="card-header gradient-primary text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-3"></i>
                            المعلومات الأساسية
                        </h5>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <!-- SKU -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-barcode me-1"></i>
                                        رمز الصنف (SKU) *
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control premium-input form-control-lg @error('sku') is-invalid @enderror" 
                                        id="sku" 
                                        name="sku" 
                                        value="{{ old('sku') }}" 
                                        placeholder="DIESEL-001"
                                        required
                                        autofocus>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Barcode -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-qrcode me-1"></i>
                                        الباركود
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control premium-input form-control-lg @error('barcode') is-invalid @enderror" 
                                        id="barcode" 
                                        name="barcode" 
                                        value="{{ old('barcode') }}"
                                        placeholder="اختياري">
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Item Name -->
                            <div class="col-12">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-tag me-1"></i>
                                        اسم الصنف *
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control premium-input form-control-lg @error('name') is-invalid @enderror" 
                                        id="name" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        placeholder="ديزل"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-align-left me-1"></i>
                                        الوصف
                                    </span>
                                    <textarea 
                                        class="form-control premium-input @error('description') is-invalid @enderror" 
                                        id="description" 
                                        name="description" 
                                        rows="4"
                                        placeholder="وصف تفصيلي للصنف...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Unit Card -->
                <div class="glass-card animate-in animate-in-delay-2 mt-4">
                    <div class="card-header gradient-success text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-dollar-sign me-3"></i>
                            السعر والوحدة
                        </h5>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <!-- Unit (Fixed to Liter) -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-balance-scale me-1"></i>
                                        الوحدة *
                                    </span>
                                    <select class="form-select premium-input form-select-lg @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                        <option value="">اختر الوحدة</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ ($unit->name === 'لتر' || $unit->name === 'قطعة') ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Unit Price -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        سعر اللتر *
                                    </span>
                                    <div class="input-group input-group-lg">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            class="form-control premium-input @error('unit_price') is-invalid @enderror" 
                                            id="unit_price" 
                                            name="unit_price" 
                                            value="{{ old('unit_price', 0) }}" 
                                            placeholder="0.00"
                                            required>
                                        <span class="input-group-text bg-success text-white fw-bold">ريال</span>
                                        @error('unit_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Management Card -->
                <div class="glass-card animate-in animate-in-delay-3 mt-4">
                    <div class="card-header gradient-warning text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-warehouse me-3"></i>
                            إدارة المخزون
                        </h5>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <!-- Min Stock -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-arrow-down me-1"></i>
                                        الحد الأدنى *
                                    </span>
                                    <div class="input-group input-group-lg">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            class="form-control premium-input @error('min_stock') is-invalid @enderror" 
                                            id="min_stock" 
                                            name="min_stock" 
                                            value="{{ old('min_stock', 0) }}" 
                                            placeholder="0"
                                            required>
                                        <span class="input-group-text bg-warning text-dark fw-bold">لتر</span>
                                        @error('min_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Max Stock -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        الحد الأقصى *
                                    </span>
                                    <div class="input-group input-group-lg">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            class="form-control premium-input @error('max_stock') is-invalid @enderror" 
                                            id="max_stock" 
                                            name="max_stock" 
                                            value="{{ old('max_stock', 0) }}" 
                                            placeholder="0"
                                            required>
                                        <span class="input-group-text bg-warning text-dark fw-bold">لتر</span>
                                        @error('max_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image & Status Card -->
                <div class="glass-card animate-in animate-in-delay-4 mt-4">
                    <div class="card-header gradient-info text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-image me-3"></i>
                            الصورة والحالة
                        </h5>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <!-- Image Upload -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-camera me-1"></i>
                                        صورة الصنف
                                    </span>
                                    <input 
                                        type="file" 
                                        class="form-control premium-input @error('image') is-invalid @enderror" 
                                        id="image" 
                                        name="image" 
                                        accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="floating-label-group">
                                    <span class="floating-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        الحالة *
                                    </span>
                                    <select class="form-select premium-input form-select-lg @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="glass-card mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-premium btn-lg px-5">
                                <i class="fas fa-save me-2"></i>
                                حفظ الصنف
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section -->
            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="glass-card animate-in animate-in-delay-2 stats-card">
                    <div class="card-header gradient-primary text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-lightbulb me-2"></i>
                            نصائح مهمة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-light border-primary mb-4">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <strong>ملاحظة:</strong> جميع الحقول المميزة بـ <span class="text-danger fw-bold">*</span> إلزامية
                        </div>
                        
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                                    <div>
                                        <strong class="d-block mb-1">رمز SKU</strong>
                                        <p class="mb-0 text-muted small">يجب أن يكون فريداً ولا يتكرر</p>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                                    <div>
                                        <strong class="d-block mb-1">الوحدة</strong>
                                        <p class="mb-0 text-muted small">محددة مسبقاً باللتر</p>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success fs-4 me-3 mt-1"></i>
                                    <div>
                                        <strong class="d-block mb-1">حدود المخزون</strong>
                                        <p class="mb-0 text-muted small">الحد الأقصى > الحد الأدنى</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="glass-card mt-4 animate-in animate-in-delay-3">
                    <div class="card-header gradient-success text-white py-4 rounded-top">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-line me-2"></i>
                            إحصائيات سريعة
                        </h5>
                    </div>
                    <div class="card-body p-5 text-center">
                        <div class="display-3 fw-bold text-success mb-2">{{ $totalItems ?? 0 }}</div>
                        <p class="text-muted mb-0 fs-5">إجمالي الأصناف</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation with premium UX
    const form = document.getElementById('itemForm');
    const minStock = document.getElementById('min_stock');
    const maxStock = document.getElementById('max_stock');
    
    form.addEventListener('submit', function(e) {
        const minValue = parseFloat(minStock.value);
        const maxValue = parseFloat(maxStock.value);
        
        if (maxValue <= minValue) {
            e.preventDefault();
            
            // Premium alert
            Swal.fire({
                icon: 'error',
                title: 'خطأ في البيانات',
                text: 'الحد الأقصى للمخزون يجب أن يكون أكبر من الحد الأدنى',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#667eea'
            });
            
            maxStock.focus();
            return false;
        }
        
        // Show loading
        Swal.fire({
            title: 'جاري الحفظ...',
            text: 'يرجى الانتظار',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
    
    // Auto-generate SKU with premium animation
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');
    
    nameInput.addEventListener('blur', function() {
        if (!skuInput.value) {
            const name = this.value.trim();
            if (name) {
                const sku = name.substring(0, 3).toUpperCase() + '-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                skuInput.value = sku;
                
                // Animate the SKU input
                skuInput.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    skuInput.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            }
        }
    });
    
    // Real-time validation feedback
    const inputs = document.querySelectorAll('.premium-input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush
@endsection
