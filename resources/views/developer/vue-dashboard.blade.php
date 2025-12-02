@extends("layouts.app")

@section("content")
<div id="app" class="p-6">
    <system-overview
        php-version="{{ $phpVersion }}"
        laravel-version="{{ $laravelVersion }}"
        database="{{ $database }}"
        environment="{{ $environment }}"
    ></system-overview>

    <artisan-commands></artisan-commands>

    <code-generator></code-generator>
</div>

@vite(["resources/css/app.css", "resources/js/app.js"])
@endsection
