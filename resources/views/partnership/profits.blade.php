@extends('layouts.app')
@section('title', 'حساب الأرباح')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">حساب وتوزيع الأرباح</h1>

        <!-- نموذج حساب الأرباح -->
        <div class="bg-purple-50 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">حساب أرباح جديدة</h2>
            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المحطة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>اختر المحطة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>اختر المشروع</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" onclick="alert('حساب الأرباح')" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg">
                        حساب الأرباح
                    </button>
                </div>
            </form>
        </div>

        <!-- نتيجة الحساب -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-100 rounded-lg p-4 text-center">
                <p class="text-green-600 text-sm">إجمالي الإيرادات</p>
                <h3 class="text-2xl font-bold text-gray-800">0 ريال</h3>
            </div>
            <div class="bg-red-100 rounded-lg p-4 text-center">
                <p class="text-red-600 text-sm">إجمالي المصروفات</p>
                <h3 class="text-2xl font-bold text-gray-800">0 ريال</h3>
            </div>
            <div class="bg-purple-100 rounded-lg p-4 text-center">
                <p class="text-purple-600 text-sm">صافي الربح</p>
                <h3 class="text-2xl font-bold text-gray-800">0 ريال</h3>
            </div>
            <div class="bg-blue-100 rounded-lg p-4 text-center">
                <p class="text-blue-600 text-sm">عدد الشركاء</p>
                <h3 class="text-2xl font-bold text-gray-800">0</h3>
            </div>
        </div>

        <!-- جدول توزيع الأرباح -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">توزيع الأرباح على الشركاء</h2>
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الشريك</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">نسبة الملكية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">حصة الربح</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            قم بحساب الأرباح أولاً لعرض التوزيع
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
