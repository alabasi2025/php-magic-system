@extends('layouts.app')

@section('title', 'تعديل بيانات المورد: ' . $supplier->name)

@section('content')
<div class="container">
    <h1>تعديل بيانات المورد: {{ $supplier->name }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        @include('suppliers._form', ['supplier' => $supplier])
        <button type="submit" class="btn btn-success">تحديث المورد</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
