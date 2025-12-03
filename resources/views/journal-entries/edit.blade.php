@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">تعديل القيد المحاسبي</h1>
    <form action="{{ route('journal-entries.update', $journalEntry->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="date" class="form-label">التاريخ</label>
            <input type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $journalEntry->date->format('Y-m-d')) }}" required>
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $journalEntry->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h4>الحركات المحاسبية</h4>
        <div id="entries-container">
            @foreach(old('entries', $journalEntry->entries->toArray()) as $index => $entry)
            <div class="row g-3 align-items-end entry-row mb-3">
                <div class="col-md-4">
                    <label for="entries[{{ $index }}][account_id]" class="form-label">الحساب</label>
                    <select name="entries[{{ $index }}][account_id]" class="form-select @error('entries.'.$index.'.account_id') is-invalid @enderror" required>
                        <option value="">اختر الحساب</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ (old('entries.'.$index.'.account_id', $entry['account_id'] ?? '') == $account->id) ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('entries.'.$index.'.account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="entries[{{ $index }}][debit]" class="form-label">مدين</label>
                    <input type="number" step="0.01" min="0" name="entries[{{ $index }}][debit]" class="form-control @error('entries.'.$index.'.debit') is-invalid @enderror" value="{{ old('entries.'.$index.'.debit', $entry['debit'] ?? '') }}" required>
                    @error('entries.'.$index.'.debit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="entries[{{ $index }}][credit]" class="form-label">دائن</label>
                    <input type="number" step="0.01" min="0" name="entries[{{ $index }}][credit]" class="form-control @error('entries.'.$index.'.credit') is-invalid @enderror" value="{{ old('entries.'.$index.'.credit', $entry['credit'] ?? '') }}" required>
                    @error('entries.'.$index.'.credit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-entry-btn">حذف</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" id="add-entry-btn" class="btn btn-secondary mb-4">إضافة حركة</button>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary">إلغاء</a>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let entriesContainer = document.getElementById('entries-container');
    let addEntryBtn = document.getElementById('add-entry-btn');

    function createEntryRow(index = null) {
        let idx = index !== null ? index : entriesContainer.children.length;
        let div = document.createElement('div');
        div.classList.add('row', 'g-3', 'align-items-end', 'entry-row', 'mb-3');

        div.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">الحساب</label>
                <select name="entries[${idx}][account_id]" class="form-select" required>
                    <option value="">اختر الحساب</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">مدين</label>
                <input type="number" step="0.01" min="0" name="entries[${idx}][debit]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">دائن</label>
                <input type="number" step="0.01" min="0" name="entries[${idx}][credit]" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-entry-btn">حذف</button>
            </div>
        `;
        return div;
    }

    addEntryBtn.addEventListener('click', function () {
        let newEntry = createEntryRow();
        entriesContainer.appendChild(newEntry);
        updateEntryIndices();
    });

    entriesContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-entry-btn')) {
            e.target.closest('.entry-row').remove();
            updateEntryIndices();
        }
    });

    function updateEntryIndices() {
        let rows = entriesContainer.querySelectorAll('.entry-row');
        rows.forEach((row, index) => {
            let selects = row.querySelectorAll('select');
            selects.forEach(select => {
                let name = select.getAttribute('name');
                let newName = name.replace(/entries\[\d+\]/, `entries[${index}]`);
                select.setAttribute('name', newName);
            });
            let inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                let name = input.getAttribute('name');
                let newName = name.replace(/entries\[\d+\]/, `entries[${index}]`);
                input.setAttribute('name', newName);
            });
        });
    }
});
</script>
@endpush
@endsection