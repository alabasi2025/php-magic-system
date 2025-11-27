@extends('layouts.app')

{{--
    Gene: Cashiers
    Task: 2046 - Frontend - Task 6 (عرض قائمة العمليات/المعاملات)
    Description: هذا الملف يمثل واجهة عرض قائمة العمليات (Transactions) لنظام الصرافين.
    يتبع معمارية الجينات (Gene Architecture) ويستخدم Blade كواجهة أمامية.
--}}

@section('title', 'قائمة العمليات - نظام الصرافين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <i class="fa fa-exchange"></i> قائمة العمليات
                <small>إدارة وعرض جميع عمليات الصرافين</small>
            </h1>
            <ol class="breadcrumb">
                <li>
                    <i class="fa fa-dashboard"></i> <a href="{{ url('/dashboard') }}">الرئيسية</a>
                </li>
                <li class="active">
                    <i class="fa fa-exchange"></i> العمليات
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list fa-fw"></i> سجل العمليات</h3>
                </div>
                <div class="panel-body">
                    {{-- جدول عرض العمليات --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نوع العملية</th>
                                    <th>المبلغ</th>
                                    <th>العملة</th>
                                    <th>الصراف</th>
                                    <th>التاريخ والوقت</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- مثال على صف بيانات (يجب استبداله بحلقة @foreach في التطبيق الفعلي) --}}
                                <tr>
                                    <td>1</td>
                                    <td>إيداع</td>
                                    <td>1,500.00</td>
                                    <td>USD</td>
                                    <td>أحمد محمد</td>
                                    <td>2025-11-27 10:30:00</td>
                                    <td><span class="label label-success">مكتملة</span></td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-xs" title="عرض التفاصيل"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>سحب</td>
                                    <td>500.00</td>
                                    <td>SAR</td>
                                    <td>فاطمة علي</td>
                                    <td>2025-11-27 11:00:00</td>
                                    <td><span class="label label-warning">قيد المراجعة</span></td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-xs" title="عرض التفاصيل"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                {{-- نهاية مثال البيانات --}}
                            </tbody>
                        </table>
                    </div>
                    {{-- نهاية جدول عرض العمليات --}}

                    {{-- هنا يمكن إضافة روابط الترقيم (Pagination) --}}
                    <div class="text-center">
                        {{-- {{ $transactions->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- يمكن إضافة أكواد JavaScript خاصة بهذه الصفحة هنا، مثل تهيئة مكتبة Datatables --}}
<script>
    $(document).ready(function() {
        // تهيئة Datatables أو أي مكتبة أخرى لعرض البيانات
        // $('#transactions-table').DataTable();
    });
</script>
@endpush