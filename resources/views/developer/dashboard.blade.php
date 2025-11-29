@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">نظام المطور v2.8.1</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-code"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">PHP Version</span>
                                    <span class="info-box-number">{{ $system_overview['php_version'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fab fa-laravel"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Laravel Version</span>
                                    <span class="info-box-number">{{ $system_overview['laravel_version'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-database"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Database Tables</span>
                                    <span class="info-box-number">{{ $quick_stats['database_tables'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-server"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Environment</span>
                                    <span class="info-box-number">{{ $system_overview['environment'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>الأدوات المتاحة</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <a href="{{ route('developer.artisan.index') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-terminal"></i> أوامر Artisan
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('developer.code-generator.index') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-code"></i> مولد الأكواد
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('developer.database.info') }}" class="btn btn-info btn-block">
                                        <i class="fas fa-database"></i> قاعدة البيانات
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <a href="{{ route('developer.monitor.system-info') }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-chart-line"></i> مراقبة النظام
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('developer.cache.overview') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-memory"></i> الذاكرة المؤقتة
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('developer.logs.index') }}" class="btn btn-dark btn-block">
                                        <i class="fas fa-file-alt"></i> السجلات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
