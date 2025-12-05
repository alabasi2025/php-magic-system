@extends('layouts.app')

@section('title', 'إضافة عميل جديد')

@section('content')
<div class="container">
    <h1>إضافة عميل جديد</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        @include('customers._form', ['customer' => new \App\Models\Customer()])
        <button type="submit" class="btn btn-success">حفظ العميل</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
