@extends('layouts.app')

@section('title', 'قائمة الصرافين')

@section('content')
    <!-- Cashiers Gene: Frontend - Task 19 (Task 2059) -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users-cog mr-1"></i>
                            قائمة الصرافين
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('cashiers.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle"></i> إضافة صراف جديد
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cashiers-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>اسم المستخدم</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- يتم ملء هذا الجزء ببيانات الصرافين عبر JavaScript أو حلقة Blade --}}
                                    @if(isset($cashiers) && $cashiers->count() > 0)
                                        @foreach($cashiers as $cashier)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $cashier->name }}</td>
                                                <td>{{ $cashier->username }}</td>
                                                <td>
                                                    @if($cashier->is_active)
                                                        <span class="badge badge-success">نشط</span>
                                                    @else
                                                        <span class="badge badge-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>{{ $cashier->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('cashiers.edit', $cashier->id) }}" class="btn btn-info btn-sm" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-cashier" data-id="{{ $cashier->id }}" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">لا يوجد صرافون مسجلون حالياً.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- تضمين مكتبات JavaScript الخاصة بالجدول (مثل DataTables) هنا -->
    <script>
        $(document).ready(function() {
            // تهيئة DataTables
            $('#cashiers-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Arabic.json" // افتراضياً
                }
            });

            // معالج حدث زر الحذف (يتطلب إضافة نموذج حذف لاحقاً)
            $('.delete-cashier').on('click', function() {
                const cashierId = $(this).data('id');
                if (confirm('هل أنت متأكد من رغبتك في حذف هذا الصراف؟')) {
                    // هنا يتم تنفيذ طلب الحذف (قد يكون عبر AJAX أو نموذج)
                    console.log('Attempting to delete cashier with ID:', cashierId);
                    // مثال: إرسال نموذج حذف
                    // $('#delete-form-' + cashierId).submit();
                }
            });
        });
    </script>
@endpush