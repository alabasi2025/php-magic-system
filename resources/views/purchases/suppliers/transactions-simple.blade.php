@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>معاملات المورد: {{ $supplier->name ?? 'غير محدد' }}</h1>
    
    <p>ID: {{ $supplier->id ?? 'N/A' }}</p>
    <p>الرصيد: {{ $supplier->balance ?? 0 }}</p>
    
    <hr>
    
    <h3>المعاملات</h3>
    @if(empty($transactions))
        <p>لا توجد معاملات</p>
    @else
        <p>عدد المعاملات: {{ count($transactions) }}</p>
    @endif
    
    <hr>
    
    <a href="{{ route('purchases.suppliers.index') }}" class="btn btn-secondary">العودة</a>
</div>
@endsection
