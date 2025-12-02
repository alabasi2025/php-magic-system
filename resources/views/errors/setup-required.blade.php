@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        إعداد مطلوب - {{ $title ?? 'النظام' }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle"></i>
                            {{ $message ?? 'النظام غير جاهز بعد' }}
                        </h5>
                        <hr>
                        <p class="mb-0">
                            {{ $instructions ?? 'يرجى إكمال الإعداد الأولي للنظام.' }}
                        </p>
                    </div>

                    @if(isset($error) && config('app.debug'))
                        <div class="alert alert-danger mt-3" role="alert">
                            <h6 class="alert-heading">تفاصيل الخطأ (Debug Mode):</h6>
                            <pre class="mb-0"><code>{{ $error }}</code></pre>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>الخطوات المطلوبة:</h5>
                        <ol>
                            <li>انتقل إلى <a href="/developer" class="btn btn-sm btn-primary"><i class="fas fa-tools"></i> نظام المطور</a></li>
                            <li>اختر <strong>Migrations</strong> من القائمة</li>
                            <li>انقر على زر <strong>"تشغيل Migrations"</strong></li>
                            <li>بعد اكتمال العملية، ارجع إلى هذه الصفحة</li>
                        </ol>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            العودة للخلف
                        </a>
                        <a href="/developer" class="btn btn-primary">
                            <i class="fas fa-tools"></i>
                            نظام المطور
                        </a>
                        <a href="{{ url()->current() }}" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                            إعادة المحاولة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
