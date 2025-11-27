@extends('layouts.app')

@section('title', 'نظام الصرافين')

@section('content')
    <!--
    Task 2050: [نظام الصرافين (Cashiers)] Frontend - Task 10
    المطلوب: إنشاء الواجهة الرئيسية (Index View) لجين الصرافين.
    المسار: resources/views/genes/cashiers/index.blade.php
    -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <i class="fa fa-money-bill-wave"></i> نظام الصرافين
                    <small>إدارة عمليات الصرف والتحصيل</small>
                </h1>
                <ol class="breadcrumb">
                    <li>
                        <i class="fa fa-dashboard"></i> <a href="{{ url('/') }}">الرئيسية</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-money-bill-wave"></i> نظام الصرافين
                    </li>
                </ol>
            </div>
        </div>
        <!-- لوحة تحكم الصرافين -->
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-cash-register fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">0</div>
                                <div>عمليات الصرف اليومية</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-right">عرض التفاصيل</span>
                            <span class="pull-left"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-receipt fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">0</div>
                                <div>إجمالي التحصيلات</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-right">عرض التفاصيل</span>
                            <span class="pull-left"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-exchange-alt fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">0</div>
                                <div>الرصيد الحالي للصراف</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-right">إدارة الرصيد</span>
                            <span class="pull-left"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- نهاية لوحة تحكم الصرافين -->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-list fa-fw"></i> آخر 10 عمليات صرف/تحصيل</h3>
                    </div>
                    <div class="panel-body">
                        <p class="text-center text-muted">سيتم عرض جدول بآخر العمليات هنا.</p>
                        <!-- هنا سيتم إضافة جدول البيانات لاحقاً -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- يمكن إضافة سكريبتات خاصة بالواجهة هنا -->
    <script>
        console.log('Cashiers Gene Index View Loaded.');
    </script>
@endpush