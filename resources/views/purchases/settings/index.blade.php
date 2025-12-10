@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="luxury-card">
                <div class="luxury-card-header">
                    <h2 class="luxury-title">
                        <i class="fas fa-cog"></i>
                        إعدادات المشتريات
                    </h2>
                    <p class="luxury-subtitle">
                        إدارة إعدادات نظام المشتريات وأنواع الفواتير
                    </p>
                </div>

                <div class="luxury-card-body">
                    <!-- أنواع فواتير المشتريات -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="luxury-section-title">
                                <i class="fas fa-file-invoice"></i>
                                أنواع فواتير المشتريات
                            </h4>
                            <button type="button" class="luxury-btn luxury-btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceTypeModal">
                                <i class="fas fa-plus"></i>
                                إضافة نوع جديد
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="luxury-table">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>الرمز</th>
                                        <th>البادئة</th>
                                        <th>الوصف</th>
                                        <th>آخر رقم</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoiceTypes as $type)
                                    <tr>
                                        <td>{{ $type->name }}</td>
                                        <td><span class="badge bg-primary">{{ $type->code }}</span></td>
                                        <td><span class="badge bg-info">{{ $type->prefix }}</span></td>
                                        <td>{{ $type->description ?? '-' }}</td>
                                        <td>{{ $type->last_number }}</td>
                                        <td>
                                            @if($type->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editInvoiceType({{ $type->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteInvoiceType({{ $type->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد أنواع فواتير</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Invoice Type Modal -->
<div class="modal fade" id="addInvoiceTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #1e293b; border: 2px solid #3b82f6;">
            <div class="modal-header" style="border-bottom: 1px solid #374151;">
                <h5 class="modal-title" style="color: #3b82f6;">
                    <i class="fas fa-plus-circle"></i>
                    إضافة نوع فاتورة جديد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addInvoiceTypeForm">
                @csrf
                <div class="modal-body" style="color: #e5e7eb;">
                    <div class="mb-3">
                        <label class="form-label">الاسم *</label>
                        <input type="text" class="form-control luxury-input" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الرمز *</label>
                        <input type="text" class="form-control luxury-input" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البادئة *</label>
                        <input type="text" class="form-control luxury-input" name="prefix" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control luxury-input" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #374151;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add Invoice Type
document.getElementById('addInvoiceTypeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("purchases.settings.invoice-types.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء الحفظ');
    });
});

// Delete Invoice Type
function deleteInvoiceType(id) {
    if (!confirm('هل أنت متأكد من حذف هذا النوع؟')) {
        return;
    }
    
    fetch(`/purchases/settings/invoice-types/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء الحذف');
    });
}
</script>
@endpush
