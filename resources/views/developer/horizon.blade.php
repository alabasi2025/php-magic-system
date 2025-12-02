@extends("layouts.app")

@section("content")
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Laravel Horizon</h1>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
        <p class="text-gray-700 dark:text-gray-300">Laravel Horizon provides a beautiful dashboard and code-driven configuration for your Laravel Redis queues. Horizon allows you to easily monitor key metrics of your queue system, such as job throughput, runtime, and job failures.</p>
        <p class="text-gray-700 dark:text-gray-300 mt-4">To access Horizon, visit <a href="/horizon" class="text-blue-500 hover:text-blue-700">/horizon</a></p>
    </div>
</div>
@endsection
