{{-- ... بعد نموذج إنشاء القيد الرئيسي ... --}}

@if (isset($journalEntry) && $journalEntry->exists)
    {{-- يتم عرض قسم المرفقات فقط بعد إنشاء القيد لأول مرة --}}
    @include('journal_entries.partials.attachments', [
        'journalEntryId' => $journalEntry->id,
        'isEditable' => true
    ])
@else
    <div class="alert alert-info">
        يمكنك إضافة المرفقات بعد حفظ قيد اليومية لأول مرة.
    </div>
@endif

{{-- ... تأكد من وجود وسم <meta name="csrf-token" content="{{ csrf_token() }}"> في رأس الصفحة --}}