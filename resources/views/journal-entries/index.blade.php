🤖 جاري توليد الكود بذكاء...
📝 المهمة: إنشاء واجهة index للقيود المحاسبية بتصميم احترافي مع جدول تفاعلي وفلاتر وبحث
📁 المسار: /home/ubuntu/php-magic-system/resources/views/journal-entries/index.blade.php
✅ تم توليد الكود بنجاح!
{"status": "success", "file_path": "/home/ubuntu/php-magic-system/resources/views/journal-entries/index.blade.php", "task": "\u0625\u0646\u0634\u0627\u0621 \u0648\u0627\u062c\u0647\u0629 index \u0644\u0644\u0642\u064a\u0648\u062f \u0627\u0644\u0645\u062d\u0627\u0633\u0628\u064a\u0629 \u0628\u062a\u0635\u0645\u064a\u0645 \u0627\u062d\u062a\u0631\u0627\u0641\u064a \u0645\u0639 \u062c\u062f\u0648\u0644 \u062a\u0641\u0627\u0639\u0644\u064a \u0648\u0641\u0644\u0627\u062a\u0631 \u0648\u0628\u062d\u062b"}
</th>
                    <th>المبلغ</th>
                    <th>الحساب المدين</th>
                    <th>الحساب الدائن</th>
                    <th>الحالة</th>
                </tr>
                <tr class="filter-row">
                    <th><input type="text" placeholder="بحث برقم القيد" /></th>
                    <th><input type="date" /></th>
                    <th><input type="text" placeholder="بحث بالوصف" /></th>
                    <th>
                        <input type="number" placeholder="min" style="width:48%; display:inline-block;" step="0.01" />
                        <input type="number" placeholder="max" style="width:48%; display:inline-block;" step="0.01" />
                    </th>
                    <th><input type="text" placeholder="بحث بالحساب المدين" /></th>
                    <th><input type="text" placeholder="بحث بالحساب الدائن" /></th>
                    <th>
                        <select>
                            <option value="">الكل</option>
                            <option value="مفتوح">مفتوح</option>
                            <option value="مغلق">مغلق</option>
                            <option value="معلق">معلق</option>
                        </select>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->entry_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ number_format($entry->amount, 2) }}</td>
                    <td>{{ $entry->debit_account }}</td>
                    <td>{{ $entry->credit_account }}</td>
                    <td>
                        @php
                            $statusClass = match ($entry->status) {
                                'مفتوح' => 'badge bg-success',
                                'مغلق' => 'badge bg-secondary',
                                'معلق' => 'badge bg-warning text-dark',
                                default => 'badge bg-light text-dark',
                            };
                        @endphp
                        <span class="{{ $statusClass }}">{{ $entry->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>رقم القيد</th>
                    <th>التاريخ</th>
                    <th>الوصف</th>
                    <th>المبلغ</th>
                    <th>الحساب المدين</th>
                    <th>الحساب الدائن</th>
                    <th>الحالة</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#journalEntriesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
            },
            orderCellsTop: true,
            fixedHeader: true,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50, 100],
            initComplete: function () {
                const api = this.api();

                // For each column in the filter row
                api.columns().every(function (index) {
                    const column = this;
                    const headerCell = $('.filter-row th').eq(index);
                    const input = headerCell.find('input, select');

                    if (input.length > 0) {
                        if (input.is('input[type="text"], input[type="date"]')) {
                            input.on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }
                        else if (input.is('select')) {
                            input.on('change', function() {
                                column.search(this.value).draw();
                            });
                        }
                        else if (input.length === 2 && input.eq(0).attr('placeholder') === 'min') {
                            // Range filter for amount
                            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                                const min = parseFloat($('.filter-row th').eq(3).find('input').eq(0).val(), 10);
                                const max = parseFloat($('.filter-row th').eq(3).find('input').eq(1).val(), 10);
                                const amount = parseFloat(data[3].replace(/,/g, '')) || 0; // Use column 3 for amount

                                if ((isNaN(min) && isNaN(max)) ||
                                    (isNaN(min) && amount <= max) ||
                                    (min <= amount && isNaN(max)) ||
                                    (min <= amount && amount <= max)) {
                                    return true;
                                }
                                return false;
                            });
                            // Trigger table draw on input change
                            $('.filter-row th').eq(3).find('input').on('keyup change', function() {
                                table.draw();
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endsection