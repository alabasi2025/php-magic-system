@extends('layouts.app')

@section('title', 'إنشاء إذن إدخال')

@section('content')
<div class="container">
    <h1>إنشاء إذن إدخال جديد</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock_ins.store') }}" method="POST">
                @csrf
                
                @include('stock_ins.form_fields')

                <button type="submit" class="btn btn-success mt-4">حفظ إذن الإدخال</button>
                <a href="{{ route('stock_ins.index') }}" class="btn btn-secondary mt-4">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- يتم تضمين السكريبت من form_fields.blade.php --}}
@endpush
