{{-- ... بعد عرض تفاصيل القيد الرئيسي ... --}}

@include('journal_entries.partials.attachments', [
    'journalEntryId' => $journalEntry->id,
    'isEditable' => false // للعرض فقط، لا يمكن الرفع أو الحذف
])