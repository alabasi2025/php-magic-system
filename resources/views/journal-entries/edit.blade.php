@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل القيد المحاسبي</h3>
                </div>
                <div class="card-body">
                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="fas fa-exclamation-triangle"></i> يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('journal-entries.update', $journalEntry) }}" id="journalEntryForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Entry Number (Readonly) --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_number">رقم القيد <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="entry_number" 
                                           id="entry_number" 
                                           class="form-control @error('entry_number') is-invalid @enderror" 
                                           value="{{ old('entry_number', $journalEntry->entry_number) }}" 
                                           readonly 
                                           required>
                                    @error('entry_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Entry Date --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="entry_date">تاريخ القيد <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           name="entry_date" 
                                           id="entry_date" 
                                           class="form-control @error('entry_date') is-invalid @enderror" 
                                           value="{{ old('entry_date', $journalEntry->entry_date ? $journalEntry->entry_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                           required>
                                    @error('entry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Reference --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reference">المرجع</label>
                                    <input type="text" 
                                           name="reference" 
                                           id="reference" 
                                           class="form-control @error('reference') is-invalid @enderror" 
                                           value="{{ old('reference', $journalEntry->reference) }}" 
                                           placeholder="رقم المرجع (اختياري)">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">الحالة <span class="text-danger">*</span></label>
                                    <select name="status" 
                                            id="status" 
                                            class="form-control @error('status') is-invalid @enderror" 
                                            required>
                                        <option value="draft" {{ old('status', $journalEntry->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="pending" {{ old('status', $journalEntry->status) == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="approved" {{ old('status', $journalEntry->status) == 'approved' ? 'selected' : '' }}>معتمد</option>
                                        <option value="posted" {{ old('status', $journalEntry->status) == 'posted' ? 'selected' : '' }}>مرحّل</option>
                                        <option value="rejected" {{ old('status', $journalEntry->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">الوصف <span class="text-danger">*</span></label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="3" 
                                              maxlength="500" 
                                              placeholder="أدخل وصف القيد المحاسبي" 
                                              required>{{ old('description', $journalEntry->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Journal Entry Details Table --}}
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="mb-3">تفاصيل القيد</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="entriesTable">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th style="width: 35%;">الحساب <span class="text-danger">*</span></th>
                                                <th style="width: 20%;">مدين (Debit)</th>
                                                <th style="width: 20%;">دائن (Credit)</th>
                                                <th style="width: 20%;">الوصف</th>
                                                <th style="width: 5%;">إجراء</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(old('details', $journalEntry->details->toArray()) as $index => $detail)
                                            <tr class="entry-row">
                                                <td>
                                                    <select name="details[{{ $index }}][account_id]" 
                                                            class="form-control account-select @error('details.'.$index.'.account_id') is-invalid @enderror" 
                                                            required>
                                                        <option value="" disabled>اختر الحساب</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}" 
                                                                    {{ (old('details.'.$index.'.account_id', $detail['account_id'] ?? '') == $account->id) ? 'selected' : '' }}>
                                                                {{ $account->code }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('details.'.$index.'.account_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="details[{{ $index }}][debit]" 
                                                           class="form-control debit-input @error('details.'.$index.'.debit') is-invalid @enderror" 
                                                           min="0" 
                                                           step="0.01" 
                                                           value="{{ old('details.'.$index.'.debit', $detail['debit'] ?? 0) }}" 
                                                           required>
                                                    @error('details.'.$index.'.debit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="details[{{ $index }}][credit]" 
                                                           class="form-control credit-input @error('details.'.$index.'.credit') is-invalid @enderror" 
                                                           min="0" 
                                                           step="0.01" 
                                                           value="{{ old('details.'.$index.'.credit', $detail['credit'] ?? 0) }}" 
                                                           required>
                                                    @error('details.'.$index.'.credit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           name="details[{{ $index }}][description]" 
                                                           class="form-control @error('details.'.$index.'.description') is-invalid @enderror" 
                                                           value="{{ old('details.'.$index.'.description', $detail['description'] ?? '') }}" 
                                                           placeholder="وصف السطر (اختياري)">
                                                    @error('details.'.$index.'.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td class="text-right font-weight-bold">الإجمالي</td>
                                                <td class="text-center font-weight-bold" id="totalDebit">0.00</td>
                                                <td class="text-center font-weight-bold" id="totalCredit">0.00</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-success" id="addRowBtn">
                                        <i class="fas fa-plus"></i> إضافة سطر
                                    </button>
                                </div>

                                <div class="text-right mb-3">
                                    <span id="balanceStatus" class="font-weight-bold text-danger">القيد غير متوازن</span>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">ملاحظات</label>
                                    <textarea name="notes" 
                                              id="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="ملاحظات إضافية (اختياري)">{{ old('notes', $journalEntry->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('journal-entries.show', $journalEntry) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                                <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> العودة إلى القائمة
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const entriesTableBody = document.querySelector('#entriesTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');
        const totalDebitEl = document.getElementById('totalDebit');
        const totalCreditEl = document.getElementById('totalCredit');
        const balanceStatus = document.getElementById('balanceStatus');
        const submitBtn = document.getElementById('submitBtn');

        // Account options HTML for new rows
        const accountOptionsHTML = `
            <option value="" disabled selected>اختر الحساب</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
        `;

        // Update totals and balance status
        function updateTotalsAndBalance() {
            let totalDebit = 0;
            let totalCredit = 0;

            const rows = entriesTableBody.querySelectorAll('tr.entry-row');
            rows.forEach(row => {
                const debitInput = row.querySelector('.debit-input');
                const creditInput = row.querySelector('.credit-input');

                let debit = parseFloat(debitInput.value) || 0;
                let credit = parseFloat(creditInput.value) || 0;

                totalDebit += debit;
                totalCredit += credit;
            });

            totalDebitEl.textContent = totalDebit.toFixed(2);
            totalCreditEl.textContent = totalCredit.toFixed(2);

            // Check balance
            if (totalDebit > 0 && totalDebit === totalCredit) {
                balanceStatus.textContent = 'القيد متوازن ✓';
                balanceStatus.classList.remove('text-danger');
                balanceStatus.classList.add('text-success');
                submitBtn.disabled = false;
            } else {
                balanceStatus.textContent = 'القيد غير متوازن ✗';
                balanceStatus.classList.remove('text-success');
                balanceStatus.classList.add('text-danger');
                submitBtn.disabled = true;
            }
        }

        // Add new row
        addRowBtn.addEventListener('click', function () {
            const rowCount = entriesTableBody.querySelectorAll('tr.entry-row').length;
            const newRow = document.createElement('tr');
            newRow.classList.add('entry-row');
            newRow.innerHTML = `
                <td>
                    <select name="details[${rowCount}][account_id]" class="form-control account-select" required>
                        ${accountOptionsHTML}
                    </select>
                </td>
                <td>
                    <input type="number" name="details[${rowCount}][debit]" class="form-control debit-input" min="0" step="0.01" value="0" required>
                </td>
                <td>
                    <input type="number" name="details[${rowCount}][credit]" class="form-control credit-input" min="0" step="0.01" value="0" required>
                </td>
                <td>
                    <input type="text" name="details[${rowCount}][description]" class="form-control" placeholder="وصف السطر (اختياري)">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                </td>
            `;
            entriesTableBody.appendChild(newRow);
            updateRowIndices();
            updateTotalsAndBalance();
        });

        // Remove row
        entriesTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                const rows = entriesTableBody.querySelectorAll('tr.entry-row');
                if (rows.length > 1) {
                    e.target.closest('.entry-row').remove();
                    updateRowIndices();
                    updateTotalsAndBalance();
                } else {
                    alert('يجب أن يحتوي القيد على سطر واحد على الأقل!');
                }
            }
        });

        // Update row indices after add/remove
        function updateRowIndices() {
            const rows = entriesTableBody.querySelectorAll('tr.entry-row');
            rows.forEach((row, index) => {
                // Update select name
                const select = row.querySelector('select');
                select.setAttribute('name', `details[${index}][account_id]`);

                // Update input names
                const inputs = row.querySelectorAll('input');
                inputs[0].setAttribute('name', `details[${index}][debit]`);
                inputs[1].setAttribute('name', `details[${index}][credit]`);
                inputs[2].setAttribute('name', `details[${index}][description]`);
            });
        }

        // Listen to input changes
        entriesTableBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) {
                updateTotalsAndBalance();
            }
        });

        // Auto-fill opposite field when one is filled
        entriesTableBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('debit-input')) {
                const debitValue = parseFloat(e.target.value) || 0;
                if (debitValue > 0) {
                    const creditInput = e.target.closest('tr').querySelector('.credit-input');
                    creditInput.value = 0;
                }
            } else if (e.target.classList.contains('credit-input')) {
                const creditValue = parseFloat(e.target.value) || 0;
                if (creditValue > 0) {
                    const debitInput = e.target.closest('tr').querySelector('.debit-input');
                    debitInput.value = 0;
                }
            }
        });

        // Initial calculation on page load
        setTimeout(function() {
            updateTotalsAndBalance();
        }, 100);
    });
</script>
@endsection
