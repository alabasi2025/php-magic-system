@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-6">⚖️ ميزان المراجعة</h1>
    
    @include('reports.components.filters')
    
    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
        <div class="overflow-x-auto">
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b border-white/20">
                        <th class="text-right py-3 px-4">كود الحساب</th>
                        <th class="text-right py-3 px-4">اسم الحساب</th>
                        <th class="text-right py-3 px-4">مدين</th>
                        <th class="text-right py-3 px-4">دائن</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['data'] ?? [] as $row)
                    <tr class="border-b border-white/10 hover:bg-white/5">
                        <td class="py-3 px-4">{{ $row['account_code'] }}</td>
                        <td class="py-3 px-4">{{ $row['account_name'] }}</td>
                        <td class="py-3 px-4">{{ number_format($row['closing_debit'], 2) }}</td>
                        <td class="py-3 px-4">{{ number_format($row['closing_credit'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">لا توجد بيانات</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-white/40 font-bold">
                        <td colspan="2" class="py-3 px-4">الإجمالي</td>
                        <td class="py-3 px-4">{{ number_format($data['summary']['total_closing_debit'] ?? 0, 2) }}</td>
                        <td class="py-3 px-4">{{ number_format($data['summary']['total_closing_credit'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
