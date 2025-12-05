@extends('layouts.app')

@section('title', 'تعديل المخزن: ' . $warehouse->name)

@section('content')
<div class="container">
    <h1>تعديل المخزن: {{ $warehouse->name }}</h1>
    <div class="card">
        <div class="card-body">
            {{-- تضمين النموذج الجزئي --}}
            @include('warehouses._form', ['warehouse' => $warehouse, 'managers' => $managers])
        </div>
    </div>
</div>
@endsection
