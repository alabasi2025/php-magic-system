@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">نظام المشتريات</h1>
        </div>
    </div>

    <div class="row">
        <!-- الموردين -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">الموردين</h5>
                    <p class="card-text">إدارة بيانات الموردين</p>
                    <a href="{{ route('purchases.suppliers.index') }}" class="btn btn-primary">عرض</a>
                </div>
            </div>
        </div>

        <!-- أوامر الشراء -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">أوامر الشراء</h5>
                    <p class="card-text">إنشاء وإدارة أوامر الشراء</p>
                    <a href="{{ route('purchases.orders.index') }}" class="btn btn-success">عرض</a>
                </div>
            </div>
        </div>

        <!-- استلام البضائع -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-box-open fa-3x mb-3 text-info"></i>
                    <h5 class="card-title">استلام البضائع</h5>
                    <p class="card-text">تسجيل استلام المشتريات</p>
                    <a href="{{ route('purchases.receipts.index') }}" class="btn btn-info">عرض</a>
                </div>
            </div>
        </div>

        <!-- فواتير الموردين -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">فواتير الموردين</h5>
                    <p class="card-text">إدارة فواتير المشتريات</p>
                    <a href="{{ route('purchases.invoices.index') }}" class="btn btn-warning">عرض</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- التقارير -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">التقارير</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('purchases.reports.suppliers') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-chart-bar"></i> تقرير الموردين
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('purchases.reports.orders') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chart-line"></i> تقرير أوامر الشراء
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('purchases.reports.summary') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-pie"></i> ملخص المشتريات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
