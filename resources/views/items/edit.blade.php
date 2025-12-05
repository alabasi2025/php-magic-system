@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">تعديل الصنف: {{ $item->name }}</div>
                <div class="card-body">
                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('items.form', ['item' => $item])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
