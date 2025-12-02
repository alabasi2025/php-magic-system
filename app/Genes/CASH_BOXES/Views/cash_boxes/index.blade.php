@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showAddModal: false, showDeleteModal: false, boxToDelete: null }">
    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">إدارة الصناديق النقدية</h1>
        <button
            @click="showAddModal = true"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center"
        >
            <svg class="w-5 h-5 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            إضافة صندوق جديد
        </button>
    </header>

    <!-- الإحصائيات العلوية -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- عدد الصناديق -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 ltr:mr-4 rtl:ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">عدد الصناديق</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_boxes'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- إجمالي الأرصدة -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 ltr:mr-4 rtl:ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 truncate">إجمالي الأرصدة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_balance'] ?? 0, 2) }} SAR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الصناديق النقدية -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            اسم الصندوق
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الرصيد الحالي
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العملة
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">الإجراءات</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cashBoxes ?? [] as $box)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $box['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($box['balance'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $box['currency'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('cash_boxes.transactions', $box['id']) }}" class="text-indigo-600 hover:text-indigo-900 ltr:mr-4 rtl:ml-4">عرض المعاملات</a>
                                <a href="{{ route('cash_boxes.edit', $box['id']) }}" class="text-yellow-600 hover:text-yellow-900 ltr:mr-4 rtl:ml-4">تعديل</a>
                                <button
                                    @click="boxToDelete = {{ $box['id'] }}; showDeleteModal = true"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    حذف
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                لا توجد صناديق نقدية مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine.js Modal لإضافة صندوق جديد -->
    <div
        x-show="showAddModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50"
        style="display: none;"
    >
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
             @click.away="showAddModal = false">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">إضافة صندوق نقدي جديد</h3>
            <form action="{{ route('cash_boxes.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">اسم الصندوق</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="initial_balance" class="block text-sm font-medium text-gray-700">الرصيد الأولي</label>
                    <input type="number" step="0.01" name="initial_balance" id="initial_balance" value="0.00" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="currency" class="block text-sm font-medium text-gray-700">العملة</label>
                    <select name="currency" id="currency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="SAR">الريال السعودي (SAR)</option>
                        <option value="USD">الدولار الأمريكي (USD)</option>
                        <!-- يمكن إضافة المزيد من العملات هنا -->
                    </select>
                </div>
                <div class="flex justify-end space-x-reverse space-x-2 mt-6">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        حفظ الصندوق
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine.js Modal لتأكيد الحذف -->
    <div
        x-show="showDeleteModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50"
        style="display: none;"
    >
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
             @click.away="showDeleteModal = false">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">تأكيد الحذف</h3>
            <p class="text-sm text-gray-500">هل أنت متأكد أنك تريد حذف هذا الصندوق النقدي؟ لا يمكن التراجع عن هذا الإجراء.</p>
            <div class="flex justify-end space-x-reverse space-x-2 mt-6">
                <button type="button" @click="showDeleteModal = false; boxToDelete = null" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    إلغاء
                </button>
                <form :action="'{{ url('cash_boxes') }}/' + boxToDelete" method="POST" x-ref="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // يمكن إضافة أي سكريبتات إضافية هنا إذا لزم الأمر
</script>
@endpush
