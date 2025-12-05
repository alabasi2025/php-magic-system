@extends('layouts.app')

@section('title', 'شاشة التقارير')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">اختيار التقرير</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('reports.generate') }}">
                        <div class="form-group mb-3">
                            <label for="report_type">نوع التقرير:</label>
                            <select name="report_type" id="report_type" class="form-control" required>
                                <option value="">-- اختر نوع التقرير --</option>
                                @foreach ($reportTypes as $key => $title)
                                    <option value="{{ $key }}" {{ old('report_type') == $key ? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">تاريخ البدء (لبعض التقارير):</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date">تاريخ الانتهاء (لبعض التقارير):</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="item_id">الصنف (اختياري لحركة الأصناف):</label>
                                    {{-- يجب استبدال هذا بحقل اختيار ديناميكي للأصناف --}}
                                    <input type="number" name="item_id" id="item_id" class="form-control" placeholder="معرف الصنف" value="{{ old('item_id') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="period_days">فترة الركود بالأيام (للأصناف الراكدة):</label>
                                    <input type="number" name="period_days" id="period_days" class="form-control" value="{{ old('period_days', 90) }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">توليد التقرير</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
