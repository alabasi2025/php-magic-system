@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">إضافة صنف جديد</div>
                <div class="card-body">
                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('items.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
