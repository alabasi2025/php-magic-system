@extends('layouts.app')

@section('title', $reportTitle)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $reportTitle }}</span>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">رجوع لشاشة التقارير</a>
                </div>

                <div class="card-body">
                    {{-- عرض الفلاتر المستخدمة --}}
                    <div class="alert alert-info">
                        <strong>الفلاتر:</strong>
                        @foreach ($filters as $key => $value)
                            @if ($value)
                                <span>{{ $key }}: {{ $value }}</span>@if (!$loop->last) | @endif
                            @endif
                        @endforeach
                    </div>

                    {{-- عرض نتائج التقرير بناءً على النوع --}}
                    @switch($reportType)
                        @case('balance')
                            @include('reports.partials.balance_table', ['items' => $data])
                            @break

                        @case('movement')
                            @include('reports.partials.movement_table', ['transactions' => $data])
                            @break

                        @case('valuation')
                            @include('reports.partials.valuation_summary', ['total_value' => $data['total_value']])
                            @break

                        @case('min_stock')
                            @include('reports.partials.min_stock_table', ['items' => $data])
                            @break

                        @case('slow_moving')
                            @include('reports.partials.slow_moving_table', ['items' => $data])
                            @break

                        @case('active')
                            @include('reports.partials.active_table', ['movements' => $data])
                            @break

                        @case('purchases')
                            @include('reports.partials.purchases_table', ['purchases' => $data])
                            @break

                        @case('sales')
                            @include('reports.partials.sales_table', ['sales' => $data])
                            @break

                        @default
                            <p class="text-danger">لا توجد بيانات لعرضها أو نوع التقرير غير مدعوم.</p>
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
