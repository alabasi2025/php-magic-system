@php
    $hasChildren = $account->children->count() > 0;
    $isExpanded = in_array($account->id, $expandedNodes);
    $indent = $level * 24; // 24px لكل مستوى
@endphp

<div class="account-node" style="margin-right: {{ $indent }}px;">
    <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition group">
        {{-- Toggle Button --}}
        @if($hasChildren)
            <button wire:click="toggleNode({{ $account->id }})" 
                    class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded hover:bg-gray-200 transition">
                @if($isExpanded)
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            </button>
        @else
            <div class="w-6"></div>
        @endif

        {{-- Account Icon --}}
        <div class="flex-shrink-0">
            @if($account->account_level === 'parent')
                <span class="text-2xl">📁</span>
            @else
                <span class="text-2xl">📄</span>
            @endif
        </div>

        {{-- Account Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="font-mono text-sm font-semibold text-blue-600">{{ $account->account_code }}</span>
                <span class="text-gray-800 font-medium">{{ $account->account_name_ar }}</span>
                
                {{-- Badges --}}
                @if($account->account_level === 'parent')
                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">رئيسي</span>
                @else
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">فرعي</span>
                @endif

                @if($account->analytical_type)
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                        {{ $account->analytical_type }}
                    </span>
                @endif
            </div>
            
            <div class="text-xs text-gray-500 mt-1">
                {{ $account->account_name_en }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
            <a href="{{ route('chart-of-accounts.show', $account->id) }}" 
               class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                عرض
            </a>
        </div>
    </div>

    {{-- Children --}}
    @if($hasChildren && $isExpanded)
        <div class="mt-1">
            @foreach($account->children as $child)
                @include('livewire.partials.account-tree-node', ['account' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
