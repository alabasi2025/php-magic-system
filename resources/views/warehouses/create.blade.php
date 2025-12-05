@extends('layouts.app')

@section('title', 'إضافة مخزن جديد')

@section('content')
<div class="container">
    <h1>إضافة مخزن جديد</h1>
    <div class="card">
        <div class="card-body">
            {{-- تضمين النموذج الجزئي --}}
            @include('warehouses._form', ['warehouse' => new \App\Models\Warehouse(), 'managers' => $managers])
        </div>
    </div>
</div>
@endsection
