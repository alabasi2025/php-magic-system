@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">إنشاء قيد محاسبي ذكي</h2>
    <form method="POST" action="{{ route('journal-entries.store') }}" id="journalEntryForm">
        @csrf
        <table class="table table-bordered" id="entriesTable">
            <thead class="thead-light text-center">
                <tr>
                    <th style="width: 40%;">الحساب</th>
                    <th style="width: 25%;">مدين (Debit)</th>
                    <th style="width: 25%;">دائن (Credit)</th>
                    <th style="width: 10%;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr class="entry-row">
                    <td>
                        <select name="accounts[]" class="form-control account-select" required>
                            <option value="" disabled selected>اختر الحساب</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="debits[]" class="form-control debit-input" min="0" step="0.01" value="0" required>
                    </td>
                    <td>
                        <input type="number" name="credits[]" class="form-control credit-input" min="0" step="0.01" value="0" required>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm remove-row" disabled>&times;</button>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right font-weight-bold">الإجمالي</td>
                    <td class="text-center font-weight-bold" id="totalDebit">0.00</td>
                    <td class="text-center font-weight-bold" id="totalCredit">0.00</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="mb-3">
            <button type="button" class="btn btn-success" id="addRowBtn">إضافة سطر</button>
        </div>

        <div class="form-group">
            <label for="description">الوصف</label>
            <textarea name="description" id="description" class="form-control" rows="3" maxlength="500" placeholder="أدخل وصف القيد المحاسبي" required></textarea>
        </div>

        <div class="text-right mb-3">
            <span id="balanceStatus" class="font-weight-bold text-danger">القيد غير متوازن</span>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>حفظ القيد</button>
    </form>
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

            if (totalDebit > 0 && totalDebit === totalCredit) {
                balanceStatus.textContent = 'القيد متوازن';
                balanceStatus.classList.remove('text-danger');
                balanceStatus.classList.add('text-success');
                submitBtn.disabled = false;
            } else {
                balanceStatus.textContent = 'القيد غير متوازن';
                balanceStatus.classList.remove('text-success');
                balanceStatus.classList.add('text-danger');
                submitBtn.disabled = true;
            }
        }

        function createRow() {
            const tr = document.createElement('tr');
            tr.classList.add('entry-row');

            // الحساب
            const tdAccount = document.createElement('td');
            const select = document.createElement('select');
            select.name = 'accounts[]';
            select.classList.add('form-control', 'account-select');
            select.required = true;

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.textContent = 'اختر الحساب';
            select.appendChild(defaultOption);

            @foreach($accounts as $account)
                let option = document.createElement('option');
                option.value = '{{ $account->id }}';
                option.textContent = '{{ $account->name }}';
                select.appendChild(option);
            @endforeach

            tdAccount.appendChild(select);
            tr.appendChild(tdAccount);

            // مدين
            const tdDebit = document.createElement('td');
            const debitInput = document.createElement('input');
            debitInput.type = 'number';
            debitInput.name = 'debits[]';
            debitInput.classList.add('form-control', 'debit-input');
            debitInput.min = '0';
            debitInput.step = '0.01';
            debitInput.value = '0';
            debitInput.required = true;
            tdDebit.appendChild(debitInput);
            tr.appendChild(tdDebit);

            // دائن
            const tdCredit = document.createElement('td');
            const creditInput = document.createElement('input');
            creditInput.type = 'number';
            creditInput.name = 'credits[]';
            creditInput.classList.add('form-control', 'credit-input');
            creditInput.min = '0';
            creditInput.step = '0.01';
            creditInput.value = '0';
            creditInput.required = true;
            tdCredit.appendChild(creditInput);
            tr.appendChild(tdCredit);

            // إجراء
            const tdAction = document.createElement('td');
            tdAction.classList.add('text-center', 'align-middle');
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-row');
            removeBtn.textContent = '×';
            tdAction.appendChild(removeBtn);
            tr.appendChild(tdAction);

            entriesTableBody.appendChild(tr);

            // Enable remove buttons if more than one row
            toggleRemoveButtons();

            attachRowEvents(tr);
        }

        function attachRowEvents(row) {
            const debitInput = row.querySelector('.debit-input');
            const creditInput = row.querySelector('.credit-input');
            const removeBtn = row.querySelector('.remove-row');

            function onInputChange(e) {
                const input = e.target;
                if (input.classList.contains('debit-input')) {
                    if (parseFloat(input.value) > 0) {
                        const creditInput = row.querySelector('.credit-input');
                        if (parseFloat(creditInput.value) > 0) {
                            creditInput.value = '0';
                        }
                    }
                } else if (input.classList.contains('credit-input')) {
                    if (parseFloat(input.value) > 0) {
                        const debitInput = row.querySelector('.debit-input');
                        if (parseFloat(debitInput.value) > 0) {
                            debitInput.value = '0';
                        }
                    }
                }
                updateTotalsAndBalance();
            }

            debitInput.addEventListener('input', onInputChange);
            creditInput.addEventListener('input', onInputChange);

            removeBtn.addEventListener('click', function () {
                row.remove();
                toggleRemoveButtons();
                updateTotalsAndBalance();
            });
        }

        function toggleRemoveButtons() {
            const rows = entriesTableBody.querySelectorAll('tr.entry-row');
            const removeButtons = entriesTableBody.querySelectorAll('.remove-row');
            if (rows.length === 1) {
                removeButtons.forEach(btn => btn.disabled = true);
            } else {
                removeButtons.forEach(btn => btn.disabled = false);
            }
        }

        // Attach events to initial row
        attachRowEvents(entriesTableBody.querySelector('tr.entry-row'));

        addRowBtn.addEventListener('click', function () {
            createRow();
        });

        // Initial totals update
        updateTotalsAndBalance();

        // Prevent form submission if unbalanced
        const form = document.getElementById('journalEntryForm');
        form.addEventListener('submit', function (e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                alert('القيد غير متوازن، الرجاء تعديل القيم لتكون متوازنة قبل الحفظ.');
            }
        });
    });
</script>
@endsection