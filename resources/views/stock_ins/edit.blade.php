@extends('layouts.app')

@section('title', 'تعديل إذن إدخال: ' . $stockIn->number)

@section('content')
<div class="container">
    <h1>تعديل إذن إدخال: {{ $stockIn->number }}</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock_ins.update', $stockIn) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('stock_ins.form_fields', ['stockIn' => $stockIn])

                <button type="submit" class="btn btn-success mt-4">تحديث إذن الإدخال</button>
                <a href="{{ route('stock_ins.show', $stockIn) }}" class="btn btn-secondary mt-4">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- يتم تضمين السكريبت من form_fields.blade.php --}}
@endpush
