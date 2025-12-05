@extends('layouts.app')

@section('title', 'تعديل بيانات العميل: ' . $customer->name)

@section('content')
<div class="container">
    <h1>تعديل بيانات العميل: {{ $customer->name }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        @include('customers._form', ['customer' => $customer])
        <button type="submit" class="btn btn-success">تحديث العميل</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
