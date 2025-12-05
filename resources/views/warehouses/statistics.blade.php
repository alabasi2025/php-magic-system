@extends('layouts.app')

@section('title', 'إحصائيات المخازن')

@section('content')
<div class="container">
    <h1>إحصائيات المخازن</h1>

    <div class="row">
        {{-- إجمالي عدد المخازن --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">إجمالي المخازن</h5>
                    <p class="card-text fs-3">{{ $statistics['total_warehouses'] }}</p>
                </div>
            </div>
        </div>

        {{-- المخازن النشطة --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">المخازن النشطة</h5>
                    <p class="card-text fs-3">{{ $statistics['active_warehouses'] }}</p>
                </div>
            </div>
        </div>

        {{-- المخازن المعطلة --}}
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">المخازن المعطلة</h5>
                    <p class="card-text fs-3">{{ $statistics['inactive_warehouses'] }}</p>
                </div>
            </div>
        </div>

        {{-- إجمالي السعة التخزينية --}}
        <div class="col-md-6 mb-4">
            <div class="card text-dark bg-light">
                <div class="card-body">
                    <h5 class="card-title">إجمالي السعة التخزينية</h5>
                    <p class="card-text fs-3">{{ number_format($statistics['total_capacity']) }}</p>
                </div>
            </div>
        </div>

        {{-- إجمالي قيمة المخزون --}}
        <div class="col-md-6 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">إجمالي قيمة المخزون</h5>
                    <p class="card-text fs-3">{{ number_format($statistics['total_stock_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">العودة للقائمة</a>
</div>
@endsection
