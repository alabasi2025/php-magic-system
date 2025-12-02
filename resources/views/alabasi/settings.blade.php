@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8" dir="rtl">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-2">إعدادات النظام</h1>

    <div x-data="{ activeTab: 'accounts' }" class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 bg-gray-50 p-4">
            <nav class="-mb-px flex space-x-4 space-x-reverse" aria-label="Tabs">
                <button @click="activeTab = 'accounts'"
                        :class="{ 'border-indigo-500 text-indigo-600 bg-white': activeTab === 'accounts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'accounts' }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition duration-150 ease-in-out rounded-t-lg focus:outline-none">
                    إعدادات الحسابات الوسيطة العامة
                </button>
                <button @click="activeTab = 'funds'"
                        :class="{ 'border-indigo-500 text-indigo-600 bg-white': activeTab === 'funds', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'funds' }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition duration-150 ease-in-out rounded-t-lg focus:outline-none">
                    إعدادات الصناديق
                </button>
                <button @click="activeTab = 'partnerships'"
                        :class="{ 'border-indigo-500 text-indigo-600 bg-white': activeTab === 'partnerships', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'partnerships' }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition duration-150 ease-in-out rounded-t-lg focus:outline-none">
                    إعدادات الشراكات
                </button>
                <button @click="activeTab = 'reports'"
                        :class="{ 'border-indigo-500 text-indigo-600 bg-white': activeTab === 'reports', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reports' }"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition duration-150 ease-in-out rounded-t-lg focus:outline-none">
                    إعدادات التقارير
                </button>
            </nav>
        </div>

        <!-- Tabs Content -->
        <div class="p-6">
            <!-- إعدادات الحسابات الوسيطة العامة -->
            <div x-show="activeTab === 'accounts'" x-transition:enter.duration.500ms>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">إعدادات الحسابات الوسيطة العامة</h2>
                <p class="text-gray-600">هنا يمكنك إدارة الإعدادات العامة للحسابات الوسيطة في النظام.</p>
                {{-- Placeholder for form/content --}}
                <div class="mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <p class="text-indigo-700">نموذج إعدادات الحسابات الوسيطة العامة سيتم إضافته هنا.</p>
                </div>
            </div>

            <!-- إعدادات الصناديق -->
            <div x-show="activeTab === 'funds'" x-transition:enter.duration.500ms>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">إعدادات الصناديق</h2>
                <p class="text-gray-600">إدارة وتكوين الصناديق المختلفة المستخدمة في النظام.</p>
                {{-- Placeholder for form/content --}}
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700">نموذج إعدادات الصناديق سيتم إضافته هنا.</p>
                </div>
            </div>

            <!-- إعدادات الشراكات -->
            <div x-show="activeTab === 'partnerships'" x-transition:enter.duration.500ms>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">إعدادات الشراكات</h2>
                <p class="text-gray-600">تكوين إعدادات الشراكات والجهات المتعاونة.</p>
                {{-- Placeholder for form/content --}}
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-700">نموذج إعدادات الشراكات سيتم إضافته هنا.</p>
                </div>
            </div>

            <!-- إعدادات التقارير -->
            <div x-show="activeTab === 'reports'" x-transition:enter.duration.500ms>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">إعدادات التقارير</h2>
                <p class="text-gray-600">تخصيص خيارات توليد التقارير وتنسيقاتها.</p>
                {{-- Placeholder for form/content --}}
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700">نموذج إعدادات التقارير سيتم إضافته هنا.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
