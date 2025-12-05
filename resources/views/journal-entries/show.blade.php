@extends('layouts.app')

@section('title', 'تفاصيل القيد المحاسبي')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">تفاصيل القيد المحاسبي</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700">
            <div>
                <span class="font-semibold">رقم القيد:</span>
                <span>{{ $journalEntry->id }}</span>
            </div>
            <div>
                <span class="font-semibold">التاريخ:</span>
                <span>{{ $journalEntry->entry_date }}</span>
            </div>
            <div>
                <span class="font-semibold">الوصف:</span>
                <span>{{ $journalEntry->description ?? '-' }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-md">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-6 border-b border-gray-200 text-left">الحساب</th>
                        <th class="py-3 px-6 border-b border-gray-200 text-right">المدين (مدين)</th>
                        <th class="py-3 px-6 border-b border-gray-200 text-right">الدائن (دائن)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $line)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-4 px-6">{{ $line->account->name }}</td>
                        <td class="py-4 px-6 text-right text-green-600 font-semibold">
                            @if($line->debit > 0)
                                {{ number_format($line->debit, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right text-red-600 font-semibold">
                            @if($line->credit > 0)
                                {{ number_format($line->credit, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-semibold text-gray-800">
                    <tr>
                        <td class="py-3 px-6 text-right">الإجمالي</td>
                        <td class="py-3 px-6 text-right text-green-700">
                            {{ number_format($journalEntry->lines->sum('debit'), 2) }}
                        </td>
                        <td class="py-3 px-6 text-right text-red-700">
                            {{ number_format($journalEntry->lines->sum('credit'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('journal-entries.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                العودة إلى القائمة
            </a>
        </div>
    </div>
</div>
@endsection