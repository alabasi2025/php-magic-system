@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-600 via-indigo-700 to-blue-700 flex items-center justify-center px-4">
    <div class="bg-white bg-opacity-90 rounded-3xl shadow-2xl w-full max-w-lg p-10">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 flex items-center justify-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2v4c0 1.105 1.343 2 3 2s3-.895 3-2v-4c0-1.105-1.343-2-3-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c4.418 0 8-1.79 8-4v-4c0-2.21-3.582-4-8-4s-8 1.79-8 4v4c0 2.21 3.582 4 8 4z" />
            </svg>
            <span>إنشاء صندوق جديد</span>
        </h2>

        <form action="{{ route('cash-boxes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-gray-700 font-semibold mb-2 flex items-center space-x-2 rtl:space-x-reverse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
                    </svg>
                    <span>اسم الصندوق</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300 text-gray-900 font-medium" placeholder="مثال: صندوق المبيعات">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="initial_balance" class="block text-gray-700 font-semibold mb-2 flex items-center space-x-2 rtl:space-x-reverse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2v4c0 1.105 1.343 2 3 2s3-.895 3-2v-4c0-1.105-1.343-2-3-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c4.418 0 8-1.79 8-4v-4c0-2.21-3.582-4-8-4s-8 1.79-8 4v4c0 2.21 3.582 4 8 4z" />
                    </svg>
                    <span>الرصيد الابتدائي</span>
                </label>
                <input type="number" step="0.01" min="0" name="initial_balance" id="initial_balance" value="{{ old('initial_balance') ?? 0 }}" required
                    class="w-full px-4 py-3 rounded-xl border border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300 text-gray-900 font-medium" placeholder="مثال: 1000.00">
                @error('initial_balance')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-gray-700 font-semibold mb-2 flex items-center space-x-2 rtl:space-x-reverse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6" />
                    </svg>
                    <span>الوصف (اختياري)</span>
                </label>
                <textarea name="description" id="description" rows="3" placeholder="وصف إضافي عن الصندوق"
                    class="w-full px-4 py-3 rounded-xl border border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300 text-gray-900 font-medium resize-none">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full flex justify-center items-center gap-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-extrabold py-3 rounded-3xl shadow-lg hover:from-purple-600 hover:to-indigo-700 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                إنشاء الصندوق
            </button>
        </form>
    </div>
</div>
@endsection