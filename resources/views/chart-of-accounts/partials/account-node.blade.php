@php
    $hasChildren = $account->children && $account->children->count() > 0;
    $indent = $level * 30;
@endphp

<div class="account-node" data-account-name="{{ $account->name }}" data-account-code="{{ $account->code }}">
    <!-- Account Row -->
    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors border-r-4" 
         style="margin-right: {{ $indent }}px; border-color: {{ $chartGroup->color ?? '#3B82F6' }};">
        <div class="flex items-center gap-3 flex-1">
            <!-- Toggle Icon -->
            @if($hasChildren)
                <button onclick="toggleAccount({{ $account->id }})" class="text-gray-500 hover:text-gray-700">
                    <i id="icon-{{ $account->id }}" class="fas fa-minus-square text-lg"></i>
                </button>
            @else
                <i class="fas fa-circle text-xs text-gray-300 mr-1"></i>
            @endif

            <!-- Account Info -->
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-800">{{ $account->name }}</span>
                    <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $account->code }}</span>
                    @if($account->type === 'group')
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">مجموعة</span>
                    @else
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">تفصيلي</span>
                    @endif
                    @if(!$account->is_active)
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">غير نشط</span>
                    @endif
                </div>
                @if($account->description)
                    <p class="text-sm text-gray-500 mt-1">{{ $account->description }}</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 no-print">
            @if($account->type === 'group')
                <button onclick="openAddAccountModal({{ $account->id }})" 
                        class="text-indigo-600 hover:text-indigo-800 px-3 py-1 rounded hover:bg-indigo-50 transition-colors"
                        title="إضافة حساب فرعي">
                    <i class="fas fa-plus"></i>
                </button>
            @endif
            <button onclick="editAccount({{ $account->id }})" 
                    class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded hover:bg-blue-50 transition-colors"
                    title="تعديل">
                <i class="fas fa-edit"></i>
            </button>
            <button onclick="deleteAccount({{ $account->id }})" 
                    class="text-red-600 hover:text-red-800 px-3 py-1 rounded hover:bg-red-50 transition-colors"
                    title="حذف">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <!-- Children -->
    @if($hasChildren)
        <div id="children-{{ $account->id }}" class="mt-1">
            @foreach($account->children as $child)
                @include('chart-of-accounts.partials.account-node', ['account' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
