{{-- ... بعد نموذج تعديل القيد الرئيسي ... --}}

@include('journal_entries.partials.attachments', [
    'journalEntryId' => $journalEntry->id,
    'isEditable' => true
])