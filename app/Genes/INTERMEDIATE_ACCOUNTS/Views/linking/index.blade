@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4" x-data="linkingInterface()">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 text-right">واجهة ربط القبوضات بالمدفوعات</h1>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- قائمة القبوضات (Receipts) -->
        <div class="w-full lg:w-1/2 bg-white shadow-lg rounded-lg p-4">
            <h2 class="text-xl font-semibold mb-4 text-green-600 border-b pb-2 text-right">القبوضات غير المرتبطة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="receipt in unlinkedReceipts" :key="receipt.id">
                            <tr
                                @click="selectReceipt(receipt)"
                                :class="{ 'bg-green-100 border-green-500 border-r-4': selectedReceipt && selectedReceipt.id === receipt.id, 'hover:bg-gray-50 cursor-pointer': true }"
                                draggable="true"
                                @dragstart="handleDragStart($event, 'receipt', receipt)"
                            >
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="receipt.description"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="receipt.amount.toFixed(2)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="receipt.date"></td>
                            </tr>
                        </template>
                        <tr x-show="unlinkedReceipts.length === 0">
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد قبوضات غير مرتبطة.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- قائمة المدفوعات (Payments) -->
        <div class="w-full lg:w-1/2 bg-white shadow-lg rounded-lg p-4">
            <h2 class="text-xl font-semibold mb-4 text-red-600 border-b pb-2 text-right">المدفوعات غير المرتبطة</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="payment in unlinkedPayments" :key="payment.id">
                            <tr
                                @click="selectPayment(payment)"
                                :class="{ 'bg-red-100 border-red-500 border-r-4': selectedPayment && selectedPayment.id === payment.id, 'hover:bg-gray-50 cursor-pointer': true }"
                                draggable="true"
                                @dragstart="handleDragStart($event, 'payment', payment)"
                            >
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="payment.description"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="payment.amount.toFixed(2)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="payment.date"></td>
                            </tr>
                        </template>
                        <tr x-show="unlinkedPayments.length === 0">
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد مدفوعات غير مرتبطة.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- منطقة الربط (Linking Area) -->
    <div
        class="mt-8 p-6 border-2 border-dashed rounded-lg transition-colors duration-200 text-center"
        :class="{
            'border-gray-300 bg-gray-50': !isDragging,
            'border-blue-500 bg-blue-50': isDragging,
            'border-green-500 bg-green-50': selectedReceipt && selectedPayment
        }"
        @dragover.prevent="isDragging = true"
        @dragleave="isDragging = false"
        @drop.prevent="handleDrop($event)"
    >
        <p class="text-lg font-medium" x-text="isDragging ? 'أفلت هنا للربط' : 'اسحب قبضة وادفعها هنا لربطها بمدفوعات'"></p>
        <p class="text-sm text-gray-500 mt-1">أو اختر من الجدولين أعلاه واضغط على زر الربط.</p>

        <div class="mt-4 flex justify-center items-center gap-4">
            <div class="p-3 border rounded-lg w-64 text-right" :class="selectedReceipt ? 'bg-green-100 border-green-500' : 'bg-gray-100 border-gray-300'">
                <p class="text-xs text-gray-600">القبضة المختارة:</p>
                <p class="font-semibold text-gray-800" x-text="selectedReceipt ? selectedReceipt.description + ' (' + selectedReceipt.amount.toFixed(2) + ')' : 'لم يتم اختيار قبضة'"></p>
            </div>
            <span class="text-2xl font-bold text-gray-500">←→</span>
            <div class="p-3 border rounded-lg w-64 text-right" :class="selectedPayment ? 'bg-red-100 border-red-500' : 'bg-gray-100 border-gray-300'">
                <p class="text-xs text-gray-600">المدفوعة المختارة:</p>
                <p class="font-semibold text-gray-800" x-text="selectedPayment ? selectedPayment.description + ' (' + selectedPayment.amount.toFixed(2) + ')' : 'لم يتم اختيار مدفوعة'"></p>
            </div>
        </div>

        <div class="mt-6">
            <label for="link-amount" class="block text-sm font-medium text-gray-700 text-right">مبلغ الربط:</label>
            <input type="number" id="link-amount" x-model.number="linkAmount" step="0.01" min="0.01"
                   :max="maxLinkAmount"
                   class="mt-1 block w-full lg:w-1/3 mx-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center"
                   placeholder="أدخل المبلغ المراد ربطه"
                   :disabled="!selectedReceipt || !selectedPayment">
            <p class="text-xs text-gray-500 mt-1 text-center" x-show="selectedReceipt && selectedPayment">
                الحد الأقصى للربط: <span x-text="maxLinkAmount.toFixed(2)"></span>
            </p>
        </div>

        <button
            @click="linkItems"
            :disabled="!selectedReceipt || !selectedPayment || linkAmount <= 0 || linkAmount > maxLinkAmount"
            class="mt-4 px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            ربط القبضة بالمدفوعة
        </button>
    </div>

    <!-- جدول الروابط (Links Table) -->
    <div class="mt-8 bg-white shadow-lg rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-4 text-indigo-600 border-b pb-2 text-right">الروابط المنشأة</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-right">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">قبضة (ID)</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">مدفوعة (ID)</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ المرتبط</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">إجراء</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="link in links" :key="link.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="link.receipt_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="link.payment_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="link.linked_amount.toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="unlinkItems(link.id)" class="text-red-600 hover:text-red-900 text-sm">إلغاء الربط</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="links.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">لم يتم إنشاء أي روابط بعد.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('linkingInterface', () => ({
            // Mock Data
            allReceipts: [
                { id: 1, amount: 1000, date: '2025-11-01', description: 'قبض من العميل أ', linked_amount: 0 },
                { id: 2, amount: 500, date: '2025-11-05', description: 'قبض من العميل ب', linked_amount: 0 },
                { id: 3, amount: 2000, date: '2025-11-10', description: 'قبض من العميل ج', linked_amount: 0 },
            ],
            allPayments: [
                { id: 101, amount: 800, date: '2025-11-02', description: 'دفع للمورد س', linked_amount: 0 },
                { id: 102, amount: 200, date: '2025-11-06', description: 'دفع للمورد ص', linked_amount: 0 },
                { id: 103, amount: 1500, date: '2025-11-12', description: 'دفع للمورد ع', linked_amount: 0 },
            ],
            links: [],
            selectedReceipt: null,
            selectedPayment: null,
            linkAmount: 0,
            isDragging: false,
            draggedItem: null, // { type: 'receipt'|'payment', data: item }

            init() {
                // Initialize linkAmount to a safe default
                this.$watch('selectedReceipt', () => this.updateLinkAmount());
                this.$watch('selectedPayment', () => this.updateLinkAmount());
            },

            get unlinkedReceipts() {
                return this.allReceipts.filter(r => r.amount > r.linked_amount);
            },

            get unlinkedPayments() {
                return this.allPayments.filter(p => p.amount > p.linked_amount);
            },

            get maxLinkAmount() {
                if (!this.selectedReceipt || !this.selectedPayment) return 0;
                const receiptRemaining = this.selectedReceipt.amount - this.selectedReceipt.linked_amount;
                const paymentRemaining = this.selectedPayment.amount - this.selectedPayment.linked_amount;
                return Math.min(receiptRemaining, paymentRemaining);
            },

            updateLinkAmount() {
                if (this.selectedReceipt && this.selectedPayment) {
                    this.linkAmount = this.maxLinkAmount;
                } else {
                    this.linkAmount = 0;
                }
            },

            selectReceipt(receipt) {
                this.selectedReceipt = receipt;
            },

            selectPayment(payment) {
                this.selectedPayment = payment;
            },

            handleDragStart(event, type, data) {
                this.isDragging = true;
                this.draggedItem = { type, data };
                event.dataTransfer.setData('text/plain', JSON.stringify({ type, id: data.id }));
                event.dataTransfer.effectAllowed = 'link';
            },

            handleDrop(event) {
                this.isDragging = false;
                const data = JSON.parse(event.dataTransfer.getData('text/plain'));

                if (data.type === 'receipt') {
                    const receipt = this.allReceipts.find(r => r.id === data.id);
                    this.selectReceipt(receipt);
                } else if (data.type === 'payment') {
                    const payment = this.allPayments.find(p => p.id === data.id);
                    this.selectPayment(payment);
                }

                // If both are selected after drop, attempt to link
                if (this.selectedReceipt && this.selectedPayment) {
                    this.linkAmount = this.maxLinkAmount; // Set default link amount
                }
            },

            linkItems() {
                if (!this.selectedReceipt || !this.selectedPayment || this.linkAmount <= 0 || this.linkAmount > this.maxLinkAmount) {
                    alert('الرجاء اختيار قبضة ومدفوعة وتحديد مبلغ ربط صحيح.');
                    return;
                }

                const link = {
                    id: Date.now(), // Simple unique ID
                    receipt_id: this.selectedReceipt.id,
                    payment_id: this.selectedPayment.id,
                    linked_amount: this.linkAmount
                };

                // Update mock data
                this.selectedReceipt.linked_amount += this.linkAmount;
                this.selectedPayment.linked_amount += this.linkAmount;

                this.links.push(link);

                // Reset selection if fully linked
                if (this.selectedReceipt.amount <= this.selectedReceipt.linked_amount) {
                    this.selectedReceipt = null;
                }
                if (this.selectedPayment.amount <= this.selectedPayment.linked_amount) {
                    this.selectedPayment = null;
                }

                // If one is still partially linked, keep it selected and update link amount
                if (this.selectedReceipt && this.selectedPayment) {
                    this.updateLinkAmount();
                } else {
                    this.linkAmount = 0;
                }

                alert(`تم ربط مبلغ ${link.linked_amount.toFixed(2)} بنجاح!`);
            },

            unlinkItems(linkId) {
                const index = this.links.findIndex(l => l.id === linkId);
                if (index === -1) return;

                const link = this.links[index];

                // Revert mock data
                const receipt = this.allReceipts.find(r => r.id === link.receipt_id);
                const payment = this.allPayments.find(p => p.id === link.payment_id);

                if (receipt) receipt.linked_amount -= link.linked_amount;
                if (payment) payment.linked_amount -= link.linked_amount;

                // Remove link
                this.links.splice(index, 1);

                // Reset selection if the unlinked item was the one selected
                if (this.selectedReceipt && this.selectedReceipt.id === link.receipt_id) {
                    this.selectedReceipt = receipt;
                }
                if (this.selectedPayment && this.selectedPayment.id === link.payment_id) {
                    this.selectedPayment = payment;
                }

                this.updateLinkAmount();

                alert('تم إلغاء الربط بنجاح.');
            }
        }))
    })
</script>
@endsection
