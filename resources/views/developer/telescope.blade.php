@extends("layouts.app")

@section("content")
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Laravel Telescope</h1>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
        <p class="text-gray-700 dark:text-gray-300">Laravel Telescope is a debugging companion for Laravel development. It provides insight into the requests coming into your application, exceptions, log entries, database queries, queued jobs, mail, notifications, cache operations, scheduled tasks, variable dumps and more.</p>
        <p class="text-gray-700 dark:text-gray-300 mt-4">To access Telescope, visit <a href="/telescope" class="text-blue-500 hover:text-blue-700">/telescope</a></p>
    </div>
</div>
@endsection
