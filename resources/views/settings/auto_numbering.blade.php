@extends('layouts.app')

@section('title', 'إعدادات الترقيم التلقائي')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog"></i> إعدادات الترقيم التلقائي الذكي</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auto-numbering.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>نوع الكيان</label>
                                    <select name="entity_type" class="form-control" required>
                                        <option value="journal_entry">القيود اليومية</option>
                                        <option value="invoice">الفواتير</option>
                                        <option value="receipt">الإيصالات</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>البادئة (Prefix)</label>
                                    <input type="text" name="prefix" class="form-control" placeholder="JE-" value="JE-">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>نمط الترقيم</label>
                                    <select name="pattern" class="form-control" required>
                                        <option value="{PREFIX}{YEAR}-{NUMBER}">JE-2025-0001</option>
                                        <option value="{PREFIX}{YEAR}{MONTH}-{NUMBER}">JE-202512-0001</option>
                                        <option value="{PREFIX}{NUMBER}">JE-0001</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>عدد الأصفار</label>
                                    <input type="number" name="padding" class="form-control" value="4" min="1" max="10" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="reset_yearly" name="reset_yearly" value="1">
                                    <label class="custom-control-label" for="reset_yearly">إعادة تعيين سنوياً</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="reset_monthly" name="reset_monthly" value="1">
                                    <label class="custom-control-label" for="reset_monthly">إعادة تعيين شهرياً</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">تفعيل النظام</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <h5 class="mb-3"><i class="fas fa-list"></i> الإعدادات الحالية</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>نوع الكيان</th>
                                    <th>النمط</th>
                                    <th>الحالة</th>
                                    <th>الرقم الحالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                <tr>
                                    <td>{{ $setting->entity_type }}</td>
                                    <td><code>{{ $setting->pattern }}</code></td>
                                    <td>
                                        @if($setting->is_active)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-secondary">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $setting->current_number }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد إعدادات</td>
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
