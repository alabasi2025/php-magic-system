@extends('layouts.app')
@section('title', 'إعدادات محاسبة الشراكات')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">إعدادات محاسبة الشراكات</h1>

        <div class="space-y-6">
            <!-- الإعدادات العامة -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">الإعدادات العامة</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">تفعيل الإشعارات</h3>
                            <p class="text-sm text-gray-600">إرسال إشعارات عند إضافة إيرادات أو مصروفات</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">الموافقة على التوزيع</h3>
                            <p class="text-sm text-gray-600">يتطلب موافقة قبل توزيع الأرباح</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- إعدادات العملة -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">إعدادات العملة</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">العملة الافتراضية</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>ريال سعودي (SAR)</option>
                            <option>دولار أمريكي (USD)</option>
                            <option>يورو (EUR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد الخانات العشرية</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- إعدادات التقارير -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">إعدادات التقارير</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تنسيق التقارير</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>PDF</option>
                            <option>Excel</option>
                            <option>كلاهما</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800">إرسال التقارير تلقائياً</h3>
                            <p class="text-sm text-gray-600">إرسال التقارير الشهرية للشركاء عبر البريد</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="flex justify-end gap-4">
                <button onclick="alert('تم الإلغاء')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </button>
                <button onclick="alert('تم حفظ الإعدادات')" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    حفظ الإعدادات
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
