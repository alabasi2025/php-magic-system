@extends('layouts.app')

@section('title', 'تسويات الشركاء')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">تسويات الشركاء</h1>
        <button
            @click="window.location.href = '/partners/settlements/create'"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            إنشاء تسوية جديدة
        </button>
    </div>

    <div x-data="{
        settlements: [
            { id: 1, partner: 'شركة الأمل', date: '2025-11-01', amount: 5000.00, status: 'مدفوعة' },
            { id: 2, partner: 'مؤسسة النور', date: '2025-11-15', amount: 2500.50, status: 'معلقة' },
            { id: 3, partner: 'الشركة المتحدة', date: '2025-11-20', amount: 750.00, status: 'جزئية' },
            { id: 4, partner: 'شركة الأمل', date: '2025-12-01', amount: 1200.00, status: 'معلقة' },
        ],
        filterStatus: 'الكل',
        get filteredSettlements() {
            if (this.filterStatus === 'الكل') {
                return this.settlements;
            }
            return this.settlements.filter(s => s.status === this.filterStatus);
        },
        getStatusClass(status) {
            switch(status) {
                case 'مدفوعة': return 'bg-green-100 text-green-800';
                case 'معلقة': return 'bg-yellow-100 text-yellow-800';
                case 'جزئية': return 'bg-blue-100 text-blue-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }
    }" class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-start space-x-4 space-x-reverse">
            <label for="status-filter" class="block text-sm font-medium text-gray-700 self-center">تصفية حسب الحالة:</label>
            <select x-model="filterStatus" id="status-filter" class="mt-1 block w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option>الكل</option>
                <option>مدفوعة</option>
                <option>معلقة</option>
                <option>جزئية</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الشركة الشريكة
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ التسوية
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المبلغ
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            حالة الدفع
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">إجراءات</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="settlement in filteredSettlements" :key="settlement.id">
                        <tr>
                            <td x-text="settlement.partner" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"></td>
                            <td x-text="settlement.date" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                            <td x-text="settlement.amount.toFixed(2) + ' ر.س'" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span x-text="settlement.status" :class="getStatusClass(settlement.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">عرض</a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredSettlements.length === 0">
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            لا توجد تسويات مطابقة لمعايير التصفية.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
