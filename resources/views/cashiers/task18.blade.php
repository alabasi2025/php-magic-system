@extends('layouts.app')

@section('title', 'نظام الصرافين - المهمة 18')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">إدارة الصرافين - المهمة 18: إعدادات إضافية</h1>

        <!--
            Task 2058: Frontend - نظام الصرافين (Cashiers) - Frontend - Task 18
            المطلوب: إنشاء واجهة أمامية للمهمة 18 ضمن نظام الصرافين.
            بما أن تفاصيل المهمة 18 غير محددة، سنقوم بإنشاء هيكل عام لصفحة إعدادات إضافية
            تستخدم مكونات Livewire أو Blade Components وفقاً لمعمارية الجينات (Gene Architecture).
        -->

        <div class="bg-white shadow-lg rounded-lg p-6">
            <p class="text-gray-600 mb-4">
                هذه الصفحة مخصصة لتنفيذ متطلبات المهمة رقم 18 ضمن نظام الصرافين.
                يُفترض أن تكون هذه الواجهة جزءًا من إعدادات أو وظائف إضافية للنظام.
            </p>

            <!-- مثال على استخدام مكون Livewire أو Blade Component -->
            <div class="border-t border-gray-200 pt-4">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">قسم الإعدادات المتقدمة</h2>
                {{--
                    @livewire('cashiers.task-eighteen-settings')
                    أو
                    <x-cashiers.task-eighteen-form />
                --}}
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <p class="text-gray-500">
                        **ملاحظة للمطور:**
                        يجب استبدال هذا المحتوى بمكون Livewire أو Blade Component
                        يحتوي على منطق الواجهة الأمامية للمهمة 18.
                        على سبيل المثال، قد يكون نموذجًا لإضافة صلاحيات جديدة للصرافين
                        أو إعدادات خاصة بالتحويلات النقدية.
                    </p>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <span class="font-medium text-gray-700">خيار الإعداد الأول</span>
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                            <span class="font-medium text-gray-700">قيمة الإعداد الثاني</span>
                            <input type="text" placeholder="أدخل القيمة" class="form-input rounded-md shadow-sm border-gray-300 w-1/3 text-right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- يمكن إضافة أي سكريبتات خاصة بهذه المهمة هنا -->
    <script>
        // console.log('Cashiers Gene - Task 18 Frontend Loaded.');
    </script>
@endpush