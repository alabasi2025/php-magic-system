@extends('layouts.app')

@section('title', 'إضافة مورد جديد')

@section('content')
<div class="container">
    <h1>إضافة مورد جديد</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        @include('suppliers._form', ['supplier' => new \App\Models\Supplier()])
        <button type="submit" class="btn btn-success">حفظ المورد</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
