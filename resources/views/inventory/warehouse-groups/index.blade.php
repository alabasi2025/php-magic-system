@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i>
                مجموعات المخازن
            </h2>
            <p class="text-muted mb-0">إدارة مجموعات المخازن وربطها بالحسابات المحاسبية</p>
        </div>
        <a href="{{ route('inventory.warehouse-groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            إضافة مجموعة جديدة
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Warehouse Groups Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">الكود</th>
                            <th class="py-3">اسم المجموعة</th>
                            <th class="py-3">الحساب المحاسبي</th>
                            <th class="py-3 text-center">عدد المخازن</th>
                            <th class="py-3 text-center">الحالة</th>
                            <th class="py-3 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary">{{ $group->code }}</span>
                                </td>
                                <td class="py-3">
                                    <strong>{{ $group->name }}</strong>
                                    @if($group->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($group->account)
                                        <span class="badge bg-info">{{ $group->account->code }}</span>
                                        <span class="text-muted">{{ $group->account->name }}</span>
                                    @else
                                        <span class="text-muted">غير مرتبط</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $group->warehouses->count() }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    @if($group->status === 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('inventory.warehouse-groups.show', $group) }}" 
                                           class="btn btn-outline-info" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.warehouse-groups.edit', $group) }}" 
                                           class="btn btn-outline-primary" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $group->id }})"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $group->id }}" 
                                          action="{{ route('inventory.warehouse-groups.destroy', $group) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">لا توجد مجموعات مخازن</p>
                                    <a href="{{ route('inventory.warehouse-groups.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>
                                        إضافة مجموعة جديدة
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groups->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function confirmDelete(groupId) {
    if (confirm('هل أنت متأكد من حذف هذه المجموعة؟')) {
        document.getElementById('delete-form-' + groupId).submit();
    }
}
</script>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.badge {
    font-weight: 500;
}
</style>
@endsection
