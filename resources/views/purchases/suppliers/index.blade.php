@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>إدارة الموردين</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item active">الموردين</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('purchases.suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة مورد جديد
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">قائمة الموردين</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المورد</th>
                                    <th>رقم الهاتف</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>العنوان</th>
                                    <th>الرصيد</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $index => $supplier)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $supplier->name }}</strong>
                                        @if($supplier->code)
                                            <br><small class="text-muted">{{ $supplier->code }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->email ?? '-' }}</td>
                                    <td>{{ $supplier->address ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $supplier->balance >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($supplier->balance, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($supplier->status === 'active' || $supplier->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('purchases.suppliers.show', $supplier->id) }}" class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('purchases.suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('purchases.suppliers.transactions', $supplier->id) }}" class="btn btn-sm btn-primary" title="المعاملات">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" title="حذف" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $supplier->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Delete Modal for {{ $supplier->name }} -->
                                <div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $supplier->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $supplier->id }}">تأكيد الحذف</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>هل أنت متأكد من حذف المورد: <strong>{{ $supplier->name }}</strong>؟</p>
                                                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> هذا الإجراء لا يمكن التراجع عنه!</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times"></i> إلغاء
                                                </button>
                                                <form action="{{ route('purchases.suppliers.destroy', $supplier->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد بيانات موردين حالياً</p>
                                        <a href="{{ route('purchases.suppliers.create') }}" class="btn btn-sm btn-primary">
                                            إضافة مورد جديد
                                        </a>
                                    </td>
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
@endsection
