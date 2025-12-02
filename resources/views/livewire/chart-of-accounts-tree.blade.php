<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">🌳 الشجرة الهرمية لدليل الحسابات</h2>
        <p class="text-gray-600">عرض تفاعلي للبنية الهرمية لدليل الحسابات</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- البحث --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🔍 بحث</label>
                <input type="text" wire:model.live="search" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="رقم أو اسم الحساب...">
            </div>

            {{-- الوحدة --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">🏢 الوحدة</label>
                <select wire:model.live="unit_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">جميع الوحدات</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- نوع الحساب --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📊 نوع الحساب</label>
                <select wire:model.live="account_type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="assets">أصول</option>
                    <option value="liabilities">خصوم</option>
                    <option value="equity">حقوق ملكية</option>
                    <option value="revenue">إيرادات</option>
                    <option value="expenses">مصروفات</option>
                </select>
            </div>

            {{-- أزرار التحكم --}}
            <div class="flex items-end gap-2">
                <button wire:click="expandAll" 
                        class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    ➕ فتح الكل
                </button>
                <button wire:click="collapseAll" 
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    ➖ إغلاق الكل
                </button>
            </div>
        </div>
    </div>

    {{-- Tree View --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if($accounts->count() > 0)
            <div class="space-y-2">
                @foreach($accounts as $account)
                    @include('livewire.partials.account-tree-node', ['account' => $account, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-4 text-lg font-medium">لا توجد حسابات</p>
                <p class="mt-2">قم بإضافة حساب جديد للبدء</p>
            </div>
        @endif
    </div>
</div>
